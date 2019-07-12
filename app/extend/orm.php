<?php
/**
 * Created by PhpStorm.
 * User: dzc
 * Date: 19-7-12
 * Time: 下午4:54
 */
namespace app\extend;
use \core\Di;
class orm
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
    public $maxInstance = 500;
    //心跳包检测时间周期  毫秒单位 (两分钟)
    public $heartTime = 2 * 60 *  1000;
    //是否初始化
    private $initd;
    //单个分配到的链接
    private $conn;

    private $alias = [];  //记录全局的语句参数
    private $sql;    //存储最后一条sql

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
            echo "数据库初始化";
            $this->setConf();
            $this->initPool();
        }
    }

    public function getPool()
    {
        return $this->pool;
    }

    public function setConn($conn)
    {
        $this->conn = $conn;
        return $this;
    }

    /**
     * 初始化 最小池
     */
    public function initPool()
    {
        for ($index = 1 ; $index <= $this->minInstance ; $index ++) {
            $this->createDb();
        }
        $this->checkPool();
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

    //field语句
    public function field( $field )
    {
        if( !is_string( $field ) ){
            throw new exception("field语句的参数必须为字符串");
        }
        $this->alias['field'] = $field;
        return $this;
    }
    //table语句
    public function table( $table )
    {
        if( !is_string( $table ) ){
            throw new exception("table语句的参数必须为字符串");
        }
        $this->alias['table'] = $table;
        return $this;
    }
    //where语句
    public function where( $where )
    {
        $this->alias['where'] = '';
        if( is_array( $where ) ){
            foreach( $where as $key=>$vo ){
                $this->alias['where'] .= " `$key`" . ' = ' . $vo . ' and ';
            }
            $this->alias['where'] = rtrim( $this->alias['where'], 'and ' );
        }else if( is_string( $where ) ){
            $this->alias['where'] = $where;
        }else{
            throw new exception("where语句的参数必须为数组或字符串");
        }
        return $this;
    }
    //limit语句
    public function limit( $page =0 , $pageNum = 10)
    {
        $this->alias['limit'] = $page. ',' . $pageNum;
        return $this;
    }
    //order语句
    public function order( $order )
    {
        if( !is_string( $order ) ){
            throw new exception("order语句的参数必须为字符串");
        }
        $this->alias['order'] = $order;
        return $this;
    }
    //group语句
    public function group( $group )
    {
        if( !is_string( $group ) ){
            throw new exception("group语句的参数必须为字符串");
        }
        $this->alias['group'] = $group;
        return $this;
    }
    //解析查询sql语句
    public function ParseSelectSql()
    {
        $this->sql = 'select *';
        if( !empty( $this->alias['field'] ) ){
            $this->sql = str_replace( '*', $this->alias['field'], $this->sql );
        }
        if( empty( $this->alias['table'] ) ){
            throw new exception("请用table子句设置查询表");
        }else{
            $this->sql .= ' from ' . $this->alias['table'];
        }
        if( !empty( $this->alias['where'] ) ){
            $this->sql .= ' where ' . $this->alias['where'];
        }
        if( !empty( $this->alias['group'] ) ){
            $this->sql .= ' group by ' . $this->alias['group'];
        }
        if( !empty( $this->alias['order'] ) ){
            $this->sql .= ' order by ' . $this->alias['order'];
        }
        if( !empty( $this->alias['limit'] ) ){
            $this->sql .= ' limit ' . $this->alias['limit'];
        }
    }
    //解析添加sql语句
    public function ParseAddSql()
    {
        $this->sql = 'insert into ';
        if( empty( $this->alias['table'] ) ){
            throw new exception("请用table子句设置添加表");
        }else{
            $this->sql .= $this->alias['table'] . ' set ';
        }
        return $this->sql;
    }
    //解析更新sql语句
    public function ParseUpdateSql()
    {
        $this->sql = 'update ';
        if( empty( $this->alias['table'] ) ){
            throw new exception("请用table子句设置修改表");
        }else{
            $this->sql .= $this->alias['table'] . ' set ';
        }
        if( empty( $this->alias['where'] ) ){
            throw new exception("更新语句必须有where子句指定条件");
        }
        return $this->sql;
    }
    //解析删除sql语句
    public function ParseDeleteSql()
    {
        $this->sql = 'delete from ';
        if( empty( $this->alias['table'] ) ){
            throw new exception("请用table子句设置删除表");
        }else{
            $this->sql .= $this->alias['table'];
        }
        if( empty( $this->alias['where'] ) ){
            throw new exception("删除语句必须有where子句指定条件");
        }
        $this->sql .= ' where ' . $this->alias['where'];
        return $this->sql;
    }
    //查询语句
    public function select()
    {
        $this->ParseSelectSql();
        $row = $this->conn->query( $this->sql )->fetchAll( \PDO::FETCH_ASSOC );
        return $row;
    }
    //查询一条
    public function find()
    {
        $this->ParseSelectSql();
        $row = $this->conn->query( $this->sql )->fetch( \PDO::FETCH_ASSOC );
        $arrObj = clone $this;  //clone当前对象防止对this对象造成污染
        $arrObj->data = $row;
        $result = $arrObj;
        unset( $arrObj );
        return $result;
    }
    //添加数据
    public function add( $data )
    {
        if( !is_array( $data ) ){
            throw new exception("添加数据add方法参数必须为数组");
        }
        $this->ParseAddSql();
        foreach( $data as $key=>$vo ){
            $this->sql .= " `{$key}` = '" . $vo . "',";
        }
        $this->conn->exec( rtrim( $this->sql, ',' ) );
        return $this->conn->lastInsertId();
    }
    //更新语句
    public function update( $data )
    {
        if( !is_array( $data ) ){
            throw new exception("更新数据update方法参数必须为数组");
        }
        $this->ParseUpdateSql();
        foreach( $data as $key=>$vo ){
            $this->sql .= " `{$key}` = '" . $vo . "',";
        }
        $this->sql = rtrim( $this->sql, ',' ) . ' where ' . $this->alias['where'];
        return $this->conn->exec( $this->sql );
    }
    //删除语句
    public function delete()
    {
        $this->ParseDeleteSql();
        return $this->conn->exec( $this->sql );
    }
    //获取最后一次执行的sql语句
    public function getLastSql()
    {
        return $this->sql;
    }
}