<?php
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

        print_r(static::getTreeByRecursive($arr));

        //$treeArr = Helper::toTree($arr);
        //echo self::printTree($treeArr);
    }

    public static function getTreeByRecursive($arr, $pid = 0, $level = 0)
    {
        static $list = [];
        foreach ($arr as $key => $val) {
            if ($val['pid'] == $pid) {
                $val['level'] = $level;
                $list[] = $val;
                unset($arr[$key]);
                static::getTreeByRecursive($arr, $val['id'], $level+1);
            }
        }
        return $list;
    }

    public static function getTreeByRefer(){}

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
        $l = '' = $pids = '';
        // 返回所拼接的字符串
        return $str;
    }









}