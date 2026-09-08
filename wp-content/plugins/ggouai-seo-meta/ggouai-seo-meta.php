<?php
/**
 * Plugin Name: ggouai SEO Meta Injector
 * Description: Reads _rank_math_* postmeta and outputs as meta tags in <head>.
 * Author: Wanwan
 * Version: 1.2.0
 */

if (!defined('ABSPATH')) exit;

// =====================================================
// 注册 rewrite rules 绕过 WP post_content 过滤
// =====================================================
add_action('init', function(){
    add_rewrite_rule('^ai-decision-maker/?$', 'index.php?ggouai_page=decision_maker', 'top');
    add_rewrite_rule('^ai-roi-calculator/?$', 'index.php?ggouai_page=roi_calculator', 'top');
    add_rewrite_rule('^prompt-generator/?$', 'index.php?ggouai_page=prompt_generator', 'top');
});
add_filter('query_vars', function($vars){
    $vars[] = 'ggouai_page';
    return $vars;
});
add_action('template_redirect', 'ggouai_render_custom_pages', 5);

function ggouai_render_custom_pages(){
    $page = get_query_var('ggouai_page');
    if($page === 'decision_maker'){
        echo ggouai_decision_maker_html();
        do_action('wp_footer');
        exit;
    }
    if($page === 'roi_calculator'){
        echo ggouai_roi_calculator_html();
        do_action('wp_footer');
        exit;
    }
    if($page === 'prompt_generator'){
        echo ggouai_prompt_generator_html();
        do_action('wp_footer');
        exit;
    }
}

