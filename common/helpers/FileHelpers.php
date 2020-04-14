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
        $result = $temp = [];
        if(is_dir($dir)) {
            $dh = opendir($dir);
            while(false !== ($file = readdir($dh))){
                if ($file == '.' || $file == '..') continue;
                $filePath  = $dir . '/' . $file;
                if ( is_dir($filePath)){
                    $result[$filePath] = static::scanDir($filePath);
                } else {
                    $temp[] = $filePath;
                }
            }
        }
        if (!empty($temp)) {
            foreach ($temp as $val){
                $result[] = $val;
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

    public static function f($dir)
    {

    }


    /**
     * 从数组中删除空白的元素（包括只有空白字符的元素），支持多维数组
     *
     * 用法：
     * @code php
     * $arr = array('', 'test', '   ');
     * Helper_Array::removeEmpty($arr);
     *
     * dump($arr);
     *   // 输出结果中将只有 'test'
     * @endcode
     *
     * @param array $arr 要处理的数组
     * @param boolean $unsetEmpty 是否移除空元素
     * @return array
     */
    static function removeEmpty($arr, $unsetEmpty = true) {
        foreach ($arr as $key => $value) {
            if (is_array($value) && count((array) $value) > 0) {
                $arr[$key] = self::removeEmpty($value);
            } else {
                // $value = trim($value);
                if(empty($value) && $unsetEmpty) {
                    unset($arr[$key]);
                } else {
                    $arr[$key] = $value;
                }
            }
        }
        return $arr;
    }




}