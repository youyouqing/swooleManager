<?php
namespace core;

use Swoole\Table;

class TableManager
{
    const TYPE_INT = Table::TYPE_INT;
    const TYPE_FLOAT = Table::TYPE_FLOAT;
    const TYPE_STRING = Table::TYPE_STRING;

    private $tables = [];

    static $instance;

    static public function shareInstance()
    {
        if (!self::$instance) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    /**
     * 添加内存表
     * @param $name
     * @param array $columns
     * @param $size
     * @param float $conflict_proportion
     * @return self
     */
    public function addTable($name , $columns = [] , $size , $conflict_proportion = 0.2)
    {
        if (!isset($this->tables[$name])) {
            $table = new Table($size,  $conflict_proportion);
            foreach ($columns as $key => $value) {
                $table->column($key , $value['type'] , $value['size']);
            }
            $table->create();
            $this->tables[$name] = $table;
        }
        return self::$instance;
    }

    public function getTable($name , $key = null , $field = null)
    {
        if (!$name) return false;
        if (!$key) return $this->tables[$name] ?? false;
        return $this->tables[$name] ? $this->tables[$name]->get($key , $field): false;
    }

    public function setTable($name , $key , $values = [])
    {
        return $this->tables[$name]->set($key , $values);
    }

    public function delTable($name , $key)
    {
        if ($this->tables[$name]) {
            $this->tables[$name]->del($key);
            unset($this->tables[$name]);
        }
    }

    public function count($name)
    {
        return $this->tables[$name]->count();
    }


}