function ggouai_decision_maker_html(){
    $title = 'AI 变现决策器 — 30 秒找到最适合你的 AI 产品';
    $desc = '回答 5 个单选问题，30 秒内 AI 匹配出最适合你的 3 个产品。不用读长文，不用比表格，直接告诉你该买哪个。';
    $permalink = 'https://www.ggouai.top/ai-decision-maker/';
    
    $products = [
        ['id'=>'prompt','slug'=>'xgnhru','title'=>'Chinese Prompt Mastery Pack','price'=>'$19','color'=>'#ff0069','icon'=>'📝','tags'=>['content','daily','efficient','writing','beginner']],
        ['id'=>'agent','slug'=>'dyrvmg','title'=>'Agent Self-Evolution Guide','price'=>'$17','color'=>'#00e5d1','icon'=>'🤖','tags'=>['tech','build','automate','expert','scaling']],
        ['id'=>'polish','slug'=>'zpjabq','title'=>'Chinese-to-English Polishing Toolkit','price'=>'$15','color'=>'#ff6b9d','icon'=>'✍️','tags'=>['writing','daily','efficient','professional','balanced']],
        ['id'=>'overseas','slug'=>'urbhcq','title'=>'AI Overseas Methodology','price'=>'$14','color'=>'#00d4ff','icon'=>'🌍','tags'=>['business','monetize','scale','aggressive','expert']],
        ['id'=>'tools','slug'=>'apjvst','title'=>'AI Tools Practical Guide','price'=>'$12','color'=>'#ff69b4','icon'=>'🚀','tags'=>['beginner','daily','efficient','writing','balanced']],
    ];
    
    $questions = [
        ['q'=>'你主要想做什么？','icon'=>'🎯','opts'=>[
            ['label'=>'📝 做内容创作（写文章/视频/文案）','tags'=>['content','writing','daily']],
            ['label'=>'🤖 构建 AI 工具/Agent','tags'=>['tech','build','automate','expert']],
            ['label'=>'✍️ 写中文/英文文档','tags'=>['writing','daily','professional']],
            ['label'=>'🌍 出海/跨境电商','tags'=>['business','monetize','scale','aggressive']],
            ['label'=>'🚀 刚入门，什么都想用','tags'=>['beginner','daily','balanced']],
        ]],
        ['q'=>'你每天能投入多少时间？','icon'=>'⏰','opts'=>[
            ['label'=>'⏱️ 少于 30 分钟','tags'=>['beginner','efficient']],
            ['label'=>'📚 30 分钟 - 1 小时','tags'=>['daily','balanced']],
            ['label'=>'💪 1-3 小时','tags'=>['daily','professional']],
            ['label'=>'🔥 3 小时以上','tags'=>['expert','scaling','scale']],
        ]],
        ['q'=>'你的目标是什么？','icon'=>'🏆','opts'=>[
            ['label'=>'⚡ 提高现有工作效率','tags'=>['efficient','daily']],
            ['label'=>'🏗️ 建立自动化流程','tags'=>['build','automate']],
            ['label'=>'💰 用 AI 赚钱','tags'=>['business','monetize','scale']],
            ['label'=>'📈 快速入门','tags'=>['beginner','efficient']],
        ]],
        ['q'=>'你的预算范围？','icon'=>'💵','opts'=>[
            ['label'=>'💵 不确定，先看看','tags'=>['balanced']],
            ['label'=>'💵 低于 $15','tags'=>['tools_pref']],
            ['label'=>'💵 $15-$18','tags'=>['balanced']],
            ['label'=>'💵 $18-$20，值得投资','tags'=>['expert','scale']],
        ]],
        ['q'=>'你的风险偏好？','icon'=>'🎲','opts'=>[
            ['label'=>'🐢 稳健，先试水','tags'=>['beginner','balanced']],
            ['label'=>'🐰 平衡，稳中求进','tags'=>['daily','balanced']],
            ['label'=>'🦊 积极，愿意尝试新事物','tags'=>['expert','monetize']],
            ['label'=>'🦁 激进，想要最大收益','tags'=>['aggressive','scale','expert']],
        ]],
    ];
    
    $js_products = json_encode($products, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    $js_questions = json_encode($questions, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    
    $opts_html = '';
    foreach($questions as $qi => $q){
        $opts_html .= '<div class="dm-q" data-q="' . $qi . '">';
        $opts_html .= '<h2 style="margin:0 0 16px 0;color:#333;font-size:1.3em;">' . $q['icon'] . ' ' . htmlspecialchars($q['q']) . '</h2>';
        $opts_html .= '<div class="dm-opts" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:10px;">';
        foreach($q['opts'] as $oi => $opt){
            $opts_html .= '<label class="dm-opt" style="display:flex;align-items:center;gap:10px;padding:14px;background:#fff;border:2px solid #eee;border-radius:10px;cursor:pointer;transition:all 0.2s;font-size:0.95em;color:#333;line-height:1.4;">';
            $opts_html .= '<input type="radio" name="q' . $qi . '" value="' . $oi . '" style="margin:0;cursor:pointer;accent-color:#ff0069;width:18px;height:18px;">';
            $opts_html .= '<span>' . htmlspecialchars($opt['label']) . '</span>';
            $opts_html .= '</label>';
        }
        $opts_html .= '</div></div>';
    }
    
    $jsonld = json_encode([
        '@context'=>'https://schema.org',
        '@graph'=>[
            ['@type'=>'WebApplication','applicationCategory'=>'BusinessApplication','name'=>'AI 变现决策器','url'=>$permalink,'description'=>$desc,'offers'=>['@type'=>'Offer','price'=>'0','priceCurrency'=>'USD']],
            ['@type'=>'WebPage','url'=>$permalink,'name'=>$title,'description'=>$desc],
            ['@type'=>'BreadcrumbList','itemListElement'=>[
                ['@type'=>'ListItem','position'=>1,'name'=>'Home','item'=>'https://www.ggouai.top/'],
                ['@type'=>'ListItem','position'=>2,'name'=>'Decision Maker','item'=>$permalink],
            ]],
            ['@type'=>'ItemList','itemListOrder'=>'ItemListUnordered','itemListElement'=>array_map(function($p){
                return ['@type'=>'Product','name'=>$p['title'],'offers'=>['@type'=>'Offer','price'=>substr($p['price'],1),'priceCurrency'=>'USD','url'=>'https://scgkagent.gumroad.com/l/'.$p['slug']]];
            }, $products)]
        ]
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    // JSON-LD 直接输出（script 标签内不需 HTML 转义，只需防 </script> 序列）
    $jsonld_safe = str_replace('</script>', '<\\/script>', $jsonld);
    $title_escaped = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
    $desc_escaped = htmlspecialchars($desc, ENT_QUOTES, 'UTF-8');
    
    $html = <<<'DM_HTML'
<!DOCTYPE html>
<html lang="zh">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>@@TITLE@@</title>
<meta name="description" content="@@DESC@@">
<link rel="canonical" href="@@PERMALINK@@">
<meta property="og:type" content="website">
<meta property="og:title" content="@@TITLE@@">
<meta property="og:description" content="@@DESC@@">
<meta property="og:url" content="@@PERMALINK@@">
<meta property="og:image" content="https://www.ggouai.top/wp-content/uploads/og-covers/hub-cover.png">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="@@TITLE@@">
<meta name="twitter:description" content="@@DESC@@">
<meta name="twitter:image" content="https://www.ggouai.top/wp-content/uploads/og-covers/hub-cover.png">
<script type="application/ld+json">@@JSONLD@@</script>
<style>
*{box-sizing:border-box}body{margin:0;font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,"PingFang SC","Microsoft YaHei",sans-serif;background:#fff;color:#333;line-height:1.6}a{color:#ff0069}
.dm-opt{transition:all 0.2s;}
.dm-opt:hover{border-color:#ff0069 !important;transform:translateY(-2px);box-shadow:0 4px 12px rgba(255,0,105,0.15)}
.dm-opt:has(input:checked){border-color:#ff0069 !important;background:#fff4f9 !important}
.dm-opt:has(input:checked) span{color:#ff0069;font-weight:bold}
.dm-card{background:#fff;padding:24px;border-radius:12px;margin:16px 0;box-shadow:0 6px 20px rgba(0,0,0,0.08);border-left:6px solid;}
.dm-rank{font-size:0.85em;color:#999;margin-bottom:6px}
.dm-name{font-size:1.4em;font-weight:bold;margin-bottom:4px}
.dm-price{font-size:1.8em;font-weight:bold;margin:8px 0}
.dm-reasons{color:#666;line-height:1.6;font-size:0.95em}
.dm-buy{display:inline-block;padding:12px 28px;border-radius:8px;color:#fff;text-decoration:none;font-weight:bold;margin-top:12px}
.dm-progress-bar{height:4px;background:#eee;border-radius:2px;margin:16px 0;overflow:hidden}
.dm-progress-fill{height:100%;background:#ff0069;transition:width 0.3s}
.dm-nav a{color:#ff0069;text-decoration:none;font-weight:bold;margin:0 4px}
@media (max-width:600px){.dm-nav{font-size:0.8em}.dm-nav a{margin:0 2px}}
</style>
</head>
<body>
<div class="dm-nav" style="padding:12px 20px;background:#fff4f9;border-bottom:1px solid #ff0069;text-align:center;font-size:0.9em;">
<a href="https://www.ggouai.top/">首页</a> · <a href="https://www.ggouai.top/products/">产品</a> · <a href="https://www.ggouai.top/free-prompts/">🎁 免费提示词</a> · <a href="https://www.ggouai.top/compare-ai-products/">对比 5 产品</a> · <a href="https://www.ggouai.top/buy-faq/">购买 FAQ</a> · <a href="https://www.ggouai.top/contact/">联系</a>
</div>

<div style="max-width:820px;margin:0 auto;padding:40px 20px;">
<div style="background:linear-gradient(135deg,#ff0069 0%,#ff6b9d 100%);color:#fff;padding:48px 32px;border-radius:16px;text-align:center;margin-bottom:32px;box-shadow:0 12px 40px rgba(255,0,105,0.3);">
<div style="font-size:4em;margin-bottom:8px;">🤖</div>
<h1 style="margin:0 0 12px 0;font-size:2.2em;line-height:1.2;">AI 变现决策器</h1>
<p style="margin:0;font-size:1.15em;opacity:0.95;">回答 5 个问题，30 秒匹配最适合你的 AI 产品。<br>不用读长文，不用比表格，直接告诉你该买哪个。</p>
</div>

<div style="display:flex;flex-wrap:wrap;gap:12px;margin:0 0 32px 0;text-align:center;">
<div style="flex:1;min-width:120px;background:#f9f9fb;padding:16px;border-radius:10px;border:1px solid #eee;">
<div style="font-size:1.6em;">⚡</div>
<div style="font-weight:bold;color:#333;margin:4px 0;">30 秒</div>
<div style="font-size:0.85em;color:#666;">完成决策</div>
</div>
<div style="flex:1;min-width:120px;background:#f9f9fb;padding:16px;border-radius:10px;border:1px solid #eee;">
<div style="font-size:1.6em;">🎯</div>
<div style="font-weight:bold;color:#333;margin:4px 0;">Top 3</div>
<div style="font-size:0.85em;color:#666;">精准匹配</div>
</div>
<div style="flex:1;min-width:120px;background:#f9f9fb;padding:16px;border-radius:10px;border:1px solid #eee;">
<div style="font-size:1.6em;">💰</div>
<div style="font-weight:bold;color:#333;margin:4px 0;">30 天退款</div>
<div style="font-size:0.85em;color:#666;">无理由</div>
</div>
<div style="flex:1;min-width:120px;background:#f9f9fb;padding:16px;border-radius:10px;border:1px solid #eee;">
<div style="font-size:1.6em;">📧</div>
<div style="font-weight:bold;color:#333;margin:4px 0;">免费报告</div>
<div style="font-size:0.85em;color:#666;">邮件版</div>
</div>
</div>

<div style="background:#fff;padding:32px;border-radius:14px;border:2px solid #ff0069;margin-bottom:24px;">
@@OPTS_HTML@@
</div>

<button id="dm-show-btn" onclick="dmShowResult()" style="display:block;width:100%;background:#000;color:#fff;padding:18px;border:none;border-radius:10px;font-size:1.2em;font-weight:bold;cursor:pointer;transition:transform 0.2s;">🎯 显示我的 Top 3 推荐 →</button>
<p style="text-align:center;color:#999;font-size:0.85em;margin:12px 0 0 0;">或 <a href="https://www.ggouai.top/free-prompts/?utm_source=decision_maker">免费拿 50+ 提示词</a> · <a href="https://www.ggouai.top/compare-ai-products/?utm_source=decision_maker">对比全部 5 个产品</a></p>

<div id="dm-result" style="margin-top:32px;"></div>

<div style="margin-top:48px;background:linear-gradient(135deg,#000 0%,#333 100%);color:#fff;padding:36px 32px;border-radius:14px;text-align:center;">
<h2 style="margin:0 0 8px 0;font-size:1.6em;">📧 拿免费报告 + 后续优惠</h2>
<p style="margin:0 0 20px 0;color:rgba(255,255,255,0.85);font-size:0.95em;">把这份 Top 3 推荐发到你的邮箱。3 天后我们告诉你，同样问题的用户最终买了哪个产品。</p>
<form id="dm-email-form" onsubmit="return false;" style="max-width:440px;margin:0 auto;display:flex;gap:8px;flex-wrap:wrap;">
<input type="email" id="dm-email" placeholder="your@email.com" required style="flex:2;min-width:200px;padding:14px 16px;font-size:1em;border:none;border-radius:8px;">
<button type="submit" onclick="dmSubmitEmail()" style="flex:1;min-width:120px;padding:14px 16px;font-size:1em;font-weight:bold;background:#ff0069;color:#fff;border:none;border-radius:8px;cursor:pointer;">⚡ 免费发送</button>
</form>
<div id="dm-email-result" style="margin-top:12px;font-weight:bold;"></div>
<p style="margin:16px 0 0 0;font-size:0.8em;color:rgba(255,255,255,0.6);">🔒 不发垃圾邮件 · 随时退订</p>
</div>

<hr style="margin:40px 0;border:none;border-top:1px solid #eee;">
<p style="color:#666;font-size:0.9em;text-align:center;">
<a href="https://www.ggouai.top/buy-faq/" style="color:#ff0069;">Buying FAQ</a> ·
<a href="https://www.ggouai.top/products/" style="color:#ff0069;">All Products</a> ·
<a href="https://www.ggouai.top/free-prompts/" style="color:#ff0069;">Free 50+ Prompts</a> ·
<a href="https://www.ggouai.top/compare-ai-products/" style="color:#ff0069;">Compare 5 Guides</a> ·
<a href="https://www.ggouai.top/contact/" style="color:#ff0069;">Contact</a>
</p>
</div>

<script>
window.PRODUCTS = @@JS_PRODUCTS@@;
window.QUESTIONS = @@JS_QUESTIONS@@;

function dmMatch(answers){
  var scores = {};
  window.PRODUCTS.forEach(function(p){ scores[p.id] = 0; });
  var userTags = [];
  Object.keys(answers).forEach(function(qi){
    var qi_num = parseInt(qi);
    var oi = parseInt(answers[qi]);
    if(window.QUESTIONS[qi_num] && window.QUESTIONS[qi_num].opts[oi]) {
      userTags = userTags.concat(window.QUESTIONS[qi_num].opts[oi].tags);
    }
  });
  window.PRODUCTS.forEach(function(p){
    p.tags.forEach(function(tag){
      if(tag === 'tools_pref') return;
      var hits = userTags.filter(function(t){ return t === tag; }).length;
      scores[p.id] += hits * 2;
    });
  });
  if(answers[3] === '1'){
    scores['tools'] += 3;
    scores['overseas'] += 2;
  }
  var sorted = Object.keys(scores).sort(function(a,b){ return scores[b] - scores[a]; });
  return sorted.map(function(id){
    var p = window.PRODUCTS.find(function(x){ return x.id === id; });
    return { product: p, score: scores[id] };
  });
}

function dmShowResult(){
  var answers = {};
  var answered = 0;
  window.QUESTIONS.forEach(function(q, qi){
    var sel = document.querySelector('input[name="q'+qi+'"]:checked');
    if(sel){ answers[qi] = sel.value; answered++; }
  });
  var result = document.getElementById('dm-result');
  if(answered < 3){
    result.innerHTML = '<div style="background:#fff4f9;padding:20px;border-radius:10px;color:#ff0069;text-align:center;">⚠️ 请至少回答 ' + (4 - answered) + ' 个问题</div>';
    return;
  }
  var ranked = dmMatch(answers);
  var top = ranked.slice(0, 3);
  var maxScore = top[0].score || 1;
  var html = '<h2 style="margin:0 0 8px 0;color:#333;">🎯 你的 Top 3 推荐</h2>';
  html += '<p style="color:#666;margin:0 0 24px 0;">基于你回答的 ' + answered + ' 个问题：</p>';
  top.forEach(function(item, i){
    var p = item.product;
    var pct = Math.round((item.score / maxScore) * 100);
    var reason = dmBuildReason(item.product, answers);
    html += '<div class="dm-card" style="border-left-color:' + p.color + ';">';
    html += '<div class="dm-rank">🏆 第 ' + (i+1) + ' 名匹配度 ' + pct + '%</div>';
    html += '<div class="dm-progress-bar"><div class="dm-progress-fill" style="width:' + pct + '%;background:' + p.color + ';"></div></div>';
    html += '<div class="dm-name">' + p.icon + ' ' + p.title + '</div>';
    html += '<div class="dm-price" style="color:' + p.color + ';">' + p.price + '</div>';
    html += '<div class="dm-reasons">' + reason + '</div>';
    html += '<a href="https://scgkagent.gumroad.com/l/' + p.slug + '?utm_source=ggouai.top&utm_medium=decision_maker&utm_campaign=dm_rank' + (i+1) + '" target="_blank" rel="nofollow noopener" class="dm-buy" style="background:' + p.color + ';">🛒 立即购买 →</a>';
    html += '</div>';
  });
  html += '<div style="background:#fff4f9;padding:20px;border-radius:10px;margin-top:24px;border-left:4px solid #ff0069;">';
  html += '<p style="margin:0 0 8px 0;font-weight:bold;color:#333;">📧 想要这份报告的邮件版？</p>';
  html += '<p style="margin:0;color:#666;font-size:0.9em;">保存到邮箱，3 天后我们告诉你，同样问题的用户最终买了哪个。</p>';
  html += '</div>';
  
  // 社交分享按钮
  var shareTitle = encodeURIComponent('我在 AI 变现决策器上测出 Top 3 推荐：' + top.map(function(t){return t.product.title}).join(' / '));
  var shareUrl = encodeURIComponent('https://www.ggouai.top/ai-decision-maker/');
  var shareText = encodeURIComponent('AI 变现决策器 — 30 秒找到最适合你的 AI 产品');
  html += '<div style="margin-top:24px;text-align:center;">';
  html += '<p style="margin:0 0 12px 0;color:#333;font-weight:bold;">📢 分享给朋友，帮 TA 也找到合适的：</p>';
  html += '<div style="display:flex;flex-wrap:wrap;justify-content:center;gap:10px;">';
  html += '<a href="https://twitter.com/intent/tweet?text=' + shareText + '&url=' + shareUrl + '" target="_blank" rel="noopener" style="background:#000;color:#fff;padding:10px 18px;border-radius:8px;text-decoration:none;font-weight:bold;">🐦 X</a>';
  html += '<a href="https://www.facebook.com/sharer/sharer.php?u=' + shareUrl + '" target="_blank" rel="noopener" style="background:#1877f2;color:#fff;padding:10px 18px;border-radius:8px;text-decoration:none;font-weight:bold;">📘 FB</a>';
  html += '<a href="https://www.linkedin.com/sharing/share-offsite/?url=' + shareUrl + '" target="_blank" rel="noopener" style="background:#0077b5;color:#fff;padding:10px 18px;border-radius:8px;text-decoration:none;font-weight:bold;">💼 LinkedIn</a>';
  html += '<a href="https://t.me/share/url?url=' + shareUrl + '&text=' + shareText + '" target="_blank" rel="noopener" style="background:#0088cc;color:#fff;padding:10px 18px;border-radius:8px;text-decoration:none;font-weight:bold;">✈️ Telegram</a>';
  html += '<a href="https://reddit.com/submit?url=' + shareUrl + '&title=' + shareTitle + '" target="_blank" rel="noopener" style="background:#ff4500;color:#fff;padding:10px 18px;border-radius:8px;text-decoration:none;font-weight:bold;">🔴 Reddit</a>';
  html += '</div>';
  html += '</div>';
  
  result.innerHTML = html;
  result.scrollIntoView({behavior:'smooth'});
}

function dmBuildReason(p, answers){
  var reasons = [];
  var skill = window.QUESTIONS[0].opts[answers[0]].label;
  if(p.id === 'prompt'){
    reasons.push('你主要在 ' + skill.slice(-8) + ' 领域，需要高质量中文提示词。');
    reasons.push('Prompt Pack 有 200+ 即用型提示词，覆盖 80% 日常场景。');
  }else if(p.id === 'agent'){
    reasons.push('你有构建 AI 工具的意愿，Agent 指南教你从零开始。');
    reasons.push('3 天构建计划，适合你当前的时间投入。');
  }else if(p.id === 'polish'){
    reasons.push('你要写中英文文档，Toolkit 帮你摆脱 Chinglish。');
    reasons.push('15 个 AI 润色模板，直接复制到 Claude/Gemini 用。');
  }else if(p.id === 'overseas'){
    reasons.push('你想用 AI 赚钱/出海，Methodology 给你 5 条实战路径。');
    reasons.push('决策框架帮你在 3 天内选定方向。');
  }else if(p.id === 'tools'){
    reasons.push('你刚入门，Practical Guide 是最简单的起点。');
    reasons.push('从 0 到 1 的 AI 赚钱路线图，30 个场景手把手教。');
  }
  return reasons.join(' ');
}

function dmSubmitEmail(){
  var email = document.getElementById('dm-email').value.trim();
  var result = document.getElementById('dm-email-result');
  if(!email || email.indexOf('@') === -1){
    result.innerHTML = '<span style="color:#e74c3c;">⚠️ 请输入有效邮箱</span>';
    return;
  }
  result.innerHTML = '<span style="color:#666;">⏳ 发送中...</span>';
  var answers = {};
  window.QUESTIONS.forEach(function(q, qi){
    var sel = document.querySelector('input[name="q'+qi+'"]:checked');
    if(sel) answers[qi] = sel.value;
  });
  var xhr = new XMLHttpRequest();
  xhr.open('POST', 'https://www.ggouai.top/wp-admin/admin-ajax.php', true);
  xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
  xhr.onload = function(){
    var resp;
    try{ resp = JSON.parse(xhr.responseText); }catch(e){ resp = {error:'parse'}; }
    if(resp.success){
      result.innerHTML = '<span style="color:#27ae60;">✅ 报告已发送到 ' + email + '。3 天后见！</span>';
    }else{
      result.innerHTML = '<span style="color:#e74c3c;">⚠️ 失败，请重试</span>';
    }
  };
  xhr.onerror = function(){
    result.innerHTML = '<span style="color:#e74c3c;">⚠️ 网络错误</span>';
  };
  var params = 'action=ggouai_decision_maker_submit&email=' + encodeURIComponent(email)
    + '&answers=' + encodeURIComponent(JSON.stringify(answers));
  xhr.send(params);
}

document.addEventListener('keydown', function(e){
  if(e.key !== 'Enter') return;
  var checked = document.querySelectorAll('input[name^="q"]:checked');
  if(checked.length >= 5){
    dmShowResult();
    e.preventDefault();
  }
});
</script>
</body>
</html>
DM_HTML;

    // 替换占位符
    $html = str_replace('@@OPTS_HTML@@', $opts_html, $html);
    $html = str_replace('@@JS_PRODUCTS@@', $js_products, $html);
    $html = str_replace('@@JS_QUESTIONS@@', $js_questions, $html);
    $html = str_replace('@@JSONLD@@', $jsonld_safe, $html);
    $html = str_replace('@@PERMALINK@@', $permalink, $html);
    $html = str_replace('@@TITLE@@', $title_escaped, $html);
    $html = str_replace('@@DESC@@', $desc_escaped, $html);
    
    return $html;
}

// 强制 sitemap URL 为 https（避免 siteurl 修改后 WP core 仍输出 http）
add_filter('wp_sitemaps_url', function($url){ return str_replace('http://www.ggouai.top', 'https://www.ggouai.top', $url); }, 99);
add_filter('wp_sitemaps_get_sitemap_url', function($url){ return str_replace('http://www.ggouai.top', 'https://www.ggouai.top', $url); }, 99);
add_filter('wp_sitemaps_siteurl', function($url){ return str_replace('http://', 'https://', $url); }, 99);
add_filter('wp_sitemaps_url_generator', function($g){ return str_replace('http://', 'https://', $g); }, 99);

// 用 _rank_math_title 覆盖 wp_title
add_filter('wp_title', function($title){
    global $post;
    if(!is_a($post, 'WP_Post')) return $title;
    $rm = get_post_meta($post->ID, '_rank_math_title', true);
    if(empty($rm)) return $title;
    $uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
    $path = parse_url($uri, PHP_URL_PATH) ?: '/';
    if($path === '/' || preg_match('#^/page/\d+/?$#', $path)){
        $blogname = get_option('blogname');
        $blodesc = get_option('blogdescription');
        return empty($blodesc) ? $blogname : $blogname . ' — ' . $blodesc;
    }
    return $rm;
}, 20);

// 最后钩子：直接覆盖 <title> 标签
add_action('wp_head', function(){
    global $post;
    $uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
    $path = parse_url($uri, PHP_URL_PATH) ?: '/';
    $title = null;
    if($path === '/' || preg_match('#^/page/\d+/?$#', $path)){
        $blogname = get_option('blogname');
        $blodesc = get_option('blogdescription');
        $title = empty($blodesc) ? $blogname : $blogname . ' — ' . $blodesc;
    } elseif(is_a($post, 'WP_Post')){
        $rm = get_post_meta($post->ID, '_rank_math_title', true);
        $title = !empty($rm) ? $rm : get_the_title($post->ID);
    }
    if($title) echo '<title>' . esc_html($title) . '</title>' . "\n";
}, 1000);

remove_action('wp_head', '_wp_render_title_tag', 1);
add_action('init', function(){
    remove_action('wp_head', '_wp_render_title_tag', 1);
});

// 移除 Polylang 生成的多余 sitemap
add_filter('wp_sitemaps_register_sitemap', function($sitemaps){
    if(isset($sitemaps['lang-sitemap'])) unset($sitemaps['lang-sitemap']);
    return $sitemaps;
});

add_action('wp_head', 'ggouai_inject_seo_meta', 5);

function ggouai_inject_seo_meta(){
    global $post;
    if(!is_a($post, 'WP_Post')) return;
    $pid = $post->ID;

    $uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
    $path = parse_url($uri, PHP_URL_PATH) ?: '/';
    if($path === '/' || preg_match('#^/page/\d+/?$#', $path)) return;

    $title    = get_post_meta($pid, '_rank_math_title', true);
    $desc     = get_post_meta($pid, '_rank_math_desc', true);
    $kw       = get_post_meta($pid, '_rank_math_focus_keyword', true);
    $og_title = get_post_meta($pid, '_rank_math_og_title', true);
    $og_desc  = get_post_meta($pid, '_rank_math_og_desc', true);
    $og_type  = get_post_meta($pid, '_rank_math_og_type', true);
    $og_url   = get_post_meta($pid, '_rank_math_og_url', true);
    $og_site  = get_post_meta($pid, '_rank_math_og_site_name', true);
    $tw_title = get_post_meta($pid, '_rank_math_og_twitter_title', true);
    $tw_desc  = get_post_meta($pid, '_rank_math_og_twitter_desc', true);

    if(empty($og_title)) $og_title = $title;
    if(empty($og_desc))  $og_desc  = $desc;
    if(empty($og_type))  $og_type  = 'article';
    if(empty($og_site))  $og_site  = get_bloginfo('name');
    if(empty($title))    $title    = get_the_title($pid);

    $canon = get_post_meta($pid, '_rank_math_canonical_url', true);
    if(empty($canon)) $canon = get_permalink($pid);

    if(!empty($desc)) echo '<meta name="description" content="' . esc_attr($desc) . '" />' . "\n";
    if(!empty($kw))   echo '<meta name="keywords" content="' . esc_attr($kw) . '" />' . "\n";
    echo '<link rel="canonical" href="' . esc_url($canon) . '" />' . "\n";

    echo '<meta property="og:type" content="' . esc_attr($og_type) . '" />' . "\n";
    echo '<meta property="og:title" content="' . esc_attr($og_title) . '" />' . "\n";
    echo '<meta property="og:description" content="' . esc_attr($og_desc) . '" />' . "\n";
    echo '<meta property="og:url" content="' . esc_url($og_url) . '" />' . "\n";
    echo '<meta property="og:site_name" content="' . esc_attr($og_site) . '" />' . "\n";

    $og_image = get_post_meta($pid, '_rank_math_og_image', true);
    if(!empty($og_image)){
        $w = get_post_meta($pid, '_rank_math_og_image_width', true);
        $h = get_post_meta($pid, '_rank_math_og_image_height', true);
        echo '<meta property="og:image" content="' . esc_url($og_image) . '" />' . "\n";
        if(!empty($w)) echo '<meta property="og:image:width" content="' . intval($w) . '" />' . "\n";
        if(!empty($h)) echo '<meta property="og:image:height" content="' . intval($h) . '" />' . "\n";
    }

    $tw_image = get_post_meta($pid, '_rank_math_og_twitter_image', true);
    if(empty($tw_image)) $tw_image = $og_image;

    if(empty($tw_title)) $tw_title = $og_title;
    if(empty($tw_desc))  $tw_desc  = $og_desc;
    echo '<meta name="twitter:card" content="summary_large_image" />' . "\n";
    echo '<meta name="twitter:title" content="' . esc_attr($tw_title) . '" />' . "\n";
    echo '<meta name="twitter:description" content="' . esc_attr($tw_desc) . '" />' . "\n";
    if(!empty($tw_image)) echo '<meta name="twitter:image" content="' . esc_url($tw_image) . '" />' . "\n";

    // JSON-LD 全部合并到 @graph
    $jsonld = get_post_meta($pid, '_ggouai_jsonld', true);
    $jsonld_faq = get_post_meta($pid, '_ggouai_jsonld_faq', true);
    $jsonld_bc = get_post_meta($pid, '_ggouai_jsonld_breadcrumb', true);
    $primary = null;
    if(!empty($jsonld)) $primary = json_decode($jsonld, true);
    $extras = [];
    if(!empty($jsonld_faq)) $extras[] = json_decode($jsonld_faq, true);
    if(!empty($jsonld_bc)) $extras[] = json_decode($jsonld_bc, true);

    if($primary !== null && is_array($primary)){
        $final = $primary;
        $extras = array_filter($extras, function($x){ return $x !== null; });
        if(!empty($extras)){
            if(isset($final['@graph']) && is_array($final['@graph'])){
                $final['@graph'] = array_merge($final['@graph'], array_values($extras));
            } else {
                $final = ['@context' => 'https://schema.org', '@graph' => array_merge([$primary], array_values($extras))];
            }
        }
        $safe = json_encode($final, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $safe = str_replace('</', '<\/', $safe);
        echo '<script type="application/ld+json">' . $safe . '</script>' . "\n";
    } elseif(!empty($jsonld)){
        $safe = str_replace('</', '<\/', $jsonld);
        echo '<script type="application/ld+json">' . $safe . '</script>' . "\n";
    } elseif(!empty($extras)){
        $extras = array_values(array_filter($extras, function($x){ return $x !== null; }));
        if(!empty($extras)){
            $final = ['@context' => 'https://schema.org', '@graph' => $extras];
            $safe = json_encode($final, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            $safe = str_replace('</', '<\/', $safe);
            echo '<script type="application/ld+json">' . $safe . '</script>' . "\n";
        }
    }
}

// =====================================================
// 5 个产品深度文 ID → 产品信息映射
// 用于浮动 Buy Now 按钮
// =====================================================
function ggouai_product_map(){
    return [
        593 => ['slug' => 'xgnhru', 'title' => 'Chinese Prompt Mastery Pack', 'price' => '$19', 'color' => '#ff0069', 'blurb' => '200+ 中文提示词 + 7 大框架'],
        594 => ['slug' => 'dyrvmg', 'title' => 'Agent Self-Evolution Guide', 'price' => '$17', 'color' => '#00e5d1', 'blurb' => 'Self-Evolution 架构 + 3 天计划'],
        595 => ['slug' => 'zpjabq', 'title' => 'Chinese-to-English Polishing Toolkit', 'price' => '$15', 'color' => '#ff6b9d', 'blurb' => '20 条规则 + 15 个 AI 润色模板'],
        596 => ['slug' => 'urbhcq', 'title' => 'AI Overseas Methodology', 'price' => '$14', 'color' => '#00d4ff', 'blurb' => '5 条路径 + 决策框架'],
        597 => ['slug' => 'apjvst', 'title' => 'AI Tools Practical Guide', 'price' => '$12', 'color' => '#ff69b4', 'blurb' => '零基础 AI 赚钱路线图'],
    ];
}

// =====================================================
// ROI 计算器 HTML 生成
// =====================================================
function ggouai_roi_calculator_html(){
    $title = 'AI ROI 计算器 — 30 秒算出你能省多少钱';
    $desc = '输入你的时薪和每周花的时间，直接算出买 AI 工具一年能帮你省多少。数字说话，不玩虚的。';
    $permalink = 'https://www.ggouai.top/ai-roi-calculator/';
    
    $jsonld = json_encode([
        '@context'=>'https://schema.org',
        '@graph'=>[
            ['@type'=>'WebApplication','applicationCategory'=>'FinanceApplication','name'=>'AI ROI 计算器','url'=>$permalink,'description'=>$desc,'operatingSystem'=>'Web','offers'=>['@type'=>'Offer','price'=>'0','priceCurrency'=>'USD']],
            ['@type'=>'WebPage','url'=>$permalink,'name'=>$title,'description'=>$desc],
            ['@type'=>'BreadcrumbList','itemListElement'=>[
                ['@type'=>'ListItem','position'=>1,'name'=>'Home','item'=>'https://www.ggouai.top/'],
                ['@type'=>'ListItem','position'=>2,'name'=>'ROI Calculator','item'=>$permalink],
            ]],
        ]
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    $jsonld_safe = str_replace('</script>', '<\\/script>', $jsonld);
    $title_escaped = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
    $desc_escaped = htmlspecialchars($desc, ENT_QUOTES, 'UTF-8');
    
    $html = <<<'ROI_HTML'
<!DOCTYPE html>
<html lang="zh">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>@@TITLE@@</title>
<meta name="description" content="@@DESC@@">
<link rel="canonical" href="@@PERMALINK@@">
<meta property="og:type" content="website">
<meta property="og:title" content="@@TITLE@@">
<meta property="og:description" content="@@DESC@@">
<meta property="og:url" content="@@PERMALINK@@">
<meta property="og:image" content="https://www.ggouai.top/wp-content/uploads/og-covers/hub-cover.png">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="@@TITLE@@">
<meta name="twitter:description" content="@@DESC@@">
<meta name="twitter:image" content="https://www.ggouai.top/wp-content/uploads/og-covers/hub-cover.png">
<script type="application/ld+json">@@JSONLD@@</script>
<style>
*{box-sizing:border-box}body{margin:0;font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,"PingFang SC","Microsoft YaHei",sans-serif;background:#0a0a0a;color:#fff;line-height:1.6}a{color:#00e5d1}
.roi-nav a{color:#00e5d1;text-decoration:none;font-weight:bold;margin:0 4px}
.roi-nav a:hover{text-decoration:underline}
.slider-row{display:flex;align-items:center;gap:16px;margin:20px 0}
.slider-row label{flex:1;color:#ccc;font-size:0.95em}
.slider-row input[type="range"]{flex:2;accent-color:#00e5d1;height:6px}
.slider-row .val{width:100px;text-align:right;font-size:1.4em;font-weight:bold;color:#00e5d1}
.result-card{background:linear-gradient(135deg,#00e5d1 0%,#00b8a5 100%);color:#000;padding:36px;border-radius:16px;margin:32px 0;text-align:center;box-shadow:0 12px 40px rgba(0,229,209,0.4)}
.result-amount{font-size:3.6em;font-weight:bold;line-height:1;margin:8px 0}
.result-label{font-size:1em;opacity:0.85;margin:0 0 12px 0}
.pain-card{background:#1a1a1a;padding:24px;border-radius:12px;margin:16px 0;border-left:4px solid #ff4500}
.pain-num{color:#ff4500;font-size:2.4em;font-weight:bold;margin:0}
.earn-card{background:#1a1a1a;padding:24px;border-radius:12px;margin:16px 0;border-left:4px solid #00e5d1}
.earn-num{color:#00e5d1;font-size:2.4em;font-weight:bold;margin:0}
.buy-btn{display:inline-block;background:#ff0069;color:#fff;padding:14px 28px;border-radius:8px;text-decoration:none;font-weight:bold;margin:6px 4px}
@media (max-width:600px){.roi-nav{font-size:0.8em}.slider-row{flex-wrap:wrap}.slider-row label,.slider-row input[type="range"]{flex:1 1 100%}.slider-row .val{text-align:left}}
</style>
</head>
<body>
<div class="roi-nav" style="padding:12px 20px;background:#000;border-bottom:1px solid #333;text-align:center;font-size:0.9em;">
<a href="https://www.ggouai.top/">首页</a> · <a href="https://www.ggouai.top/ai-decision-maker/">🎯 AI 决策器</a> · <a href="https://www.ggouai.top/products/">产品</a> · <a href="https://www.ggouai.top/free-prompts/">🎁 免费提示词</a> · <a href="https://www.ggouai.top/compare-ai-products/">对比 5 产品</a> · <a href="https://www.ggouai.top/buy-faq/">购买 FAQ</a>
</div>

<div style="max-width:780px;margin:0 auto;padding:40px 20px;">
<div style="background:linear-gradient(135deg,#00e5d1 0%,#00b8a5 100%);color:#000;padding:44px 32px;border-radius:16px;text-align:center;margin-bottom:32px;box-shadow:0 12px 40px rgba(0,229,209,0.3);">
<div style="font-size:4em;margin-bottom:8px;">💰</div>
<h1 style="margin:0 0 12px 0;font-size:2.2em;line-height:1.2;">AI ROI 计算器</h1>
<p style="margin:0;font-size:1.15em;">输入你的时薪和每周花的时间，直接算出买 AI 工具一年能帮你省多少。<br>数字说话，不玩虚的。</p>
</div>

<div style="background:#1a1a1a;padding:32px;border-radius:14px;border:1px solid #333;margin-bottom:24px;">
<h2 style="margin:0 0 16px 0;color:#fff;font-size:1.3em;">📊 输入你的数据</h2>

<div class="slider-row">
<label>💰 你的时薪 ($/小时)</label>
<input type="range" id="salary" min="10" max="200" step="5" value="50" oninput="roiCalc()">
<div class="val" id="salary-val">$50</div>
</div>

<div class="slider-row">
<label>⏱️ 每周花在这类工作上的时间 (小时)</label>
<input type="range" id="hours" min="2" max="60" step="1" value="15" oninput="roiCalc()">
<div class="val" id="hours-val">15h</div>
</div>

<div class="slider-row">
<label>🤖 AI 能帮你节省的时间比例</label>
<input type="range" id="save_pct" min="20" max="90" step="5" value="60" oninput="roiCalc()">
<div class="val" id="save-val">60%</div>
</div>

<div style="background:#000;padding:16px;border-radius:10px;margin-top:24px;color:#999;font-size:0.85em;text-align:center;">
💡 提示：AI 平均能节省 40-80% 的重复劳动时间。写作/翻译/整理数据/客服回复都在范围内。
</div>
</div>

<div id="roi-result"></div>

<div style="background:linear-gradient(135deg,#000 0%,#1a1a1a 100%);color:#fff;padding:32px;border-radius:14px;margin-top:32px;">
<h2 style="margin:0 0 12px 0;font-size:1.5em;">🎁 现在就试试这些产品</h2>
<p style="margin:0 0 20px 0;color:#aaa;">所有产品 30 天无理由退款。你先试试，不行全额退。</p>
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:12px;">
<a href="https://scgkagent.gumroad.com/l/apjvst?utm_source=ggouai.top&utm_medium=roi_calc&utm_campaign=roi_tools" class="buy-btn" style="text-align:center;padding:16px;">
🚀 AI Tools Guide<br><span style="font-weight:normal;opacity:0.85;font-size:0.85em;">$12 起步</span>
</a>
<a href="https://scgkagent.gumroad.com/l/xgnhru?utm_source=ggouai.top&utm_medium=roi_calc&utm_campaign=roi_prompt" class="buy-btn" style="text-align:center;padding:16px;">
📝 Prompt Pack<br><span style="font-weight:normal;opacity:0.85;font-size:0.85em;">$19 200+ 提示词</span>
</a>
<a href="https://scgkagent.gumroad.com/l/dyrvmg?utm_source=ggouai.top&utm_medium=roi_calc&utm_campaign=roi_agent" class="buy-btn" style="text-align:center;padding:16px;">
🤖 Agent Guide<br><span style="font-weight:normal;opacity:0.85;font-size:0.85em;">$17 自动化</span>
</a>
<a href="https://www.ggouai.top/ai-decision-maker/?utm_source=roi_calc" class="buy-btn" style="text-align:center;padding:16px;background:#00e5d1;color:#000;">
🎯 不确定买哪个？<br><span style="font-weight:normal;opacity:0.85;font-size:0.85em;">30 秒 AI 帮你决定</span>
</a>
</div>
</div>

<hr style="margin:40px 0;border:none;border-top:1px solid #333;">
<p style="color:#666;font-size:0.9em;text-align:center;">
<a href="https://www.ggouai.top/ai-decision-maker/">🎯 决策器</a> ·
<a href="https://www.ggouai.top/products/">产品</a> ·
<a href="https://www.ggouai.top/free-prompts/">免费 50+ 提示词</a> ·
<a href="https://www.ggouai.top/compare-ai-products/">对比 5 产品</a> ·
<a href="https://www.ggouai.top/buy-faq/">购买 FAQ</a>
</p>
</div>

<script>
function roiCalc(){
  var salary = parseInt(document.getElementById('salary').value);
  var hours = parseInt(document.getElementById('hours').value);
  var savePct = parseInt(document.getElementById('save_pct').value);
  
  document.getElementById('salary-val').textContent = '$' + salary;
  document.getElementById('hours-val').textContent = hours + 'h';
  document.getElementById('save-val').textContent = savePct + '%';
  
  // 计算
  var weeklyHoursSaved = hours * (savePct / 100);
  var weeklyValueSaved = weeklyHoursSaved * salary;
  var yearlyValueSaved = weeklyValueSaved * 52;
  
  // 各产品 ROI
  var products = [
    {name:'AI Tools Guide', icon:'🚀', price:12, url:'https://scgkagent.gumroad.com/l/apjvst?utm_source=ggouai.top&utm_medium=roi_calc&utm_campaign=roi_tools'},
    {name:'Prompt Pack', icon:'📝', price:19, url:'https://scgkagent.gumroad.com/l/xgnhru?utm_source=ggouai.top&utm_medium=roi_calc&utm_campaign=roi_prompt'},
    {name:'Agent Guide', icon:'🤖', price:17, url:'https://scgkagent.gumroad.com/l/dyrvmg?utm_source=ggouai.top&utm_medium=roi_calc&utm_campaign=roi_agent'},
  ];
  
  var html = '<div class="result-card">';
  html += '<div class="result-label">🎯 你每周可以省下的时间</div>';
  html += '<div class="result-amount">' + weeklyHoursSaved.toFixed(1) + ' 小时</div>';
  html += '<div class="result-label">= $' + weeklyValueSaved.toLocaleString() + ' / 周</div>';
  html += '</div>';
  
  html += '<div class="result-card" style="background:linear-gradient(135deg,#ff0069 0%,#ff6b9d 100%);color:#fff;">';
  html += '<div class="result-label" style="opacity:0.9;">📅 一年能帮你省下</div>';
  html += '<div class="result-amount">$' + yearlyValueSaved.toLocaleString(undefined,{maximumFractionDigits:0}) + '</div>';
  html += '<div class="result-label" style="opacity:0.9;">花 $12-$19 买 AI 工具，回本只要 ' + Math.ceil(12 / weeklyValueSaved * 100) + ' 分钟</div>';
  html += '</div>';
  
  html += '<h2 style="margin:32px 0 16px 0;color:#fff;">💰 每个产品的 ROI</h2>';
  products.forEach(function(p){
    var netAnnual = yearlyValueSaved - p.price;
    var roiPct = ((yearlyValueSaved / p.price - 1) * 100).toFixed(0);
    var weeksToPayback = (p.price / weeklyValueSaved).toFixed(2);
    html += '<div class="earn-card">';
    html += '<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;">';
    html += '<div style="font-size:1.2em;font-weight:bold;color:#fff;">' + p.icon + ' ' + p.name + ' <span style="color:#00e5d1;">$' + p.price + '</span></div>';
    html += '<div class="earn-num" style="font-size:1.6em;">+' + roiPct + '% ROI</div>';
    html += '</div>';
    html += '<div style="color:#aaa;font-size:0.9em;margin:8px 0;">';
    html += '• 回本时间：<strong style="color:#00e5d1;">' + weeksToPayback + ' 周</strong><br>';
    html += '• 一年后净赚：<strong style="color:#00e5d1;">$' + netAnnual.toLocaleString(undefined,{maximumFractionDigits:0}) + '</strong>';
    html += '</div>';
    html += '<a href="' + p.url + '" target="_blank" rel="nofollow noopener" class="buy-btn" style="margin-top:12px;">🛒 立即购买 →</a>';
    html += '</div>';
  });
  
  // 分享结果
  var shareText = encodeURIComponent('我算了下，AI 工具一年能帮我省 $' + yearlyValueSaved.toLocaleString(undefined,{maximumFractionDigits:0}) + '。你也算算看：');
  var shareUrl = encodeURIComponent('https://www.ggouai.top/ai-roi-calculator/');
  html += '<div style="background:#1a1a1a;padding:20px;border-radius:12px;margin-top:24px;text-align:center;border:1px solid #333;">';
  html += '<p style="margin:0 0 12px 0;color:#fff;font-weight:bold;">📢 分享给朋友，帮 TA 也算算：</p>';
  html += '<a href="https://twitter.com/intent/tweet?text=' + shareText + '&url=' + shareUrl + '" target="_blank" rel="noopener" class="buy-btn" style="background:#000;">🐦 X</a>';
  html += '<a href="https://www.facebook.com/sharer/sharer.php?u=' + shareUrl + '" target="_blank" rel="noopener" class="buy-btn" style="background:#1877f2;">📘 FB</a>';
  html += '<a href="https://t.me/share/url?url=' + shareUrl + '&text=' + shareText + '" target="_blank" rel="noopener" class="buy-btn" style="background:#0088cc;">✈️ Telegram</a>';
  html += '<a href="https://www.linkedin.com/sharing/share-offsite/?url=' + shareUrl + '" target="_blank" rel="noopener" class="buy-btn" style="background:#0077b5;">💼 LinkedIn</a>';
  html += '</div>';
  
  document.getElementById('roi-result').innerHTML = html;
}

// 初始化
window.onload = function(){ roiCalc(); };
</script>
</body>
</html>
ROI_HTML;
    
    $html = str_replace('@@JSONLD@@', $jsonld_safe, $html);
    $html = str_replace('@@PERMALINK@@', $permalink, $html);
    $html = str_replace('@@TITLE@@', $title_escaped, $html);
    $html = str_replace('@@DESC@@', $desc_escaped, $html);
    
    return $html;
}

// =====================================================
// Prompt 生成器 HTML 生成
// =====================================================
function ggouai_prompt_generator_html(){
    $title = 'AI Prompt 生成器 — 免费造提示词';
    $desc = '选场景，选语气，选详细度，一键生成高质量 AI 提示词。免费，无需注册，一键复制。';
    $permalink = 'https://www.ggouai.top/prompt-generator/';
    
    $jsonld = json_encode([
        '@context'=>'https://schema.org',
        '@graph'=>[
            ['@type'=>'WebApplication','applicationCategory'=>'ProductivityApplication','name'=>'AI Prompt 生成器','url'=>$permalink,'description'=>$desc,'operatingSystem'=>'Web','offers'=>['@type'=>'Offer','price'=>'0','priceCurrency'=>'USD']],
            ['@type'=>'WebPage','url'=>$permalink,'name'=>$title,'description'=>$desc],
            ['@type'=>'BreadcrumbList','itemListElement'=>[
                ['@type'=>'ListItem','position'=>1,'name'=>'Home','item'=>'https://www.ggouai.top/'],
                ['@type'=>'ListItem','position'=>2,'name'=>'Prompt Generator','item'=>$permalink],
            ]],
        ]
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    $jsonld_safe = str_replace('</script>', '<\\/script>', $jsonld);
    $title_escaped = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
    $desc_escaped = htmlspecialchars($desc, ENT_QUOTES, 'UTF-8');
    
    $html = <<<'PG_HTML'
<!DOCTYPE html>
<html lang="zh">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>@@TITLE@@</title>
<meta name="description" content="@@DESC@@">
<link rel="canonical" href="@@PERMALINK@@">
<meta property="og:type" content="website">
<meta property="og:title" content="@@TITLE@@">
<meta property="og:description" content="@@DESC@@">
<meta property="og:url" content="@@PERMALINK@@">
<meta property="og:image" content="https://www.ggouai.top/wp-content/uploads/og-covers/hub-cover.png">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="@@TITLE@@">
<meta name="twitter:description" content="@@DESC@@">
<meta name="twitter:image" content="https://www.ggouai.top/wp-content/uploads/og-covers/hub-cover.png">
<script type="application/ld+json">@@JSONLD@@</script>
<style>
*{box-sizing:border-box}body{margin:0;font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,"PingFang SC","Microsoft YaHei",sans-serif;background:#0a0a0a;color:#fff;line-height:1.6}a{color:#00e5d1}
.pg-nav a{color:#00e5d1;text-decoration:none;font-weight:bold;margin:0 4px}
.opts-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(140px,1fr));gap:10px}
.opt-card{padding:16px;background:#1a1a1a;border:2px solid #333;border-radius:10px;cursor:pointer;transition:all 0.2s;text-align:center;font-size:0.9em}
.opt-card:hover{border-color:#00e5d1;transform:translateY(-2px)}
.opt-card.selected{border-color:#00e5d1;background:#103330;color:#00e5d1}
.opt-card .icon{font-size:1.6em;margin-bottom:4px}
.output-box{background:#000;color:#00e5d1;padding:20px;border-radius:10px;font-family:"SF Mono","Consolas",monospace;font-size:0.95em;white-space:pre-wrap;word-break:break-word;max-height:400px;overflow:auto;border:1px solid #333}
.copy-btn{background:#00e5d1;color:#000;padding:10px 20px;border:none;border-radius:8px;font-weight:bold;cursor:pointer;font-size:1em}
.copy-btn:hover{background:#00b8a5}
.share-btn{display:inline-block;padding:8px 16px;border-radius:8px;color:#fff;text-decoration:none;font-weight:bold;font-size:0.85em}
.pg-cta{background:linear-gradient(135deg,#ff0069 0%,#ff6b9d 100%);color:#fff;padding:32px;border-radius:14px;margin:32px 0;text-align:center}
@media (max-width:600px){.pg-nav{font-size:0.8em}.opts-grid{grid-template-columns:repeat(auto-fit,minmax(100px,1fr))}.opt-card{padding:10px;font-size:0.8em}}
</style>
</head>
<body>
<div class="pg-nav" style="padding:12px 20px;background:#000;border-bottom:1px solid #333;text-align:center;font-size:0.9em;">
<a href="https://www.ggouai.top/">首页</a> · <a href="https://www.ggouai.top/ai-decision-maker/">🎯 决策器</a> · <a href="https://www.ggouai.top/ai-roi-calculator/">💰 ROI</a> · <a href="https://www.ggouai.top/products/">产品</a> · <a href="https://www.ggouai.top/free-prompts/">🎁 免费提示词</a>
</div>

<div style="max-width:820px;margin:0 auto;padding:40px 20px;">
<div style="background:linear-gradient(135deg,#00e5d1 0%,#00b8a5 100%);color:#000;padding:44px 32px;border-radius:16px;text-align:center;margin-bottom:32px;box-shadow:0 12px 40px rgba(0,229,209,0.3);">
<div style="font-size:4em;margin-bottom:8px;">✨</div>
<h1 style="margin:0 0 12px 0;font-size:2.2em;line-height:1.2;">AI Prompt 生成器</h1>
<p style="margin:0;font-size:1.15em;">选场景，选语气，一键生成高质量提示词。<br>免费，无需注册，用完就复制。</p>
</div>

<div style="background:#1a1a1a;padding:28px;border-radius:14px;border:1px solid #333;margin-bottom:24px;">
<h2 style="margin:0 0 16px 0;color:#fff;font-size:1.3em;">1️⃣ 选场景</h2>
<div class="opts-grid" id="scene-grid">
<div class="opt-card selected" data-scene="writing"><div class="icon">✍️</div>写作</div>
<div class="opt-card" data-scene="coding"><div class="icon">💻</div>编程</div>
<div class="opt-card" data-scene="marketing"><div class="icon">📣</div>营销</div>
<div class="opt-card" data-scene="analysis"><div class="icon">📊</div>分析</div>
<div class="opt-card" data-scene="translation"><div class="icon">🌐</div>翻译</div>
<div class="opt-card" data-scene="business"><div class="icon">💼</div>商务</div>
<div class="opt-card" data-scene="education"><div class="icon">🎓</div>学习</div>
<div class="opt-card" data-scene="creative"><div class="icon">🎨</div>创意</div>
</div>

<h2 style="margin:24px 0 16px 0;color:#fff;font-size:1.3em;">2️⃣ 选语气</h2>
<div class="opts-grid" id="tone-grid">
<div class="opt-card selected" data-tone="professional">专业</div>
<div class="opt-card" data-tone="casual">轻松</div>
<div class="opt-card" data-tone="friendly">友好</div>
<div class="opt-card" data-tone="persuasive">说服</div>
<div class="opt-card" data-tone="expert">专家</div>
<div class="opt-card" data-tone="creative">创意</div>
</div>

<h2 style="margin:24px 0 16px 0;color:#fff;font-size:1.3em;">3️⃣ 选详细度</h2>
<div class="opts-grid" id="detail-grid">
<div class="opt-card" data-detail="brief">简短</div>
<div class="opt-card selected" data-detail="balanced">适中</div>
<div class="opt-card" data-detail="detailed">详尽</div>
</div>

<h2 style="margin:24px 0 16px 0;color:#fff;font-size:1.3em;">4️⃣ 你想让 AI 做什么？</h2>
<input type="text" id="user-task" placeholder="例如：写一篇小红书爆款文案" style="width:100%;padding:14px 16px;font-size:1em;border:2px solid #333;border-radius:8px;background:#000;color:#fff;">
</div>

<button onclick="pgGenerate()" style="display:block;width:100%;background:#00e5d1;color:#000;padding:18px;border:none;border-radius:10px;font-size:1.2em;font-weight:bold;cursor:pointer;transition:transform 0.2s;">✨ 生成 Prompt</button>

<div id="pg-result" style="margin-top:24px;"></div>

<div class="pg-cta">
<h2 style="margin:0 0 8px 0;font-size:1.5em;">📚 想要 200+ 现成模板？</h2>
<p style="margin:0 0 16px 0;opacity:0.95;">Prompt Pack 有 200+ 中文即用型提示词 + 7 大框架。<br>比生成器更实用，覆盖写作/翻译/分析/营销。</p>
<a href="https://scgkagent.gumroad.com/l/xgnhru?utm_source=ggouai.top&utm_medium=prompt_generator&utm_campaign=pg_upgrade" style="display:inline-block;background:#fff;color:#ff0069;padding:14px 32px;border-radius:8px;font-weight:bold;text-decoration:none;">🛒 Prompt Pack $19 →</a>
</div>

<hr style="margin:40px 0;border:none;border-top:1px solid #333;">
<p style="color:#666;font-size:0.9em;text-align:center;">
<a href="https://www.ggouai.top/ai-decision-maker/">🎯 决策器</a> ·
<a href="https://www.ggouai.top/ai-roi-calculator/">💰 ROI</a> ·
<a href="https://www.ggouai.top/products/">产品</a> ·
<a href="https://www.ggouai.top/free-prompts/">免费 50+ 提示词</a> ·
<a href="https://www.ggouai.top/buy-faq/">购买 FAQ</a>
</p>
</div>

<script>
var selScene = 'writing';
var selTone = 'professional';
var selDetail = 'balanced';

function pgBindGrid(gridId, callback){
  var cards = document.querySelectorAll('#' + gridId + ' .opt-card');
  cards.forEach(function(card){
    card.addEventListener('click', function(){
      cards.forEach(function(c){ c.classList.remove('selected'); });
      card.classList.add('selected');
      callback(card.dataset);
    });
  });
}

pgBindGrid('scene-grid', function(d){ selScene = d.scene; });
pgBindGrid('tone-grid', function(d){ selTone = d.tone; });
pgBindGrid('detail-grid', function(d){ selDetail = d.detail; });

// 场景模板
var sceneTemplates = {
  writing: '你是资深内容创作者。请{task}。要求：结构清晰，语言流畅，有吸引力。',
  coding: '你是 10 年经验的全栈工程师。请{task}。要求：代码可读性高，注释完整，遵循最佳实践。',
  marketing: '你是首席营销官。请{task}。要求：抓住用户痛点，激发行动，文案有感染力。',
  analysis: '你是数据分析专家。请{task}。要求：逻辑严密，数据支撑，得出结论有依据。',
  translation: '你是专业翻译家。请{task}。要求：信达雅，符合目标语言习惯，术语准确。',
  business: '你是商业顾问。请{task}。要求：可行性高，成本可控，有明确 ROI 预期。',
  education: '你是耐心好老师。请{task}。要求：由浅入深，用类比帮助理解，附练习题。',
  creative: '你是创意导演。请{task}。要求：跳出现有套路，给意想不到的视角，让人眼前一亮。'
};

// 语气修饰
var toneModifiers = {
  professional: '整体风格：专业、克制、客观。避免口语化和表情符号。',
  casual: '整体风格：轻松、口语化、像朋友聊天。可以用表情符号和网络用语。',
  friendly: '整体风格：温暖、亲切、让人放心。像朋友推荐东西。',
  persuasive: '整体风格：强说服力、有节奏感、结尾给行动号召。',
  expert: '整体风格：专家视角、引用具体框架或方法论、有权威感。',
  creative: '整体风格：充满想象力、用隐喻和类比、让人有"原来还能这样"的感觉。'
};

// 详细度
var detailModifiers = {
  brief: '输出长度：简短，300 字以内。只保留核心信息。',
  balanced: '输出长度：适中，500-800 字。有结构、有细节、有例子。',
  detailed: '输出长度：详尽，1500+ 字。包含背景、步骤、案例、常见陷阱。'
};

function pgGenerate(){
  var task = document.getElementById('user-task').value.trim();
  if(!task){
    document.getElementById('pg-result').innerHTML = '<div style="background:#fff4f9;padding:16px;border-radius:10px;color:#ff0069;text-align:center;">⚠️ 请输入你想让 AI 做什么</div>';
    return;
  }
  
  var tmpl = sceneTemplates[selScene];
  var prompt = tmpl.replace('{task}', task);
  prompt += '\n\n' + toneModifiers[selTone];
  prompt += '\n' + detailModifiers[selDetail];
  
  // 病毒追踪尾巴
  prompt += '\n\n（Prompt by ggouai.top/prompt-generator — 试试生成你的专属版本 →）';
  
  var result = document.getElementById('pg-result');
  result.innerHTML = '<h2 style="margin:0 0 12px 0;color:#fff;">✨ 你的专属 Prompt</h2>';
  result.innerHTML += '<div class="output-box" id="pg-output">' + prompt.replace(/</g,'&lt;') + '</div>';
  result.innerHTML += '<div style="display:flex;gap:10px;margin-top:12px;flex-wrap:wrap;">';
  result.innerHTML += '<button class="copy-btn" onclick="pgCopy()" id="copy-btn">📋 复制</button>';
  result.innerHTML += '<a href="https://twitter.com/intent/tweet?text=' + encodeURIComponent(prompt) + '&url=' + encodeURIComponent('https://www.ggouai.top/prompt-generator/') + '" target="_blank" rel="noopener" class="share-btn" style="background:#000;">🐦 发 X</a>';
  result.innerHTML += '<a href="https://t.me/share/url?url=' + encodeURIComponent('https://www.ggouai.top/prompt-generator/') + '&text=' + encodeURIComponent('我用这个 Prompt 生成器造出了超棒的提示词：' + prompt.slice(0,100)) + '" target="_blank" rel="noopener" class="share-btn" style="background:#0088cc;">✈️ 发 Telegram</a>';
  result.innerHTML += '</div>';
  result.scrollIntoView({behavior:'smooth'});
}

function pgCopy(){
  var text = document.getElementById('pg-output').textContent;
  navigator.clipboard.writeText(text).then(function(){
    var btn = document.getElementById('copy-btn');
    btn.textContent = '✓ 已复制';
    setTimeout(function(){ btn.textContent = '📋 复制'; }, 2000);
  }).catch(function(){
    // 降级：手动选中文本
    var range = document.createRange();
    range.selectNode(document.getElementById('pg-output'));
    window.getSelection().removeAllRanges();
    window.getSelection().addRange(range);
    var btn = document.getElementById('copy-btn');
    btn.textContent = '已选中，按 Ctrl+C';
  });
}
</script>
</body>
</html>
PG_HTML;
    
    $html = str_replace('@@JSONLD@@', $jsonld_safe, $html);
    $html = str_replace('@@PERMALINK@@', $permalink, $html);
    $html = str_replace('@@TITLE@@', $title_escaped, $html);
    $html = str_replace('@@DESC@@', $desc_escaped, $html);
    
    return $html;
}

// =====================================================
// AJAX 处理器：AI 决策器提交
// =====================================================
add_action('wp_ajax_ggouai_decision_maker_submit', 'ggouai_handle_decision_maker_submit');
add_action('wp_ajax_nopriv_ggouai_decision_maker_submit', 'ggouai_handle_decision_maker_submit');

function ggouai_handle_decision_maker_submit(){
    global $wpdb;
    
    $email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
    $answers_raw = isset($_POST['answers']) ? sanitize_text_field($_POST['answers']) : '{}';
    
    if(empty($email) || !is_email($email)){
        wp_send_json_error('Invalid email.');
    }
    
    $answers = json_decode($answers_raw, true);
    if(!is_array($answers)) $answers = [];
    
    // 建表
    $wpdb->query("
    CREATE TABLE IF NOT EXISTS {$wpdb->prefix}decision_maker_answers (
      id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
      email VARCHAR(200) NOT NULL,
      answers LONGTEXT,
      created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
      INDEX (email)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");
    
    $wpdb->insert($wpdb->prefix . 'decision_maker_answers', [
        'email' => $email,
        'answers' => json_encode($answers),
    ], ['%s','%s']);
    
    // 也加入邮件订阅表
    $wpdb->query("
    CREATE TABLE IF NOT EXISTS {$wpdb->prefix}email_subscribers (
      id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
      email VARCHAR(200) NOT NULL UNIQUE,
      page_source VARCHAR(100) DEFAULT '',
      created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
      subscribed_products TINYINT(1) DEFAULT 0,
      INDEX (email)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");
    $wpdb->replace($wpdb->prefix . 'email_subscribers', [
        'email' => $email,
        'page_source' => 'decision_maker',
    ], ['%s','%s']);
    
    // 构建决策报告邮件
    $ranked = ggouai_compute_dm_match($answers);
    $top3 = array_slice($ranked, 0, 3);
    $email_body = ggouai_build_dm_report_email($email, $top3, $answers);
    
    $subject = '🎯 你的 AI 变现 Top 3 推荐报告';
    
    // 用 agently-cli 邮件桥替代 wp_mail（wp_mail 本机没 SMTP 发不出）
    $bridge = 'E:\\hermes\\scripts\\ggouai-mail-bridge.py';
    $python = 'E:\\hermes\\venv\\Scripts\\python.exe';
    if(file_exists($bridge) && file_exists($python)){
        // body 写临时文件避免 shell 转义
        $tmpBody = tempnam(sys_get_temp_dir(), 'ggo');
        file_put_contents($tmpBody, $email_body);
        $cmd = escapeshellarg($python) . ' ' . escapeshellarg($bridge) .
               ' --to ' . escapeshellarg($email) .
               ' --subject ' . escapeshellarg($subject) .
               ' --body-file ' . escapeshellarg($tmpBody);
        if(DIRECTORY_SEPARATOR === '\\'){
            $cmd = str_replace('.cmd', '.cmd', $cmd);
        }
        @exec($cmd . ' 2>&1', $output, $rc);
        // 异步写日志
        $logEntry = json_encode([
            'ts'=>date('c'), 'email'=>$email, 'rc'=>$rc,
            'output'=>implode("\n", array_slice($output,0,3)),
        ]);
        file_put_contents('E:\\hermes\\scripts\\dm_mail.log', $logEntry . "\n", FILE_APPEND);
        @unlink($tmpBody);
    }
    
    wp_send_json_success('');
}

function ggouai_compute_dm_match($answers){
    $products = [
        'prompt'    => ['slug'=>'xgnhru','title'=>'Chinese Prompt Mastery Pack','price'=>'$19','color'=>'#ff0069','icon'=>'📝','tags'=>['content','daily','efficient','writing','beginner']],
        'agent'     => ['slug'=>'dyrvmg','title'=>'Agent Self-Evolution Guide','price'=>'$17','color'=>'#00e5d1','icon'=>'🤖','tags'=>['tech','build','automate','expert','scaling']],
        'polish'    => ['slug'=>'zpjabq','title'=>'Chinese-to-English Polishing','price'=>'$15','color'=>'#ff6b9d','icon'=>'✍️','tags'=>['writing','daily','efficient','professional','balanced']],
        'overseas'  => ['slug'=>'urbhcq','title'=>'AI Overseas Methodology','price'=>'$14','color'=>'#00d4ff','icon'=>'🌍','tags'=>['business','monetize','scale','aggressive','expert']],
        'tools'     => ['slug'=>'apjvst','title'=>'AI Tools Practical Guide','price'=>'$12','color'=>'#ff69b4','icon'=>'🚀','tags'=>['beginner','daily','efficient','writing','balanced']],
    ];
    $questions = [
        [['content','daily','efficient','writing','beginner'],['tech','build','automate','expert','scaling'],['writing','daily','efficient','professional','balanced'],['business','monetize','scale','aggressive','expert'],['beginner','daily','efficient','writing','balanced']],
        [['beginner','efficient'],['daily','balanced'],['daily','professional'],['expert','scaling','scale']],
        [['efficient','daily'],['build','automate'],['business','monetize','scale'],['beginner','efficient']],
        [['balanced'],['tools_pref'],['balanced'],['expert','scale']],
        [['beginner','balanced'],['daily','balanced'],['expert','monetize'],['aggressive','scale','expert']],
    ];
    
    $user_tags = [];
    foreach($answers as $qi=>$oi){
        $qi = intval($qi); $oi = intval($oi);
        if($qi < 5 && $oi < 5 && isset($questions[$qi][$oi])){
            $user_tags = array_merge($user_tags, $questions[$qi][$oi]);
        }
    }
    
    $scores = [];
    foreach($products as $id=>$p){
        $s = 0;
        foreach($p['tags'] as $tag){
            if($tag === 'tools_pref') continue;
            $hits = count(array_intersect([$tag], $user_tags));
            $s += $hits * 2;
        }
        $scores[$id] = $s;
    }
    if(isset($answers[3]) && $answers[3] === '1'){
        $scores['tools'] += 3;
        $scores['overseas'] += 2;
    }
    
    arsort($scores);
    $ranked = [];
    foreach($scores as $id=>$s){
        $p = $products[$id];
        $ranked[] = array_merge($p, ['score'=>$s]);
    }
    return $ranked;
}

function ggouai_build_dm_report_email($email, $top3, $answers){
    $lines = [];
    $lines[] = "Hi! 这是你刚才在 AI 变现决策器上生成的 Top 3 推荐报告。\n\n";
    $lines[] = "=== 你的 Top 3 AI 产品推荐 ===\n\n";
    foreach($top3 as $i=>$item){
        $lines[] = "第 " . ($i+1) . " 名 " . $item['icon'] . " " . $item['title'];
        $lines[] = "价格: " . $item['price'];
        $lines[] = "购买: https://scgkagent.gumroad.com/l/" . $item['slug'] . "?utm_source=email&utm_medium=decision_maker&utm_campaign=dm_email\n\n";
    }
    $lines[] = "=== 后续 ===\n\n";
    $lines[] = "3 天后我们会再发一封邮件，告诉你：\n";
    $lines[] = "👉 同样问题的用户最终买了哪个产品\n";
    $lines[] = "👉 你错过了什么（未推荐的 2 个产品的适用场景）\n\n";
    $lines[] = "=== 现在也可以 ===\n\n";
    $lines[] = "→ 完整对比 5 个产品: https://www.ggouai.top/compare-ai-products/\n";
    $lines[] = "→ 免费拿 50+ 提示词: https://www.ggouai.top/free-prompts/\n";
    $lines[] = "→ 购买 FAQ: https://www.ggouai.top/buy-faq/\n\n";
    $lines[] = "所有产品 30 天无理由退款保证。\n\n";
    $lines[] = "— ggouai.top Team";
    return implode("\n", $lines);
}

// =====================================================
// AJAX 处理器：Free Prompt Bait 邮箱提交
// =====================================================
add_action('wp_ajax_ggouai_free_bait_submit', 'ggouai_handle_free_bait_submit');
add_action('wp_ajax_nopriv_ggouai_free_bait_submit', 'ggouai_handle_free_bait_submit');

function ggouai_handle_free_bait_submit(){
    global $wpdb;
    
    $email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
    $page = isset($_POST['page_source']) ? sanitize_text_field($_POST['page_source']) : '';
    
    if(empty($email) || !is_email($email)){
        wp_send_json_error('Invalid email address.');
    }
    
    // 检查是否已订阅
    $exists = $wpdb->get_var($wpdb->prepare(
        "SELECT id FROM {$wpdb->prefix}email_subscribers WHERE email = %s", $email
    ));
    
    if($exists){
        // 已订阅，返回成功但提示重复
        wp_send_json_success('You already subscribed. Check your inbox!');
    }
    
    // 插入新订阅
    $inserted = $wpdb->insert($wpdb->prefix . 'email_subscribers', [
        'email' => $email,
        'page_source' => $page,
    ], ['%s', '%s']);
    
    if($inserted === false){
        wp_send_json_error('Database error. Try again.');
    }
    
    // 触发通知邮件（用 wp_mail 或 agent-mail）
    $welcome = ggouai_build_prompt_pack_email();
    $subject = '🎁 Your 50+ Free AI Prompt Pack';
    $headers = ['From: ggouai.top <no-reply@ggouai.top>'];
    $sent = @wp_mail($email, $subject, $welcome, $headers);
    
    // 同时用 Agent Mail 备份发送（scgk8618@agent.qq.com）
    if(function_exists('shell_exec')){
        @shell_exec("agently-cli send --to {$email} --subject '" . escapeshellarg($subject) . "' --body '" . escapeshellarg(substr($welcome, 0, 1800)) . "' 2>/dev/null");
    }
    
    wp_send_json_success('');
}

function ggouai_build_prompt_pack_email(){
    return <<<EMAIL_BODY
Hi! Here's your free AI prompt pack. 🎁

=== 50+ PROMPTS FOR CHATGPT / CLAUDE / DEEPSEEK ===

📝 CONTENT CREATION (15)
1. Write a 500-word blog post about [topic] for a beginner audience. Include an H2 heading structure and 3 practical tips.
2. Turn this paragraph into a LinkedIn post with 3 line breaks and a call-to-action: [paste content]
3. Generate 5 tweet threads (7 tweets each) about [topic] with a hook in tweet 1.
4. Rewrite [content] in a [tone: playful/professional/poetic] tone.
5. Create a comparison table between [Product A] and [Product B] covering price, features, and best-use-case.
6. Write a product description for [product] targeting [audience] with a 40-word limit.
7. Generate 10 SEO-friendly titles under 60 characters for [topic].
8. Write an email sequence (5 emails over 14 days) for a [type] webinar launch.
9. Turn this outline into a full blog post: [outline].
10. Write a customer testimonial in first person for [product/service].
11. Summarize this 2000-word document into 5 bullet points: [paste text]
12. Generate 20 H2/H3 subheadings for a blog post about [topic].
13. Rewrite [text] as if explaining to a 12-year-old (ELI5 style).
14. Create a FAQ (10 Q&A pairs) for [product/topic].
15. Draft a press release for [company] announcing [event/product].

🎨 CREATIVE WRITING (10)
16. Write a 300-word opening scene for a [genre] story about [premise].
17. Create a character profile: [name/age/occupation/background conflict].
18. Generate 5 plot twists for a [genre] story about [premise].
19. Write a dialogue between [Character A] and [Character B] discussing [topic].
20. Describe [scene] using only the 5 senses, no dialogue.
21. Write a poem (haiku/sonnet/free verse) about [theme].
22. Create a world-building bible for [genre] setting.
23. Write an epistolary short story (letters, texts, diary entries).
24. Generate 10 names for [type: character/place/brand].
25. Write a scene where [Character A] must deliver [bad news] to [Character B].

💼 BUSINESS (10)
26. Write a cold email to [type] at a company, referencing [specific trigger].
27. Create a project proposal for [project] with timeline and budget.
28. Write a resignation letter with 30-day notice, keeping the relationship intact.
29. Draft a follow-up email after [meeting/event], referencing [key point].
30. Create a 90-day OKR document for a [team type].
31. Write a job description for [role] at a [company stage].
32. Draft a customer complaint response that de-escalates and offers [solution].
33. Create a pitch deck outline (10 slides) for a [product/company].
34. Write a partnership proposal to [company] with mutual value proposition.
35. Draft an internal memo announcing [change] to all-hands.

🔍 ANALYSIS (10)
36. Break down [complex concept] into 5 levels of understanding (novice → expert).
37. What are the strongest and weakest arguments for [debate topic]?
38. SWOT analysis for [company/product].
39. Identify 5 risks in this plan and suggest mitigations: [paste plan].
40. Compare [method A] vs [method B] with pros, cons, and use-cases.
41. What's the counterargument to [common claim]?
42. List 10 assumptions in this reasoning chain: [paste reasoning].
43. Extract the key insights from this research: [paste data].
44. What questions should I ask in [interview/meeting] to make it productive?
45. Write a 1-page executive summary of [long document].

⚡ META-PROMPTS (5)
46. "You are a prompt engineer. Here's my current prompt: [paste prompt]. Improve it by adding missing context, clearer instructions, examples, and evaluation criteria."
47. "Before answering, list the 3 most important questions a client should ask about this. Then answer my question: [your question]."
48. "Rewrite my prompt using CO-STAR framework: [paste prompt]"
49. "Simulate a skeptical expert reviewing my plan. Identify 3 fatal flaws and 3 quick wins: [paste plan]."
50. "Generate 3 variations of this prompt, one for each AI model (ChatGPT, Claude, DeepSeek), tuned to their strengths: [paste prompt]"

=== END OF PACK ===

Want the full 200+ prompt pack with frameworks (CO-STAR, RTF, RACE)?
→ https://scgkagent.gumroad.com/l/xgnhru?utm_source=email&utm_medium=freebait&utm_campaign=full_pack

Want to build AI agents that self-improve?
→ https://scgkagent.gumroad.com/l/dyrvmg?utm_source=email

Thanks!
— ggouai.top Team

EMAIL_BODY;
}

// =====================================================
// 全站社交分享按钮（除首页外所有文章/页面底部）
// =====================================================
add_action('wp_footer', 'ggouai_inject_share_buttons', 20);

function ggouai_inject_share_buttons(){
    global $post;
    if(!is_a($post, 'WP_Post')) return;
    $uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
    $path = parse_url($uri, PHP_URL_PATH) ?: '/';
    if($path === '/' || preg_match('#^/page/\d+/?$#', $path)) return;
    if($post->post_type === 'attachment') return;

    $url   = get_permalink($post->ID);
    $title = urlencode(get_the_title($post->ID));
    $raw   = urlencode($url);

    $tpl = <<<SHARE_HTML
<!-- SHARE START -->
<div style="background:#f9f9fb;padding:20px 24px;border-radius:10px;border:1px solid #eee;margin:32px 0;">
<p style="margin:0 0 12px 0;font-size:1.05em;color:#333;">🔗 分享这篇文章</p>
<div style="display:flex;flex-wrap:wrap;gap:10px;">
SHARE_HTML;

    $buttons = [
        ['name' => 'X / Twitter', 'color' => '#1a1a1a', 'href' => "https://twitter.com/intent/tweet?url={$raw}&text={$title}", 'icon' => '𝕏'],
        ['name' => 'Facebook',    'color' => '#1877f2', 'href' => "https://www.facebook.com/sharer/sharer.php?u={$raw}", 'icon' => 'f'],
        ['name' => 'LinkedIn',    'color' => '#0a66c2', 'href' => "https://www.linkedin.com/sharing/share-offsite/?url={$raw}", 'icon' => 'in'],
        ['name' => 'WhatsApp',    'color' => '#25d366', 'href' => "https://wa.me/?text={$title}%20%0A%0A{$raw}", 'icon' => '💬'],
        ['name' => 'Reddit',      'color' => '#ff4500', 'href' => "https://www.reddit.com/submit?url={$raw}&title={$title}", 'icon' => '🤖'],
        ['name' => 'Telegram',    'color' => '#0088cc', 'href' => "https://t.me/share/url?url={$raw}&text={$title}", 'icon' => '✈️'],
    ];

    foreach($buttons as $b){
        $tpl .= '<a href="' . $b['href'] . '" target="_blank" rel="noopener" style="display:inline-flex;align-items:center;gap:6px;background:' . $b['color'] . ';color:#fff;padding:8px 14px;border-radius:6px;text-decoration:none;font-size:0.9em;font-weight:bold;">' . $b['icon'] . ' ' . $b['name'] . '</a>';
    }

    $tpl .= <<<SHARE_HTML
</div>
</div>
<!-- SHARE END -->
SHARE_HTML;

    echo $tpl;
}

// =====================================================
// 5 篇产品深度文底部固定 "Buy Now" 卡片
// 用户滚到文章末尾时会看到醒目的购买 CTA
// =====================================================
add_action('wp_footer', 'ggouai_inject_floating_buy', 30);

function ggouai_inject_floating_buy(){
    global $post;
    if(!is_a($post, 'WP_Post')) return;
    $uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
    $path = parse_url($uri, PHP_URL_PATH) ?: '/';
    if($path === '/' || preg_match('#^/page/\d+/?$#', $path)) return;

    $map = ggouai_product_map();
    if(!isset($map[$post->ID])) return;
    $p = $map[$post->ID];
    $buy_url  = 'https://scgkagent.gumroad.com/l/' . $p['slug'] . '?utm_source=ggouai.top&utm_medium=deep_dive&utm_campaign=buy_now';
    $buy_url_e = urlencode($buy_url);

    $tpl = <<<BUY_HTML
<!-- BUY NOW FLOATING START -->
<div style="position:fixed;bottom:20px;right:20px;max-width:340px;background:#fff;border:2px solid {$p['color']};border-radius:14px;padding:16px 18px;box-shadow:0 12px 40px rgba(0,0,0,0.22);z-index:9999;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">
<div style="display:flex;align-items:flex-start;gap:8px;margin-bottom:10px;">
<div style="flex:1;">
<div style="font-size:1.1em;font-weight:bold;color:#333;line-height:1.3;margin:0 0 4px 0;">{$p['title']}</div>
<div style="color:#666;font-size:0.85em;margin:0 0 6px 0;">{$p['blurb']}</div>
<div style="font-size:1.5em;font-weight:bold;color:{$p['color']};margin:0;">{$p['price']}</div>
</div>
<button onclick="this.parentElement.parentElement.style.display='none'" style="background:transparent;border:none;color:#999;font-size:1.4em;cursor:pointer;padding:0 4px;line-height:1;" title="Close">×</button>
</div>
<a href="{$buy_url}" target="_blank" rel="nofollow noopener" style="display:block;background:{$p['color']};color:#fff;text-align:center;padding:12px 20px;border-radius:8px;text-decoration:none;font-weight:bold;font-size:1.05em;">🛒 Buy Now — Instant Download</a>
<p style="margin:8px 0 0 0;color:#999;font-size:0.75em;text-align:center;">30-day money-back · Lifetime updates</p>
</div>
<!-- BUY NOW FLOATING END -->
BUY_HTML;

    echo $tpl;
}

// =====================================================
// 首页产品专区
// =====================================================
add_action('wp_footer', 'ggouai_inject_home_products', 10);
function ggouai_inject_home_products(){
    $uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/';
    $path = parse_url($uri, PHP_URL_PATH) ?: '/';
    if($path !== '/' && !preg_match('#^/page/\d+/?$#', $path)) return;

    echo '<!-- HOME PRODUCTS START -->' . "\n";
    echo '<section style="background:linear-gradient(135deg,#fff4f9 0%,#fff 100%);padding:48px 20px;margin:40px 0;border-top:4px solid #ff0069;border-bottom:4px solid #ff0069;">' . "\n";
    echo '<div style="max-width:1200px;margin:0 auto;">' . "\n";
    echo '<h2 style="font-size:2em;margin:0 0 8px 0;color:#333;text-align:center;">🛍️ AI Tools & Products</h2>' . "\n";
    echo '<p style="color:#666;text-align:center;margin:0 0 24px 0;">One-time purchase · Lifetime access · Works with any AI</p>' . "\n";
    echo '<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:20px;">' . "\n";

    foreach(ggouai_product_map() as $p){
        echo '<div style="background:#fff;padding:20px;border-radius:10px;box-shadow:0 4px 12px rgba(0,0,0,0.08);border-top:4px solid ' . $p['color'] . ';">' . "\n";
        echo '<h3 style="margin:0 0 8px 0;font-size:1.1em;color:#333;">' . esc_html($p['title']) . '</h3>' . "\n";
        echo '<p style="margin:0 0 12px 0;font-size:0.9em;color:#666;">' . esc_html($p['blurb']) . '</p>' . "\n";
        echo '<p style="margin:0 0 12px 0;font-size:1.4em;font-weight:bold;color:' . $p['color'] . ';">' . esc_html($p['price']) . '</p>' . "\n";
        echo '<a href="https://scgkagent.gumroad.com/l/' . $p['slug'] . '?utm_source=ggouai.top&utm_medium=home&utm_campaign=home_products" target="_blank" rel="nofollow noopener" style="display:inline-block;background:' . $p['color'] . ';color:#fff;padding:10px 20px;text-decoration:none;border-radius:6px;font-weight:bold;">Buy →</a>' . "\n";
        echo '</div>' . "\n";
    }

    echo '</div>' . "\n";
    echo '<p style="text-align:center;margin-top:24px;"><a href="' . home_url('/ai-decision-maker/?utm_source=ggouai.top&utm_medium=home&utm_campaign=home_dm') . '" style="color:#ff0069;font-weight:bold;text-decoration:none;">🎯 AI 决策器 →</a> · <a href="' . home_url('/ai-roi-calculator/?utm_source=ggouai.top&utm_medium=home&utm_campaign=home_roi') . '" style="color:#ff0069;font-weight:bold;text-decoration:none;">💰 ROI 计算器 →</a> · <a href="' . home_url('/prompt-generator/?utm_source=ggouai.top&utm_medium=home&utm_campaign=home_pg') . '" style="color:#ff0069;font-weight:bold;text-decoration:none;">✨ Prompt 生成器 →</a> · <a href="' . home_url('/products/?utm_source=ggouai.top&utm_medium=home&utm_campaign=home_footer') . '" style="color:#ff0069;font-weight:bold;text-decoration:none;">See all products →</a> · <a href="' . home_url('/compare-ai-products/?utm_source=ggouai.top&utm_medium=home&utm_campaign=home_compare') . '" style="color:#ff0069;font-weight:bold;text-decoration:none;">Compare 5 guides →</a> · <a href="' . home_url('/free-prompts/?utm_source=ggouai.top&utm_medium=home&utm_campaign=home_free_bait') . '" style="color:#ff0069;font-weight:bold;text-decoration:none;">🎁 Free 50+ Prompts →</a> · <a href="' . home_url('/buy-faq/?utm_source=ggouai.top&utm_medium=home&utm_campaign=home_faq') . '" style="color:#ff0069;font-weight:bold;text-decoration:none;">Buying FAQ →</a></p>' . "\n";
    echo '</div>' . "\n";
    echo '</section>' . "\n";
    echo '<!-- HOME PRODUCTS END -->' . "\n";
}

// =====================================================
// 首页 head meta 覆盖
// =====================================================
add_action('wp_head', 'ggouai_inject_home_meta', 1000);
function ggouai_inject_home_meta(){
    if(!isset($_SERVER['HTTP_HOST'])) return;
    $current_url = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
    $path = parse_url($current_url, PHP_URL_PATH) ?: '/';
    if($path !== '/' && !preg_match('#^/page/\d+/?$#', $path)) return;

    remove_action('wp_head', 'rel_canonical');
    remove_action('wp_head', 'wp_generator');

    $blogname = get_option('blogname');
    $blodesc  = get_option('blogdescription');
    $site_title = empty($blodesc) ? $blogname : $blogname . ' — ' . $blodesc;
    $home_url = trailingslashit(get_option('siteurl'));
    echo '<meta name="description" content="' . esc_attr($site_title) . '" />' . "\n";
    echo '<link rel="canonical" href="' . esc_url($home_url) . '" />' . "\n";
    echo '<meta property="og:type" content="website" />' . "\n";
    echo '<meta property="og:title" content="' . esc_attr($blogname) . '" />' . "\n";
    echo '<meta property="og:description" content="' . esc_attr($blodesc) . '" />' . "\n";
    echo '<meta property="og:url" content="' . esc_url($home_url) . '" />' . "\n";
    echo '<meta property="og:site_name" content="' . esc_attr($blogname) . '" />' . "\n";
    $hero = 'https://www.ggouai.top/wp-content/uploads/og-covers/prompt_pack.png';
    echo '<meta property="og:image" content="' . esc_url($hero) . '" />' . "\n";
    echo '<meta property="og:image:width" content="1200" />' . "\n";
    echo '<meta property="og:image:height" content="630" />' . "\n";
    echo '<meta name="twitter:card" content="summary_large_image" />' . "\n";
    echo '<meta name="twitter:image" content="' . esc_url($hero) . '" />' . "\n";
}
