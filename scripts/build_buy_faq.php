<?php
/**
 * build_buy_faq.php
 * 购买 FAQ 页 - 消除购买决策阻力
 * 每篇产品深度文都链回这一页
 */
define('WP_USE_THEMES', false);
require_once 'E:\\xampp\\htdocs\\wp-load.php';
global $wpdb;

$slug = 'buy-faq';
$existing = get_page_by_path($slug);
if($existing){
    echo "[SKIP] already exists (ID{$existing->ID})\n";
    exit;
}

$title = 'Buying Guide & FAQ — Everything Before You Checkout';
$desc = 'How payment works, refunds, delivery, what you actually get. 30-day money-back guarantee on all 5 products.';

$permalink_placeholder = 'https://www.ggouai.top/buy-faq/';

$body = <<<'BODY_HTML'
<h2 style="font-size:1.6em;margin:32px 0 12px 0;color:#333;">📦 What happens after you buy?</h2>
<ol style="font-size:1.05em;line-height:1.8;">
<li><strong>Instant delivery</strong> — After you complete payment on <a href="https://scgkagent.gumroad.com" target="_blank" rel="noopener">scgkagent.gumroad.com</a>, you get an email within 30 seconds with a download link.</li>
<li><strong>Library access</strong> — Log into Gumroad → <a href="https://gumroad.com/library" target="_blank" rel="noopener">your Library</a> → all your purchased products in one place. Download anytime.</li>
<li><strong>Updates included</strong> — Every product ships with <strong>lifetime updates</strong>. When we improve the content, you get notified by email with a new download link — no extra charge.</li>
<li><strong>Lifetime access</strong> — One-time payment, permanent access. No subscription, no renewal.</li>
</ol>

<h2 style="font-size:1.6em;margin:32px 0 12px 0;color:#333;">💳 Accepted payment methods</h2>
<ul style="font-size:1.05em;line-height:1.8;">
<li>Credit / debit card (Visa, Mastercard, American Express, Discover)</li>
<li>PayPal</li>
<li>Apple Pay / Google Pay</li>
<li>Purchase from <strong>anywhere in the world</strong> — Gumroad accepts USD globally</li>
</ul>

<h2 style="font-size:1.6em;margin:32px 0 12px 0;color:#333;">🔄 30-day money-back guarantee</h2>
<p style="background:#fff4f9;padding:16px 20px;border-left:4px solid #ff0069;border-radius:6px;">
<strong>No questions asked.</strong> If you purchase and the product doesn't deliver what you expected, email us within 30 days for a full refund. No survey, no hoops, no explanation required.
</p>
<p>Email: <strong><a href="mailto:scgk8618@agent.qq.com">scgk8618@agent.qq.com</a></strong> — reply within 24 hours.</p>

<h2 style="font-size:1.6em;margin:32px 0 12px 0;color:#333;">📄 What format do you get?</h2>
<p>All 5 products include:</p>
<ul style="line-height:1.8;">
<li><strong>PDF</strong> — the main document, print-friendly, offline-readable</li>
<li><strong>Markdown / plain text</strong> — for import into Notion, Obsidian, ChatGPT, or your editor</li>
<li><strong>Copy-paste ready templates</strong> — prompts you can paste directly into any AI</li>
</ul>

<h2 style="font-size:1.6em;margin:32px 0 12px 0;color:#333;">🔒 Is it a personal license?</h2>
<p>Yes. One license = one person. You can use it for your personal projects, your job, your business, your students, whatever — but you can't resell, redistribute, or publish the content as your own.</p>
<p>Full terms: <a href="https://www.ggouai.top/terms/">Terms of Service</a></p>

<h2 style="font-size:1.6em;margin:32px 0 12px 0;color:#333;">❓ Still have questions?</h2>
<p>Email <a href="mailto:scgk8618@agent.qq.com">scgk8618@agent.qq.com</a> and I'll reply personally within 24 hours.</p>

<hr style="margin:40px 0;"/>

