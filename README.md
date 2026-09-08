# ggouai-tools
<<<<<<< HEAD
Free AI productivity tools for content creators - Decision Maker, ROI Calculator, Prompt Generator (WordPress plugins, MIT licensed)
=======

ggouai.top 变现工具箱 — 3 个免费 AI 生产工具 + 邮件桥 + SEO 插件

## 工具

### 1. AI 决策器 `/ai-decision-maker/`
5 个单选题 → 30 秒内匹配 Top 3 产品，发邮件报告。
- **场景**：帮用户在 N 个产品里挑合适的
- **架构**：WordPress plugin + AJAX + 邮件桥
- **技术**：PHP + WordPress REST + 纯前端 JS（无框架）

### 2. AI ROI 计算器 `/ai-roi-calculator/`
3 滑块（时薪 / 每周时间 / 任务类型）→ 算年度节省 + ROI + 回本时间。
- **场景**：让用户算清楚 AI 工具值不值得买
- **架构**：纯前端 JS 实时计算

### 3. Prompt 生成器 `/prompt-generator/`
8 场景 × 6 语气 × 3 详细度 × 任务描述 → 生成结构化 prompt，一键复制 + 分享。
- **场景**：非技术用户想写好 prompt 但不知道怎么写
- **架构**：纯前端 JS 模板拼接

## 邮件桥
`scripts/ggouai-mail-bridge.py` — 因为 wp_mail 无 SMTP 发不出，改用 Agent Mail CLI（QQ 邮箱 API）做桥。
- 每日配额 50 条，够 drip 用
- Windows/Linux 兼容

## 邮件 Drip
`scripts/send_drip_email.php` — 订阅 3 天后发未推荐产品 + 20% 折扣码，4 天再发决策器/ROI 引导。

## SEO
`wp-content/plugins/ggouai-seo-meta/ggouai-seo-meta.php` — 自写 WP 插件：
- 读 `_rank_math_*` postmeta 渲染 meta（RankMath 加载了但没初始化时可用）
- 输出完整 JSON-LD WebApplication（3 个工具页）
- rewrite 规则绕过 WP post_content 过滤，输出完整 HTML

## 技术栈
- WordPress 6.3 + Kadence theme
- PHP 8.3 + MySQL 5.7
- Python 3.13（邮件桥 / 自动化）
- 纯前端 JS（无 React/Vue，CDN 不依赖）

## 部署
1. 上传 `wp-content/plugins/ggouai-seo-meta/` 到 WP
2. 上传 `scripts/` 到服务器
3. WP 后台激活插件
4. `flush_rewrite_rules()` 让新 rewrite 生效
5. cron 每天 10:00 跑 drip

## 许可
MIT
>>>>>>> 1d52884 (Initial: 3 free AI tools + mail bridge + SEO plugin)
