<?php


namespace app\extend;


use core\Di;

class mysqlPool
{
    public $hostname; //数据库地址
    public $database;   //数据库名
    public $username;  //数据库用户名
    public $password;   //数据库密码
    public $hostport;  //数据库端口
    public $charset;   //数据库编码

    //连接池
    public $pool = [];
    //最小实例
    public $minInstance = 50;
    //最大实例
    public $maxInstance = 100;
    //心跳包检测时间周期  毫秒单位 (两分钟)
    public $heartTime =  2 * 60 * 1000;
    //是否初始化
    private $initd;
    //单个分配到的链接

    static $instance;//对象实例

    static public function shareInstance()
    {
        if (!self::$instance) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    /**
     * 默认配置
     */
    public function setConf($hostname = false , $database = false , $username = false , $password = false , $hostport = false , $charset = false)
    {
        $this->hostname = $hostname ? $hostname : Di::shareInstance()->get(Di::DI_CONFIG.".database")["hostname"];
        $this->database = $database ? $database : Di::shareInstance()->get(Di::DI_CONFIG.".database")["database"];
        $this->username = $username ? $username : Di::shareInstance()->get(Di::DI_CONFIG.".database")["username"];
        $this->password = $password ? $password : Di::shareInstance()->get(Di::DI_CONFIG.".database")["password"];
        $this->hostport = $hostport ? $hostport : Di::shareInstance()->get(Di::DI_CONFIG.".database")["hostport"];
        $this->charset  = $charset  ? $charset  : Di::shareInstance()->get(Di::DI_CONFIG.".database")["charset"];
        return $this;
    }

    private function __construct()
    {

    }

    public function init()
    {
        if (!$this->initd) {
            $this->setConf();
            $this->initPool();
        }
    }

    /**
     * 获取连接池
     * @return array
     */
    public function getPool()
    {
        return $this->pool;
    }

    /**
     * 初始化 最小池
     */
    public function initPool()
    {
        for ($index = 1 ; $index <= $this->minInstance ; $index ++) {
            $this->createDb();
        }
        $this->initd = true;
    }


    /**
     * 创建db 入池
     */
    public function createDb()
    {
        $dsn = "mysql:host=$this->hostname;dbname=$this->database;charset=$this->charset;port=$this->hostport";
        try{
            $res = new \PDO( $dsn, $this->username, $this->password);
        } catch (PDOException $e) {
            throw $e;
        }
        $this->pool[] = $res;
    }


    /**
     * 获取连接池的数据库实例
     * 这里使用pop是保证一个事务对应一个链接，使用完毕后返还给连接池
     * 超过连接池就坐等，也能起到保护mysqlserver的作用
     */
    public function getPoolCon()
    {
        $poolConNum = count($this->pool);
        if ($poolConNum < $this->maxInstance) {
            $this->createDb();
        }
        return array_pop($this->pool);
    }

    /**
     * 把数据库实例返还给连接池
     * 需要手动执行，如查询完毕数据 手动调用
     */
    public function backPool($instance)
    {
        $instance and array_push($this->pool,$instance);
    }


    /**
     * 检查并维修连接池
     * 需要用到定时器
     */
    public function checkPool()
    {
        echo "准备检查中...".PHP_EOL;
        swoole_timer_tick($this->heartTime , function () {

            echo "检查中...".PHP_EOL;

            //清理空闲链接 阀值设定为一半的最大连接数
            $poolConNum = count($this->pool);
            if ($poolConNum > intval($this->maxInstance/2)) {
                for ($indexPop = 0 ; $indexPop < $this->maxInstance/4 ; $indexPop ++) {
                    array_pop($this->pool);
                }
                $poolConNum = count($this->pool);
            }
            //新增繁忙链接
            if ($poolConNum < $this->minInstance) {
                for ($indexPush = $poolConNum ; $indexPush < $this->minInstance ; $indexPush ++) {
                    $this->createDb();
                }
            }
            //促活链接 TODO
            foreach ($this->pool as $item) {

            }

        });
    }

}