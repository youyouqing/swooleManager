# swooleManager
swooleManager   timer task

查看swoole 版本方式：
```bash
php --ri swoole

# Swoole => enabled
# Author => Swoole Team <team@swoole.com>
# Version => 4.x
```


启动http服务：
```bash
composer config -g repo.packagist composer https://packagist.phpcomposer.com
composer install
cd app && php index.php http start
```



TODO
- [ ] 热更新 （使用md5_file()）  代码写在core/server/http.php:58
- [ ] 秒级cron定时器  新建vendor/dragonmantank/cron-expression/src/Cron/SecondField.php逻辑处理