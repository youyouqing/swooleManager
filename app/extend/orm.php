<?php
/**
 * Created by PhpStorm.
 * User: dzc
 * Date: 19-7-12
 * Time: 下午4:54
 */
namespace app\extend;
use function app\common\backPoolConnection;
use core\Di;
use think\Exception;

class orm
{
    private $alias = [];  //记录全局的语句参数
    private $sql;    //存储最后一条sql
    private $conn;

    //断线重连次数
    private $Recount = 5;

    static $instance;//对象实例

    static public function shareInstance()
    {
        if (!self::$instance) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    public function setConn($conn)
    {
        $this->conn = $conn;
        return $this;
    }

    //field语句
    public function field( $field )
    {
        if( !is_string( $field ) ){
            throw new Exception("field语句的参数必须为字符串");
        }
        $this->alias['field'] = $field;
        return $this;
    }
    //table语句
    public function table( $table )
    {
        if( !is_string( $table ) ){
            throw new Exception("table语句的参数必须为字符串");
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
            throw new Exception("where语句的参数必须为数组或字符串");
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
            throw new Exception("order语句的参数必须为字符串");
        }
        $this->alias['order'] = $order;
        return $this;
    }
    //group语句
    public function group( $group )
    {
        if( !is_string( $group ) ){
            throw new Exception("group语句的参数必须为字符串");
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
            throw new Exception("请用table子句设置查询表");
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
            throw new Exception("请用table子句设置添加表");
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
            throw new Exception("请用table子句设置修改表");
        }else{
            $this->sql .= $this->alias['table'] . ' set ';
        }
        if( empty( $this->alias['where'] ) ){
            throw new Exception("更新语句必须有where子句指定条件");
        }
        return $this->sql;
    }
    //解析删除sql语句
    public function ParseDeleteSql()
    {
        $this->sql = 'delete from ';
        if( empty( $this->alias['table'] ) ){
            throw new Exception("请用table子句设置删除表");
        }else{
            $this->sql .= $this->alias['table'];
        }
        if( empty( $this->alias['where'] ) ){
            throw new Exception("删除语句必须有where子句指定条件");
        }
        $this->sql .= ' where ' . $this->alias['where'];
        return $this->sql;
    }

    /**
     * 支持断线重连的查询器
     */
    public function queryWithReConnect($sql , $pdoType)
    {
        try{
            switch ($pdoType) {
                case \PDO::FETCH_ASSOC:
                    $result = $this->conn->query( $this->sql )->fetchAll($pdoType);
                    backPoolConnection($this->conn);
                    return $result;
                break;

            }
        } catch (\PDOException $e) {
            if ($e->errorInfo[0] == 70100 || $e->errorInfo[0] == 2006){
               //这两个状态代表数据库挂了
                $reCount = 0;
                while ($reCount < $this->Recount) {
                    $this->queryWithReConnect($sql,$pdoType);
                    $reCount ++;
                    Di::shareInstance()->get(Di::DI_LOG)->log("mysql断线重连中。。次数:".$reCount);
                }
            }
        }

    }

    //查询语句
    public function select()
    {
        $this->ParseSelectSql();
        $row = $this->queryWithReConnect($this->sql , \PDO::FETCH_ASSOC);
        return $row;
    }
    //查询一条
    public function find()
    {
        $this->ParseSelectSql();
        $row = $this->queryWithReConnect($this->sql , \PDO::FETCH_ASSOC);
        return $row;
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
        backPoolConnection($this->conn);
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
        backPoolConnection($this->conn);
        return $this->conn->exec( $this->sql );
    }
    //删除语句
    public function delete()
    {
        $this->ParseDeleteSql();
        backPoolConnection($this->conn);
        return $this->conn->exec( $this->sql );
    }
    //获取最后一次执行的sql语句
    public function getLastSql()
    {
        return $this->sql;
    }
}