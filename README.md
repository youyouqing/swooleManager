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
3 cd swooleManager &&  composer install
4 导入app/datasource/webcron.sql到你mysql中
5 覆盖源码配置项 vendor/topthink/think-orm/src/config.php
    'type'            => Di::shareInstance()->get(Di::DI_CONFIG.".databases")["type"] ?? "mysql",
    // 服务器地址
    'hostname'        => Di::shareInstance()->get(Di::DI_CONFIG.".databases")["hostname"] ?? "172.20.199.4",
    // 数据库名
    'database'        => Di::shareInstance()->get(Di::DI_CONFIG.".databases")["database"] ?? "webcron",
    // 用户名
    'username'        => Di::shareInstance()->get(Di::DI_CONFIG.".databases")["username"] ?? "root",
    // 密码
    'password'        => Di::shareInstance()->get(Di::DI_CONFIG.".databases")["password"] ?? "qingniu123",
    // 端口
    'hostport'        => Di::shareInstance()->get(Di::DI_CONFIG.".databases")["hostport"] ?? "3306",
6 启动项目 php app/index.php http start
```



TODO
- [ ] web接口
- [ ] docker项目一键集成