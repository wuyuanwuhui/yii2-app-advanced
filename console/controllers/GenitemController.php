<?php
namespace console\controllers;
use Yii;
use yii\console\Controller;
use common\helpers\FileHelpers;
use yii\helpers\StringHelper;
use common\helpers\ArrayHelpers;
use backend\modules\sys\components\Helper;

class GenitemController extends Controller
{

    public static $controllerExt = 'Controller.php';

    public function actionRun()
    {

        $this->parseModuleFile();

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
     * 获取模块注释：保存模块为item 并返回模块名称以备给controller使用，因为模块是controller的父级
     *
     * @param string $controllerFile
     * @return string
     * @throws \ReflectionException
     */
    public function parseModuleFile($controllerFile = '')
    {
        $controllerFile = 'backend/modules/sys/controllers/SysuserController.php';
        $moduleClass = substr($controllerFile, 0, strrpos($controllerFile, 'controllers')) . 'Module';
        $moduleFile = Yii::getAlias('@' . $moduleClass) . '.php';

        if ( !file_exists($moduleFile)) return '';

        $moduleClass = str_replace(['/'], ['\\'], $moduleClass);
        $module = new \ReflectionClass($moduleClass);
        $moduleComment = Helper::getComment($module);

        return $moduleComment;
    }

    public function saveItem(){}

    /**
     * 分析controller 文件： 主要是文件夹注释、action 注释、action url
     *
     * @param string $controllerFile
     * @param string $moduleComment
     * @throws \ReflectionException
     */
    public function parseControllerFile($controllerFile = '', $moduleComment = '')
    {
        $controllerFile = 'backend/modules/sys/controllers/SysuserController.php';
        // 提取 url
        $controllerUrl = str_replace(['backend', 'modules', 'controllers', self::$controllerExt], '', $controllerFile); // todo
        $controllerUrl = strtolower(FileHelpers::normalizePath($controllerUrl));
        Yii::debug($controllerUrl);

        $className = str_replace(['/', '.php'], ['\\', ''], $controllerFile);
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

    // ---------------------------------------------------------------------------------------------------------------

    public function actionTest()
    {
        $str = '/vagrant/www/yii2-app-advanced/backend/modules/sys/modules/sub1/controllers/DefaultController.php';
//        var_dump(substr($str, 0, strrpos($str, 'controllers')));
//        $arr = [
//            ['id' => 100, 'n' => 'abc',],
//            ['id' => 105, 'n' => 'edf',],
//        ];
//        foreach ($arr as &$value) {
//            // unset($value);
//        }
//        print_r($arr);
//        foreach($arr as $value) {}
//        print_r($arr);

        $dir = '/vagrant/www/yii2-app-advanced/backend/modules/sys';
        $files = FileHelpers::findFiles($dir, [
            // 'filter' => function($path) {},
            'only' => ['pattern' => '*Controller.php'],
            'recursive' => true,
        ]);
        Yii::debug($files);
    }

    public function actionTree()
    {
        $arr = [
            ['id' => 1, 'pid' => 0, 'name' => '系统管理'],
            ['id' => 10, 'pid' => 1, 'name' => '用户管理'],
            ['id' => 20, 'pid' => 10, 'name' => '用户创建'],
            ['id' => 30, 'pid' => 10, 'name' => '用户修改'],
            ['id' => 50, 'pid' => 30, 'name' => '用户修改制定'],

            ['id' => 2, 'pid' => 0, 'name' => '游戏管理'],
            ['id' => 200, 'pid' => 2, 'name' => '游戏信息'],
            ['id' => 210, 'pid' => 2, 'name' => '游戏对接'],
            ['id' => 310, 'pid' => 210, 'name' => '对接腾讯'],
        ];
        $treeArr = ArrayHelpers::toTree($arr);
        echo FileHelpers::printTree($treeArr);
    }










}