<h2 style="text-align:center;color:#333;">🛒 Ready to pick one?</h2>
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:16px;margin:24px 0;">
<div style="text-align:center;padding:20px;background:#fff;border:2px solid #ff0069;border-radius:10px;">
<div style="font-weight:bold;color:#ff0069;">Chinese Prompt Mastery</div>
<div style="font-size:1.5em;color:#ff0069;margin:8px 0;">$19</div>
<a href="https://scgkagent.gumroad.com/l/xgnhru?utm_source=ggouai.top&utm_medium=buyfaq&utm_campaign=buyfaq_cta" target="_blank" rel="nofollow noopener" style="display:inline-block;background:#ff0069;color:#fff;padding:10px 20px;border-radius:6px;text-decoration:none;font-weight:bold;">Buy →</a>
</div>
<div style="text-align:center;padding:20px;background:#fff;border:2px solid #00e5d1;border-radius:10px;">
<div style="font-weight:bold;color:#00a597;">Agent Self-Evolution</div>
<div style="font-size:1.5em;color:#00a597;margin:8px 0;">$17</div>
<a href="https://scgkagent.gumroad.com/l/dyrvmg?utm_source=ggouai.top&utm_medium=buyfaq&utm_campaign=buyfaq_cta" target="_blank" rel="nofollow noopener" style="display:inline-block;background:#00e5d1;color:#fff;padding:10px 20px;border-radius:6px;text-decoration:none;font-weight:bold;">Buy →</a>
</div>
<div style="text-align:center;padding:20px;background:#fff;border:2px solid #ff6b9d;border-radius:10px;">
<div style="font-weight:bold;color:#c5375f;">CN → EN Polishing</div>
<div style="font-size:1.5em;color:#c5375f;margin:8px 0;">$15</div>
<a href="https://scgkagent.gumroad.com/l/zpjabq?utm_source=ggouai.top&utm_medium=buyfaq&utm_campaign=buyfaq_cta" target="_blank" rel="nofollow noopener" style="display:inline-block;background:#ff6b9d;color:#fff;padding:10px 20px;border-radius:6px;text-decoration:none;font-weight:bold;">Buy →</a>
</div>
<div style="text-align:center;padding:20px;background:#fff;border:2px solid #00d4ff;border-radius:10px;">
<div style="font-weight:bold;color:#008ab0;">AI Overseas Method</div>
<div style="font-size:1.5em;color:#008ab0;margin:8px 0;">$14</div>
<a href="https://scgkagent.gumroad.com/l/urbhcq?utm_source=ggouai.top&utm_medium=buyfaq&utm_campaign=buyfaq_cta" target="_blank" rel="nofollow noopener" style="display:inline-block;background:#00d4ff;color:#fff;padding:10px 20px;border-radius:6px;text-decoration:none;font-weight:bold;">Buy →</a>
</div>
<div style="text-align:center;padding:20px;background:#fff;border:2px solid #ff69b4;border-radius:10px;">
<div style="font-weight:bold;color:#c43a7a;">AI Tools Practical</div>
<div style="font-size:1.5em;color:#c43a7a;margin:8px 0;">$12</div>
<a href="https://scgkagent.gumroad.com/l/apjvst?utm_source=ggouai.top&utm_medium=buyfaq&utm_campaign=buyfaq_cta" target="_blank" rel="nofollow noopener" style="display:inline-block;background:#ff69b4;color:#fff;padding:10px 20px;border-radius:6px;text-decoration:none;font-weight:bold;">Buy →</a>
</div>
</div>
<p style="text-align:center;color:#666;">Not sure which one? Read the <a href="https://www.ggouai.top/2026/09/05/choosing-right-ai-product-5-guides-compared/">decision matrix →</a></p>
BODY_HTML;

$postarr = [
    'post_title'   => $title,
    'post_name'    => $slug,
    'post_content' => $body,
    'post_status'  => 'publish',
    'post_type'    => 'page',
    'post_author'  => 1,
    'post_excerpt' => $desc,
];
$pid = wp_insert_post($postarr, false);
if(is_wp_error($pid)){
    echo "[FAIL] {$pid->get_error_message()}\n";
    exit;
}

$permalink = get_permalink($pid);
$meta = [
    '_rank_math_title'      => $title,
    '_rank_math_desc'       => $desc,
    '_rank_math_focus_keyword' => 'gumroad buy guide faq',
    '_rank_math_og_title'   => $title,
    '_rank_math_og_desc'    => $desc,
    '_rank_math_og_type'    => 'website',
    '_rank_math_og_url'     => $permalink,
    '_rank_math_og_site_name' => get_bloginfo('name'),
];
foreach($meta as $k=>$v){
    $wpdb->delete('wp_postmeta', ['post_id'=>$pid, 'meta_key'=>$k]);
    $wpdb->insert('wp_postmeta', ['post_id'=>$pid, 'meta_key'=>$k, 'meta_value'=>$v]);
}

// JSON-LD FAQPage schema
$jsonld = [
    '@context' => 'https://schema.org',
    '@type' => 'FAQPage',
    'mainEntity' => [
        [
            '@type' => 'Question',
            'name' => 'What format do I get after purchasing?',
            'acceptedAnswer' => [
                '@type' => 'Answer',
                'text' => 'All 5 products come as PDF (main document) plus Markdown / plain text (for Notion, Obsidian, ChatGPT). Every prompt is copy-paste ready — you can paste it into any AI tool immediately.'
            ]
        ],
        [
            '@type' => 'Question',
            'name' => 'Is there a money-back guarantee?',
            'acceptedAnswer' => [
                '@type' => 'Answer',
                'text' => 'Yes. 30-day money-back guarantee on all products. If it doesn\'t deliver, email scgk8618@agent.qq.com within 30 days for a full refund. No questions asked.'
            ]
        ],
        [
            '@type' => 'Question',
            'name' => 'Do I get updates for free?',
            'acceptedAnswer' => [
                '@type' => 'Answer',
                'text' => 'Yes. Lifetime updates included. When the content is improved, you get notified by email with a new download link — no extra charge, ever.'
            ]
        ],
        [
            '@type' => 'Question',
            'name' => 'What payment methods are accepted?',
            'acceptedAnswer' => [
                '@type' => 'Answer',
                'text' => 'Credit / debit cards (Visa, Mastercard, Amex), PayPal, Apple Pay, Google Pay. Payments processed by Gumroad in USD — accepted worldwide.'
            ]
        ],
        [
            '@type' => 'Question',
            'name' => 'Can I use the content commercially?',
            'acceptedAnswer' => [
                '@type' => 'Answer',
                'text' => 'Yes. Personal + commercial use is included in the license. You can\'t resell, redistribute, or publish the raw content as your own, but you can absolutely use it in your business, courses, videos, and services.'
            ]
        ]
    ]
];
$json_str = json_encode($jsonld, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_APOS | JSON_HEX_QUOT);
$wpdb->insert('wp_postmeta', ['post_id'=>$pid, 'meta_key'=>'_ggouai_jsonld_faq', 'meta_value'=>$json_str]);

echo "[OK] ID$pid: $permalink\n";

// 清缓存
if(function_exists('wp_cache_flush')) wp_cache_flush();
$cache_dir = 'E:\\xampp\\htdocs\\wp-content\\cache';
if(is_dir($cache_dir)){
    $rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($cache_dir), RecursiveIteratorIterator::CHILD_FIRST);
    foreach($rii as $f){ if($f->isFile()) unlink($f->getPathname()); }
}
