<?php
/**
 * build_roi_calculator.php
 * AI ROI 计算器 - /ai-roi-calculator/
 * 
 * 思路：让用户输入时薪和每周愿意花的时间，直接算出：
 *   - 不买：每周花 10 小时手动做 X，损失 $200/周
 *   - 买了 Prompt Pack $19：AI 帮你 2 小时完成，节省 8 小时 = $160 每周
 *   - 一年净赚：($160-$19/52)*52 = $8,236
 * 
 * 用户算完之后立刻看到：买这个 $19 = 一年赚 $8000
 * 转化率比决策器更高（数字冲击）
 * 
 * 技术：rewrite rule + heredoc 输出（同决策器）
 */
define('WP_USE_THEMES', false);
require_once 'E:\\xampp\\htdocs\\wp-load.php';

// 直接注册 rewrite + 输出函数到插件（跟决策器一样）
// 因为要注册 rewrite rule，必须先 flush_rewrite_rules
global $wpdb;

// 建一个页面壳，供 search 收录（但实际渲染走插件 rewrite）
$slug = 'ai-roi-calculator';
$existing = get_page_by_path($slug);
$pid = null;
if($existing){
    $pid = $existing->ID;
    echo "[OK] page ID$pid 已存在\n";
}else{
    $pid = wp_insert_post([
        'post_title' => 'AI ROI 计算器 — 看看你能省多少钱',
        'post_name'  => $slug,
        'post_content' => '<!-- 实际内容由插件 rewrite 输出 -->',
        'post_status'  => 'publish',
        'post_type'    => 'page',
        'post_author'  => 1,
        'post_excerpt' => '输入你的时薪和每周花的时间，算算 AI 工具一年能帮你省多少。',
    ], false);
    if(is_wp_error($pid)){
        echo "[FAIL] " . $pid->get_error_message() . "\n";
        exit;
    }
    echo "[OK] 页面 ID$pid 创建\n";
}

// 清 rewrite rules 让插件里 add_rewrite_rule 生效
$wpdb->query("DELETE FROM wp_options WHERE option_name='rewrite_rules'");
flush_rewrite_rules();
echo "[OK] rewrite rules flushed\n";

// 加 meta
$meta = [
    '_rank_math_title' => 'AI ROI 计算器 — 30 秒算出你能省多少',
    '_rank_math_desc' => '输入时薪和每周花的时间，算算买 AI 工具一年能帮你省多少。数字说话。',
    '_rank_math_focus_keyword' => 'ai roi 计算器 免费',
    '_rank_math_og_title' => 'AI ROI 计算器 — 30 秒算出你能省多少',
    '_rank_math_og_desc' => '输入时薪和每周花的时间，算算买 AI 工具一年能帮你省多少。',
    '_rank_math_og_type' => 'website',
    '_rank_math_og_url' => 'https://www.ggouai.top/ai-roi-calculator/',
    '_rank_math_og_image' => 'https://www.ggouai.top/wp-content/uploads/og-covers/hub-cover.png',
    '_rank_math_og_image_width' => '1200',
    '_rank_math_og_image_height' => '630',
];
foreach($meta as $k=>$v){
    $wpdb->delete('wp_postmeta', ['post_id'=>$pid, 'meta_key'=>$k]);
    $wpdb->insert('wp_postmeta', ['post_id'=>$pid, 'meta_key'=>$k, 'meta_value'=>$v]);
}

// 更新 sitemap
$sm = 'E:\\xampp\\htdocs\\robots-sitemap.xml';
$content = file_get_contents($sm);
$entry = '  <url>
    <loc>https://www.ggouai.top/ai-roi-calculator/</loc>
    <lastmod>2026-09-06</lastmod>
    <priority>0.9</priority>
    <changefreq>weekly</changefreq>
  </url>';
if(strpos($content, 'ai-roi-calculator/') === false){
    $content = str_replace('</sitemapurlset>', $entry . "\n</sitemapurlset>", $content);
    file_put_contents($sm, $content);
    echo "[OK] sitemap 已加 ROI 计算器\n";
}

echo "[DONE] /ai-roi-calculator/ 页面 ID$pid\n";
