<?php
/**

 * Created by PhpStorm.
 * User: chrispaul
 * Date: 2018/4/23
 * Time: 10:27
 */

namespace console\controllers;

use yii\Console\Controller;
use Yii;
use backend\modules\sys\components\Helper;
use yii\helpers\Inflector;
use yii\helpers\VarDumper;
use console\models\AuthItemItem as AuthItem;
use console\models\AuthItemChild;
use yii\rbac\Item;

/**
 * 遍历 application 获取路由
 * Class GenrouteController
 *
 * @package app\modules\cron\controllers
 */
class GenrouteController extends Controller
{
    public $type = Item::TYPE_PERMISSION;
    const PREFIX = '/';

    /**
     * 获取应用所有路由
     *
     * 注意：由于本controller是继承console 也就是 console App, 所以不会自动读到web模块路由，需要在main.php中手动配置模块
     *
     * @return array
     */
    public function actionRun()
    {
        $result = [];
        $this->getRouteRecrusive(Yii::$app, $result);
        Yii::debug($result);
    }

    /**
     * 排除模块：不会添加到权限
     *
     * @var array
     */
    protected $excludeModules = [
        'gii',
        'cron',
        'vii'
    ];

    /**
     * 递归获取路由
     *
     * 注意： console 下不能使用 module->uniqueId, 用module->id 代替
     * @param \yii\base\Module $module
     * @param array $result
     * @return array
     */
    private function getRouteRecrusive($module, &$result)
    {
        $token = "Get Route of '" . get_class($module) . "' with id '" . $module->uniqueId . "'";
        Yii::beginProfile($token, __METHOD__);
        try {
            foreach ($module->getModules() as $id => $child) {
                // echo $module->uniqueId . "\n";
                if(in_array($module->id, $this->excludeModules)) continue;

                if (($child = $module->getModule($id)) !== null) {
                    $this->getRouteRecrusive($child, $result);
                }
            }
            // exclude
            if(in_array($module->id, $this->excludeModules)) return [];

            foreach ($module->controllerMap as $id => $type) {
                $this->getControllerActions($type, $id, $module, $result);
            }

            $namespace = trim($module->controllerNamespace, '\\') . '\\';
            // todo recursive
            $result[$module->id] = [
                'id' => $module->id,
                'ns' => $namespace,
            ];
            $this->getControllerFiles($module, $namespace, '', $result);
        } catch (\Exception $exc) {
            Yii::error($exc->getMessage(), __METHOD__);
        }
        Yii::endProfile($token, __METHOD__);
    }

    /**
     * Get list controller under module
     * @param \yii\base\Module $module
     * @param string $namespace
     * @param string $prefix
     * @param mixed $result
     * @return mixed
     */
    private function getControllerFiles($module, $namespace, $prefix, &$result)
    {
        $path = Yii::getAlias('@' . str_replace('\\', '/', $namespace), false);
        $token = "Get controllers from '$path'";
        Yii::beginProfile($token, __METHOD__);
        try {
            if (!is_dir($path)) {
                return;
            }
            foreach (scandir($path) as $file) {
                if ($file == '.' || $file == '..') {
                    continue;
                }
                if (is_dir($path . '/' . $file)) {
                    $this->getControllerFiles($module, $namespace . $file . '\\', $prefix . $file . '/', $result);
                } elseif (strcmp(substr($file, -14), 'Controller.php') === 0) {
                    $id = Inflector::camel2id(substr(basename($file), 0, -14));
                    $className = $namespace . Inflector::id2camel($id) . 'Controller';
                    if (strpos($className, '-') === false && class_exists($className) && is_subclass_of($className, 'yii\base\Controller')) {
                        // get controllers
                        $result[$module->id]['controllers'][$prefix . $id] = [
                            'className' => $className
                        ];
                        $this->getControllerActions($className, $prefix . $id, $module, $result);
                    }
                }
            }
        } catch (\Exception $exc) {
            Yii::error($exc->getMessage(), __METHOD__);
        }
        Yii::endProfile($token, __METHOD__);
    }

    /**
     * Get list action of controller
     * @param mixed $type
     * @param string $id
     * @param \yii\base\Module $module
     * @param string $result
     */
    private function getControllerActions($type, $id, $module, &$result)
    {
        $token = "Create controller with cofig=" . VarDumper::dumpAsString($type) . " and id='$id'";
        Yii::beginProfile($token, __METHOD__);
        try {
            /* @var $controller \yii\base\Controller */
            $controller = Yii::createObject($type, [$id, $module]);
            $result[$module->id]['controllers'][$id]['actions']['*'] = '/' . $controller->id . '/*';
            $this->getActionRoutes($controller, $id, $module, $result);

        } catch (\Exception $exc) {
            Yii::error($exc->getMessage(), __METHOD__);
        }
        Yii::endProfile($token, __METHOD__);
    }

    /**
     * Get route of action
     * @param \yii\base\Controller $controller
     * @param array $result all controller action.
     */
    private function getActionRoutes($controller, $id, $module, &$result)
    {
        $token = "Get actions of controller '" . $controller->id . "'";
        Yii::beginProfile($token, __METHOD__);
        try {
            $prefix = '/' . $controller->id . '/';
            foreach ($controller->actions() as $id => $value) {
                // $result[] = $prefix . $id;
            }
            $class = new \ReflectionClass($controller);
            foreach ($class->getMethods() as $method) {
                $name = $method->getName();
                if ($method->isPublic() && !$method->isStatic() && strpos($name, 'action') === 0 && $name !== 'actions') {
                    $name = strtolower(preg_replace('/(?<![A-Z])[A-Z]/', ' \0', substr($name, 6)));
                    $result[$module->id]['controllers'][$id]['actions'][trim($name)] = $prefix . ltrim(str_replace(' ', '-', $name), '-');
                }
            }
        } catch (\Exception $exc) {
            Yii::error($exc->getMessage(), __METHOD__);
        }
        Yii::endProfile($token, __METHOD__);
    }









}
