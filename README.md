# swooleManager
swooleManager   基于swoole4.X的秒级定时任务管理系统

查看swoole 版本方式：
```bash
php --ri swoole

# Swoole => enabled
# Author => Swoole Team <team@swoole.com>
# Version => 4.x
```

docker环境镜像(独立php环境)
```
docker pull zhicongdai/qnphp
```

启动http服务：
```bash
安装方式  
1 git clone https://github.com/youyouqing/swooleManager.git
2 composer config -g repo.packagist composer https://packagist.phpcomposer.com
3 composer install
4 php app/index.php http start
```



TODO
- [ ] 热更新 （使用md5_file()）  代码写在core/server/http.php:58
- [ ] 秒级cron定时器  新建vendor/dragonmantank/cron-expression/src/Cron/SecondField.php逻辑处理