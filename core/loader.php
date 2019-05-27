<?php
/**
 * Created by PhpStorm.
 * User: dzc
 * Date: 2019/5/8
 * Time: 20:30
 */

namespace core;


use think\cache\driver\Redis;

class loader
{
    static public function load()
    {
        spl_autoload_register("core\\loader::autoload", true, true);
    }


    static public function autoload($class)
    {
        $file = ROOT_PATH . "/" . str_replace("\\", "/", $class . ".php");
        if (is_file($file)) {
            include $file;
        }
        return false;
    }

    static public function includeCore($dirpath)
    {
        $arr = [];
        self::getCorePath($dirpath, $arr);
        foreach ($arr as $path) {
            if (is_file($path)) {
                include_once $path;
            }
        }
    }

    static public function includeComposer()
    {
        $file = VENDOR_PATH . '/autoload.php';
        if (file_exists($file)) {
            require $file;
        } else {
            die("include composer autoload.php fail\n");
        }
    }

    static public function includeConfig($dirpath)
    {
        $arr = [];
        self::getCorePath($dirpath, $arr);
        foreach ($arr as $path) {
            $fileName = pathinfo($path)["filename"];
            Di::shareInstance()->set("config.{$fileName}", include_once $path);
        }
    }

    /**
     * 初始化db操作都在这里执行 注入di
     */
    static public function initDatabases()
    {
        Di::shareInstance()->set("MYSQL",null);
    }

    /**
     * 初始化redis 注入di
     */
    static public function initRedis()
    {
        Di::shareInstance()->set("REDIS",(new Redis(Di::shareInstance()->get("config.redis"))));
    }



    static private function getCorePath($dirpath, &$arr)
    {
        if (is_dir($dirpath)) {
            $hadle = @opendir($dirpath);
            while ($file = readdir($hadle)) {
                if (!in_array($file, [".", ".."])) {
                    $subdir = $dirpath . "/" . $file;
                    array_push($arr, $subdir);
                    if (is_dir($subdir)) {
                        self::getCorePath($subdir, $arr);
                    }
                }
            }
        }
    }

}