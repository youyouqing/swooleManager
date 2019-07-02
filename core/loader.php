<?php
/**
 * Created by PhpStorm.
 * User: dzc
 * Date: 2019/5/8
 * Time: 20:30
 */

namespace core;

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
        }
    }

    static public function includeConfig($dirpath)
    {
        $arr = [];
        self::getCorePath($dirpath, $arr);
        foreach ($arr as $path) {
            if (strpos($path ,".php") === false) continue;
            $fileName = pathinfo($path)["filename"];
            Di::shareInstance()->set(Di::DI_CONFIG.".{$fileName}", include_once $path);
        }
    }

    /**
     * 初始化db操作都在这里执行 注入di
     */
    static public function initDatabases()
    {
        //TODO
    }

    /**
     * 初始化redis 注入di
     */
    static public function initRedis()
    {
        $redis = new \Redis();
        $redis->pconnect(Di::shareInstance()->get(Di::DI_CONFIG.".redis")['host'] , Di::shareInstance()->get(Di::DI_CONFIG.".redis")['port']);
        $redis->auth(Di::shareInstance()->get(Di::DI_CONFIG.".redis")['auth']);
        Di::shareInstance()->set(Di::DI_REDIS,$redis);
    }

    /**
     * 初始化log 注入di
     */
    static public function initLogs()
    {
        Di::shareInstance()->set(Di::DI_LOG,(log::shareInstance()));
    }

    static private function getCorePath($dirpath, &$arr , $deep = false)
    {
        if (is_dir($dirpath)) {
            $hadle = @opendir($dirpath);
            while ($file = readdir($hadle)) {
                if (!in_array($file, [".", ".."])) {
                    $subdir = $dirpath . "/" . $file;
                    array_push($arr, $subdir);
                    if ($deep && is_dir($subdir)) {
//                        print_r($subdir);exit();
                        self::getCorePath($subdir, $arr);
                    }
                }
            }
        }
    }

}