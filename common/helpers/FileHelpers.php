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
     * 扫描文件夹递归获取 controller 文件
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
     * @param string $l
     * @return string
     */
    public static function printTree($arr, $l = '-|', $pids = '')
    {
        static $l = ''; static $str = ''; static $pids = '';

        foreach ($arr as $key => $val) {
            $ids = $val['id'];
            $str .= $l . $val['name'] . "($pids$ids)" . "\r\n";
            // 如果有子节点则递归
            if (!empty($arr[$key]['children']) && is_array($arr[$key]['children'])) {
                $l .= '-|';  // 加前缀
                $pids .= $ids . '_'; // 并带上级pid
                self::printTree($arr[$key]['children'], $l, $pids);
            }
        }
        // 如果无子节点则置空变量
        $l = $pids = '';
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