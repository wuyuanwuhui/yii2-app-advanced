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