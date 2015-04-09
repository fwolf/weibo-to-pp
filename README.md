# Weibo to Coding.net 冒泡


## Message Flow

- Weibo posted
- IFTTT detect Weibo post, send mail to mailgun
- mailgun got mail, transfer it to HTTP POST with Route
- This project accept HTTP POST, then post to pp


## Prepare/Install

- Setup this to a public access site, got url point to public/index.php
- Copy config.default.php to config.php, fill configures
- Set a Route on mailgun, receive mail then forward to url above
- Set IFTTT monitor weibo, if new post, send mail to above mailgun mailbox
- Done, post new Weibo to test, with hash tag you set
