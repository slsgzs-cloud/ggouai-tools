#!/usr/bin/env python3
"""
ggouai-mail-bridge.py

独立邮件发送 CLI，通过 agently-cli 发信（每日 50 条配额）。

用法：
    python ggouai-mail-bridge.py --to user@gmail.com --subject "..." --body "..."
    python ggouai-mail-bridge.py --to user@gmail.com --subject "..." --body-file path.html

为什么不用 wp_mail：
    - wp_mail 依赖 sendmail 或 SMTP 插件，本机没配，永远发不出去
    - agently-cli 是 Agent Mail 官方 CLI，稳定，每日 50 条免费
"""

import argparse
import json
import os
import subprocess
import sys
import tempfile
import time


def main():
    p = argparse.ArgumentParser(description='ggouai 邮件桥（走 agently-cli）')
    p.add_argument('--to', required=True, help='收件人邮箱')
    p.add_argument('--subject', required=True, help='邮件主题')
    p.add_argument('--body', help='邮件正文（HTML 或纯文本）')
    p.add_argument('--body-file', help='邮件正文文件路径')
    p.add_argument('--cc', action='append', default=[], help='抄送（可多次）')
    p.add_argument('--log', default='E:\\hermes\\scripts\\mail_bridge.log', help='日志路径')
    args = p.parse_args()

    # 准备正文
    if args.body_file:
        with open(args.body_file, 'r', encoding='utf-8') as f:
            body = f.read()
    elif args.body:
        body = args.body
    else:
        print('错误: 缺少 --body 或 --body-file', file=sys.stderr)
        return 1

    # agently-cli 要求相对路径，用临时目录
    tmp = tempfile.mkdtemp(prefix='ggouai_mail_')
    body_path = os.path.join(tmp, 'body.html')
    with open(body_path, 'w', encoding='utf-8') as f:
        f.write(body)

    # 在 Windows 上 npm 装的 CLI 是 .cmd 脚本，需要 shell=True 或直接指定 .cmd
    if os.name == 'nt':
        cmd = 'agently-cli.cmd message +send --to "{}" --subject "{}" --body-file "body.html" --confirmed'.format(
            args.to.replace('"', '\"'),
            args.subject.replace('"', '\"'),
        )
        for cc in args.cc:
            cmd += ' --cc "{}"'.format(cc.replace('"', '\"'))
        use_shell = True
    else:
        cmd = ['agently-cli', 'message', '+send',
               '--to', args.to,
               '--subject', args.subject,
               '--body-file', 'body.html',
               '--confirmed']
        for cc in args.cc:
            cmd.extend(['--cc', cc])
        use_shell = False

    start = time.time()
    try:
        r = subprocess.run(cmd, capture_output=True, text=True, cwd=tmp, timeout=60, shell=use_shell)
        elapsed = time.time() - start
        stdout, stderr = r.stdout, r.stderr
        exit_code = r.returncode
    except subprocess.TimeoutExpired:
        exit_code, stdout, stderr = -1, '', 'timeout after 60s'
        elapsed = time.time() - start
    except FileNotFoundError as e:
        exit_code, stdout, stderr = -2, '', f'agently-cli not found: {e}'
        elapsed = time.time() - start

    # 清理
    try:
        os.remove(body_path)
        os.rmdir(tmp)
    except:
        pass

    # 写日志
    entry = {
        'ts': time.strftime('%Y-%m-%dT%H:%M:%S'),
        'to': args.to,
        'subject': args.subject,
        'body_len': len(body),
        'exit': exit_code,
        'elapsed': round(elapsed, 2),
        'stdout': stdout[:2000],
        'stderr': stderr[:2000],
    }
    try:
        with open(args.log, 'a', encoding='utf-8') as f:
            f.write(json.dumps(entry, ensure_ascii=False) + '\n')
    except:
        pass

    print(stdout)
    if exit_code != 0:
        print(f'agently-cli exit={exit_code}', file=sys.stderr)
        if stderr:
            print(stderr, file=sys.stderr)
        return 1
    return 0


if __name__ == '__main__':
    sys.exit(main())
