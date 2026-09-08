<?php
/**
 * send_drip_email.php
 * 邮件 drip 自动化
 * 
 * 逻辑：
 * 1. 找出 email_subscribers 表里 subscribed_products=0 的用户（还没买任何产品）
 * 2. 只发创建超过 3 天的
 * 3. 根据 decision_maker_answers 里的 answers 计算未推荐的 2 个产品
 * 4. 发第 2 封邮件：推荐"你还没试过的" + 折扣码
 * 5. 发完标记 drip_1_sent=1，4 天后再发 drip_2
 * 
 * 使用：每天 10:00 跑一次
 * cron: '0 10 * * *' '/E:/xampp/php/php.exe E:/hermes/scripts/send_drip_email.php'
 */
define('WP_USE_THEMES', false);
require_once 'E:\\xampp\\htdocs\\wp-load.php';
global $wpdb;

// 建表（如果不存在）
$wpdb->query("
CREATE TABLE IF NOT EXISTS {$wpdb->prefix}email_subscribers (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(200) NOT NULL UNIQUE,
  page_source VARCHAR(100) DEFAULT '',
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  subscribed_products TINYINT(1) DEFAULT 0,
  drip_1_sent TINYINT(1) DEFAULT 0,
  drip_1_sent_at DATETIME NULL,
  drip_2_sent TINYINT(1) DEFAULT 0,
  drip_2_sent_at DATETIME NULL,
  INDEX (email),
  INDEX (drip_1_sent, drip_2_sent)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
");

$wpdb->query("
CREATE TABLE IF NOT EXISTS {$wpdb->prefix}decision_maker_answers (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(200) NOT NULL,
  answers LONGTEXT,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  INDEX (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
");

// 给 email_subscribers 加 drip 列（如果还没）
$has_drip1 = $wpdb->get_var("SHOW COLUMNS FROM wp_email_subscribers LIKE 'drip_1_sent'");
if(!$has_drip1){
    $wpdb->query("ALTER TABLE wp_email_subscribers ADD COLUMN drip_1_sent TINYINT(1) DEFAULT 0");
    $wpdb->query("ALTER TABLE wp_email_subscribers ADD COLUMN drip_1_sent_at DATETIME NULL");
    $wpdb->query("ALTER TABLE wp_email_subscribers ADD COLUMN drip_2_sent TINYINT(1) DEFAULT 0");
    $wpdb->query("ALTER TABLE wp_email_subscribers ADD COLUMN drip_2_sent_at DATETIME NULL");
    echo "[OK] 已加 drip 列\n";
}

// 找未发 drip_1 的用户
$pending_drip1 = $wpdb->get_results("
    SELECT * FROM wp_email_subscribers 
    WHERE drip_1_sent = 0 AND subscribed_products = 0 
    AND created_at < DATE_SUB(NOW(), INTERVAL 3 DAY)
    LIMIT 50
");

// 找未发 drip_2 的用户
$pending_drip2 = $wpdb->get_results("
    SELECT * FROM wp_email_subscribers 
    WHERE drip_2_sent = 0 AND drip_1_sent = 1 
    AND drip_1_sent_at < DATE_SUB(NOW(), INTERVAL 4 DAY)
    LIMIT 50
");

$stats = ['drip1_sent' => 0, 'drip2_sent' => 0, 'total' => count($pending_drip1) + count($pending_drip2)];

foreach($pending_drip1 as $sub){
    // 从决策器 answers 计算未推荐产品
    $answers_rows = $wpdb->get_results($wpdb->prepare(
        "SELECT answers FROM wp_decision_maker_answers WHERE email = %s ORDER BY id DESC LIMIT 1",
        $sub->email
    ));
    
    $recommended = [];
    $not_recommended = ['prompt','agent','polish','overseas','tools'];
    
    if(!empty($answers_rows)){
        $answers = json_decode($answers_rows[0]->answers, true);
        // 复用决策器的匹配逻辑
        if(function_exists('ggouai_compute_dm_match')){
            $ranked = ggouai_compute_dm_match($answers);
            foreach($ranked as $r){
                $recommended[] = $r['slug'];
                $not_recommended = array_diff($not_recommended, [$r['slug']]);
            }
        }
    }
    
    // 如果没有决策器数据，随机推 2 个
    if(empty($not_recommended)){
        $not_recommended = array_rand(['xgnhru','dyrvmg','zpjabq','urbhcq','apjvst'], 2);
    }
    
    $body = ggouai_build_drip1_email($sub->email, array_slice($not_recommended, 0, 2));
    $subject = '💡 你可能错过的 2 个 AI 产品（限时折扣）';
    $headers = ['From: ggouai.top <no-reply@ggouai.top>', 'Content-Type: text/html; charset=UTF-8'];
    @wp_mail($sub->email, $subject, $body, $headers);
    
    $wpdb->update('wp_email_subscribers', [
        'drip_1_sent' => 1,
        'drip_1_sent_at' => current_time('mysql'),
    ], ['id' => $sub->id]);
    $stats['drip1_sent']++;
}

foreach($pending_drip2 as $sub){
    $body = ggouai_build_drip2_email($sub->email);
    $subject = '⏰ 你的 30 天退款保证还剩多少天？';
    $headers = ['From: ggouai.top <no-reply@ggouai.top>', 'Content-Type: text/html; charset=UTF-8'];
    @wp_mail($sub->email, $subject, $body, $headers);
    
    $wpdb->update('wp_email_subscribers', [
        'drip_2_sent' => 1,
        'drip_2_sent_at' => current_time('mysql'),
    ], ['id' => $sub->id]);
    $stats['drip2_sent']++;
}

echo "[OK] drip 完成: drip1={$stats['drip1_sent']}, drip2={$stats['drip2_sent']}, 总处理={$stats['total']}\n";

// 报告当前状态
$total_subs = $wpdb->get_var("SELECT COUNT(*) FROM wp_email_subscribers");
$drip1_done = $wpdb->get_var("SELECT COUNT(*) FROM wp_email_subscribers WHERE drip_1_sent=1");
$drip2_done = $wpdb->get_var("SELECT COUNT(*) FROM wp_email_subscribers WHERE drip_2_sent=1");
echo "订阅者总数: $total_subs, drip1 已发: $drip1_done, drip2 已发: $drip2_done\n";

// ======================== 邮件模板 ========================

function ggouai_build_drip1_email($email, $not_recommended_slugs){
    $products = [
        'xgnhru' => ['title'=>'Chinese Prompt Mastery Pack','price'=>'$19','icon'=>'📝','blurb'=>'200+ 即用型中文提示词，覆盖写作/翻译/分析。'],
        'dyrvmg' => ['title'=>'Agent Self-Evolution Guide','price'=>'$17','icon'=>'🤖','blurb'=>'3 天构建一个会自我学习的 AI Agent，附 15 段完整代码。'],
        'zpjabq' => ['title'=>'Chinese-to-English Polish Toolkit','price'=>'$15','icon'=>'✍️','blurb'=>'15 个润色模板，直接复制到 Claude/Gemini，Chinglish 秒变地道英文。'],
        'urbhcq' => ['title'=>'AI Overseas Methodology','price'=>'$14','icon'=>'🌍','blurb'=>'5 条 AI 出海路径 + 决策框架，3 天选定方向。'],
        'apjvst' => ['title'=>'AI Tools Practical Guide','price'=>'$12','icon'=>'🚀','blurb'=>'从 0 到 1 的 AI 赚钱路线图，30 个场景手把手教。'],
    ];
    
    $discount = 'GGOUAI_D20';
    
    $html = '<div style="max-width:600px;margin:0 auto;padding:32px 20px;font-family:-apple-system,sans-serif;background:#fff;color:#333;">';
    $html .= '<div style="background:linear-gradient(135deg,#ff0069 0%,#ff6b9d 100%);color:#fff;padding:32px;border-radius:12px;text-align:center;margin-bottom:24px;">';
    $html .= '<div style="font-size:3em;">💡</div>';
    $html .= '<h1 style="margin:0;font-size:1.8em;">你可能错过了这 2 个</h1>';
    $html .= '<p style="margin:8px 0 0 0;opacity:0.95;">上次决策器只推了 3 个，但这 2 个可能更合适你</p>';
    $html .= '</div>';
    
    foreach($not_recommended_slugs as $slug){
        if(!isset($products[$slug])) continue;
        $p = $products[$slug];
        $html .= '<div style="background:#f9f9fb;padding:20px;border-radius:10px;margin:16px 0;border-left:4px solid #ff0069;">';
        $html .= '<div style="font-size:1.2em;font-weight:bold;margin-bottom:6px;">' . $p['icon'] . ' ' . $p['title'] . '</div>';
        $html .= '<div style="color:#666;margin-bottom:12px;">' . $p['blurb'] . '</div>';
        $html .= '<a href="https://scgkagent.gumroad.com/l/' . $slug . '?utm_source=drip1&utm_medium=email&utm_campaign=' . $slug . '" style="display:inline-block;background:#ff0069;color:#fff;padding:10px 24px;border-radius:8px;text-decoration:none;font-weight:bold;">🛒 试 ' . $p['price'] . ' →</a>';
        $html .= '</div>';
    }
    
    $html .= '<div style="background:#000;color:#fff;padding:20px;border-radius:10px;text-align:center;margin:24px 0;">';
    $html .= '<div style="font-size:1.1em;">🎁 专属 20% 折扣码</div>';
    $html .= '<div style="font-size:2em;font-weight:bold;color:#00e5d1;margin:8px 0;">' . $discount . '</div>';
    $html .= '<div style="font-size:0.85em;opacity:0.85;">在 Gumroad 结账时输入</div>';
    $html .= '</div>';
    
    $html .= '<div style="text-align:center;margin:24px 0;color:#666;font-size:0.9em;">';
    $html .= '所有产品 30 天无理由退款。<br>';
    $html .= '<a href="https://www.ggouai.top/buy-faq/" style="color:#ff0069;">查看购买 FAQ</a>';
    $html .= '</div>';
    
    $html .= '<hr style="border:none;border-top:1px solid #eee;margin:24px 0;">';
    $html .= '<div style="text-align:center;color:#999;font-size:0.8em;">';
    $html .= '你不喜欢这个邮件？<a href="https://www.ggouai.top/contact/?utm_source=drip1" style="color:#ff0069;">点这里退订</a><br>';
    $html .= '— ggouai.top Team';
    $html .= '</div>';
    $html .= '</div>';
    
    return $html;
}

function ggouai_build_drip2_email($email){
    $html = '<div style="max-width:600px;margin:0 auto;padding:32px 20px;font-family:-apple-system,sans-serif;background:#fff;color:#333;">';
    $html .= '<div style="background:linear-gradient(135deg,#000 0%,#333 100%);color:#fff;padding:32px;border-radius:12px;text-align:center;margin-bottom:24px;">';
    $html .= '<div style="font-size:3em;">⏰</div>';
    $html .= '<h1 style="margin:0;font-size:1.8em;">3 天到了</h1>';
    $html .= '<p style="margin:8px 0 0 0;opacity:0.95;">上次推荐的产品，你试了吗？</p>';
    $html .= '</div>';
    
    $html .= '<div style="background:#fff4f9;padding:24px;border-radius:10px;margin:16px 0;border-left:4px solid #ff0069;">';
    $html .= '<h3 style="margin:0 0 8px 0;color:#333;">🎯 还没决定？再来一次</h3>';
    $html .= '<p style="margin:0 0 12px 0;color:#666;">AI 决策器 30 秒个性化推荐，帮你找到最合适的。</p>';
    $html .= '<a href="https://www.ggouai.top/ai-decision-maker/?utm_source=drip2&utm_medium=email" style="display:inline-block;background:#ff0069;color:#fff;padding:12px 28px;border-radius:8px;text-decoration:none;font-weight:bold;">🎯 再试决策器 →</a>';
    $html .= '</div>';
    
    $html .= '<div style="background:#f9f9fb;padding:24px;border-radius:10px;margin:16px 0;">';
    $html .= '<h3 style="margin:0 0 8px 0;color:#333;">💰 想知道值不值？算算 ROI</h3>';
    $html .= '<p style="margin:0 0 12px 0;color:#666;">输入你的时薪，直接算出一年能省多少。</p>';
    $html .= '<a href="https://www.ggouai.top/ai-roi-calculator/?utm_source=drip2&utm_medium=email" style="display:inline-block;background:#00e5d1;color:#000;padding:12px 28px;border-radius:8px;text-decoration:none;font-weight:bold;">💰 试 ROI 计算器 →</a>';
    $html .= '</div>';
    
    $html .= '<div style="background:#000;color:#fff;padding:24px;border-radius:10px;text-align:center;margin:24px 0;">';
    $html .= '<div style="font-size:0.9em;">🎁 5 个产品全清单</div>';
    $html .= '<div style="font-size:1.5em;font-weight:bold;margin:8px 0;">$12-$19 · 终身使用 · 30 天退款</div>';
    $html .= '<a href="https://www.ggouai.top/compare-ai-products/?utm_source=drip2&utm_medium=email" style="display:inline-block;background:#00e5d1;color:#000;padding:12px 24px;border-radius:8px;text-decoration:none;font-weight:bold;margin-top:8px;">对比 5 个产品 →</a>';
    $html .= '</div>';
    
    $html .= '<div style="text-align:center;color:#999;font-size:0.8em;">';
    $html .= '<a href="https://www.ggouai.top/contact/?utm_source=drip2" style="color:#ff0069;">退订</a>';
    $html .= '</div>';
    $html .= '</div>';
    
    return $html;
}
