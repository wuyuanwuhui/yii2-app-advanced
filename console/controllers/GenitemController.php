<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/4/13 0013
 * Time: 23:34
 */

namespace console\controllers;

use Yii;
use yii\console\Controller;
use common\helpers\FileHelpers;
use yii\helpers\StringHelper;
use yii\helpers\ArrayHelper;
use backend\modules\sys\components\Helper;

class GenitemController extends Controller
{

    public static $controllerExt = 'Controller.php';

    public function actionRun()
    {

        $this->parseControllerFile();

        exit;
        $path = Yii::getAlias('@backend/modules');
        $controllerFiles = FileHelpers::fetchControllerFromDir($path);
        Yii::debug($controllerFiles);

        foreach ($controllerFiles as $controllerFile)
        {
            // 去掉前缀目录
            $controllerFile = substr($controllerFile, strpos($controllerFile, $path));
            // 提取 module
            $moduleClass = substr($controllerFile, 0, strrpos($controllerFile, 'controllers')) . 'Module';
            Yii::debug($moduleClass);
            // parseModule


            // parseController

        }
    }


    /**
     * 分析controller 文件： 主要是文件夹注释、action 注释、action url
     * @param string $file
     * @throws \ReflectionException
     */
    public function parseControllerFile($file = '')
    {
        $file = 'backend/modules/sys/controllers/SysuserController.php';
        // 提取 url
        $controllerUrl = str_replace(['backend', 'modules', 'controllers', self::$controllerExt], '', $file); // todo
        $controllerUrl = strtolower(FileHelpers::normalizePath($controllerUrl));
        Yii::debug($controllerUrl);

        $className = str_replace(['/', '.php'], ['\\', ''], $file);
        $controlClass = new \ReflectionClass($className);
        $controllerComment = Helper::getComment($controlClass);
        Yii::debug($controllerComment);

        $methods = $controlClass->getMethods(\ReflectionMethod::IS_PUBLIC);
        foreach ($methods as $method)
        {
            if (!StringHelper::startsWith($method->name, 'action')) continue;
            if ($method->name === 'actions') continue; // todo

            $actionUrl = $controllerUrl . '/' . substr(strtolower($method->name), strlen('action'));
            $actionComment = Helper::getComment($method);
            Yii::debug($actionComment);
            Yii::debug($actionUrl);
        }


    }


    public function actionTest()
    {
        $str = '/vagrant/www/yii2-app-advanced/backend/modules/sys/modules/sub1/controllers/DefaultController.php';
        var_dump(substr($str, 0, strrpos($str, 'controllers')));
        $arr = [
            ['id' => 100, 'n' => 'abc',],
            ['id' => 105, 'n' => 'edf',],
        ];
        foreach ($arr as &$value) {
            // unset($value);
        }
        print_r($arr);
        foreach($arr as $value) {}
        print_r($arr);
    }







}