# swooleManager
swooleManager   timer task


启动http服务：  cd app && php index.php http start


TODO
- [ ] 热更新 （使用md5_file()）  代码写在core/server/http.php:58
- [ ] 秒级cron定时器  新建vendor/dragonmantank/cron-expression/src/Cron/SecondField.php逻辑处理