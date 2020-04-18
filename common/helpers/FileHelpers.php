<?php
namespace common\helpers;

use Yii;
use yii\helpers\FileHelper;
use yii\helpers\StringHelper;

class FileHelpers extends FileHelper
{
    /**
     * 递归列出文件夹下文件
     * @param $dir
     * @return array
     */
    public static function scanDirFiles($dir)
    {
        $result = [];
        if(is_dir($dir)) {
            $dh = opendir($dir);
            while(false !== ($file = readdir($dh))){
                if ($file == '.' || $file == '..') continue;
                $filePath  = $dir . '/' . $file;
                if ( is_dir($filePath)){
                    $result[$filePath] = static::scanDirFiles($filePath);
                } else {
                    $result[$file] = $filePath;
                }
            }
        }

        return $result;
    }

    /**
     * 扫描文件夹递归获取 controller 文件: use FileHelpers::findFiles instead
     * @param $dir
     * @param string extension
     * @return array
     */
    public static function fetchControllerFromDir($dir, $extension = 'Controller.php')
    {
        static $result = [];
        if(is_dir($dir)) {
            $dh = opendir($dir);
            while(false !== ($file = readdir($dh))){
                if ($file == '.' || $file == '..') continue;
                $filePath  = $dir . '/' . $file;
                if ( is_dir($filePath)){
                    static::fetchControllerFromDir($filePath);
                } else {
                    if (StringHelper::endsWith($file, $extension)) {
                        $result[] = $filePath;
                    }
                }
            }
        }
        return $result;
    }


    /**
     * 打印出树形结构：前提数组本身已经是树形结构数组
     * @param $arr
     * @param int $level
     * @param array $ppid
     * @return string
     */
    public static function printTree($arr, $level = 0, $ppid = [])
    {
        static $str = '';
        foreach ($arr as $key => $val)
        {
            if ($val['pid'] != 0) {
                $ppid[] = $val['pid'];
                $ppid = array_unique($ppid);
            }
            $prefix = str_repeat('-|', $level);
            // echo "{$val['name']} and level is : {$level}, prefix is: {$prefix} \r\n";

            $pids = '';
            if (!empty($ppid)) {
                $pids = implode('_', $ppid) . '_';
            }
            $str .= $prefix . $val['name'] . "($pids{$val['id']})" . "\r\n";

            // 如果有子节点则递归
            if (!empty($arr[$key]['children']) && is_array($arr[$key]['children'])) {
                self::printTree($arr[$key]['children'],$level+1, $ppid);
            }
        }
        // 返回所拼接的字符串
        return $str;
    }

    // -------------------------------------------------------------------------------------------------------------

    private static $parse;
    public static function instanceParser()
    {
        if(self::$parse == null) {
            self::$parse = new DocParser();
        }
        return self::$parse;
    }

    public static function DocParser($doc)
    {
        if (!$doc) return '';
        return self::instanceParser()->parse($doc);
    }

    /**
     * @param $object
     * @param string $key
     * @return string
     */
    public static function getComment($object, $key = 'description')
    {
        $comment = $object->getDocComment();
        $comment = Helper::DocParser($comment);
        return ($comment[$key]) ?? ($comment['long_description'] ?? '');
    }


}