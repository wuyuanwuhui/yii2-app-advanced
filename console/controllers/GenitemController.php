<?php
namespace console\controllers;
use Yii;
use yii\console\Controller;
use common\helpers\FileHelpers;
use yii\helpers\StringHelper;
use common\helpers\ArrayHelpers;
use backend\modules\sys\components\Helper;
use console\models\AuthItemItem;
use console\models\AuthItemChild;
use yii\rbac\Item;

/**
 * Class GenitemController
 *
 * @description 将路由生成权限: 只需要运行 php yii genitem/run 即可
 *
 * @hint 注意: 前端VUE定义的路由命名必须与自动生成的模块路由一致，详见数据库auth_item 表 path 字段
 *
 * @package console\controllers
 */
class GenitemController extends Controller
{
    public static $controllerExt = 'Controller.php';
    public static $replace = ['backend', 'modules', 'controllers', 'Controller.php'];

    public $searchPath = 'backend/modules';
    public $useCache = 0;

    public function options($actionID)
    {
        return ['searchPath', 'useCache'];
    }

    /**
     * 通过扫描目录获取module文件和controller文件生成权限以及菜单
     *
     *  1. 将已经分配的权限保存到缓存
     *  2. 支持添加单个控制器的权限不影响
     *  3. 支持回滚到执行失败之前的状态 useCache = 1
     *
     * @param string $searchPath 不要使用绝对路径根目录, 从命名空间开始类似 backend/modules、app/sys/modules ...
     * @param int $useCache
     * @throws \ReflectionException
     * @throws \yii\db\Exception
     */
    public function actionRun($searchPath = 'backend/modules', $useCache = 0)
    {
        // $optionValues = $this->getOptionValues('run'); //yii genitem/run --search-path='backend/modules'  use-cache=0
        if ($useCache == 1) {
            $itemChildCache = Yii::$app->cache->get('auth_item_child_all');
            $this->deleteItem();
            $this->saveItemFromCache();
        } else {
            // 先从缓存中读取已经手动分配的角色权限 重新生成item后再次导入
            $itemChildCache = Yii::$app->cache->get('auth_item_child_role');
            // 把数据表数据保存到缓存
            $this->saveToCache();
            // 删除item
            $this->deleteItem();
            // 重新新增item
            $searchPath = FileHelpers::normalizePath($searchPath);
            $path = Yii::getAlias("@{$searchPath}");
            $controllerFiles = FileHelpers::findFiles($path, [ 'only' => ['pattern' => '*Controller.php'] ]);
            // Yii::debug($controllerFiles); die;
            foreach ($controllerFiles as $controllerFile)
            {
                // 去掉前缀目录
                $controllerFile = substr($controllerFile, strpos($controllerFile, $searchPath));
                // parseModule
                $module = $this->parseModuleFile($controllerFile);
                // parseController
                $this->parseControllerFile($controllerFile, $module);
            }
        }
        // 从缓存数据中重新保存到child 表 (非新增部分)
        $this->saveItemChildFromCache($itemChildCache);
    }

    /**
     * 扫描单个控制器生成权限, 主要对于新增单个控制器来说很方便
     *
     * @param $controllerFile string 不要使用绝对路径, 从命名空间开始类似 backend/modules、app/sys/modules ...
     * @param int $pid
     * @throws \ReflectionException
     */
    public function actionRunOneController($controllerFile, $pid = 0)
    {
        $controllerFile = FileHelpers::normalizePath($controllerFile);
        $this->parseControllerFile($controllerFile, $pid);
    }

    static $defaultDuration = 24 * 3600;
    /**
     * 把表数据保存到缓存中
     * 1. 缓存 auth_items 表数据，若出现意外情况可以支持回滚到之前状态
     * 2. 缓存 auth_item_child 表数据已经分配的权限数据, 以备后面路由生成权限后再导入
     * 3. 如果 item 被改但没同步到 auth_items 表，则会导致再次导入item_child的时候出错，所以导入前检查item是否存在
     */
    protected function saveToCache()
    {
        $item = AuthItemItem::find()->where(['type' => Item::TYPE_PERMISSION])->asArray()->all();
        $child_all = AuthItemChild::find()->asArray()->all();
        $child_role = AuthItemChild::find()->where( "auth_type !=" . Item::TYPE_PERMISSION)->asArray()->all();
        $cache = Yii::$app->cache;

        if (false === $cache->set('auth_item', $item, self::$defaultDuration)) {
            exit('save item cache error');
        }
        if (false === $cache->set('auth_item_child_all', $child_all, self::$defaultDuration)) {
            exit('save auth_item_child_all cache error');
        }
        if (false === $cache->set('auth_item_child_role', $child_role, self::$defaultDuration)) {
            exit('save auth_item_child_role cache error');
        }
        // $cache->getOrSet('item');
    }

    /**
     * 从缓存中读取并重新保存到item表 (非新增数据)
     * @throws \yii\db\Exception
     */
    protected function saveItemFromCache()
    {
        $items =  Yii::$app->cache->get('auth_item');
        $columns = [
            'id',
            'pid',
            'name' ,
            'type',
            'path',
            'description',
            'rule_name',
            'data',
            'created_at',
            'updated_at',
        ];
        $insert = Yii::$app->getDb()->createCommand()->batchInsert('auth_item', $columns, $items)->execute();
        echo "saveItemFromCache finished \r\n ";
    }

    /**
     * 导入之前分配的权限数据 (非新增数据)
     * @param $item_child array
     * @throws \yii\db\Exception
     */
    protected function saveItemChildFromCache($item_child)
    {
        $db = Yii::$app->getDb();
        // $item_child = Yii::$app->cache->get('auth_item_child');
        if (!empty($item_child)) {
            foreach ($item_child as $val) {
                if (empty(AuthItemItem::findOne(['name' => $val['parent']]))) continue;
                if (empty(AuthItemItem::findOne(['name' => $val['child']]))) continue;
                // existed should be continue
                if (!empty(AuthItemChild::findOne(['parent' => $val['parent'], 'child' => $val['child']]))) continue;
                $insert = $db->createCommand()->insert('auth_item_child', $val)->execute();
                if ($insert) echo "inserted {$val['parent']}, {$val['child']} to auth_item_child ...";
                echo "\r\n";
            }
        }
    }

    /**
     * 获取模块注释：保存模块为item 并返回模块名称以备给controller使用，因为模块是controller的父级
     *
     * @param string $controllerFile
     * @return string
     * @throws \ReflectionException
     */
    public function parseModuleFile($controllerFile)
    {
        // $controllerFile = 'backend/modules/sys/controllers/SysuserController.php';
        // 提取 url
        $modulePath = substr($controllerFile, 0, strrpos($controllerFile, 'controllers'));
        $moduleUrl = str_replace(static::$replace,'', $modulePath); // todo
        $moduleUrl = strtolower(FileHelpers::normalizePath($moduleUrl));
        $moduleClass =  $modulePath . 'Module';
        $moduleFile = Yii::getAlias('@' . $moduleClass) . '.php';
        if ( !file_exists($moduleFile)) exit($moduleFile . ' is not exists');

        $moduleClass = str_replace(['/'], ['\\'], $moduleClass);
        $module = new \ReflectionClass($moduleClass);
        $moduleComment = Helper::getComment($module);
        if (empty($moduleComment)) exit($moduleClass . ' module comment is empty');
        Yii::debug($moduleComment);

        $model = $this->saveAuthItem($moduleComment, 0, $moduleUrl, 1);
        if (!empty($model)) {
            $this->saveToAdmin($moduleComment);
        }
        return  !empty($model) ? $model : null;
    }

    /**
     * 分析controller 文件： 主要是文件夹注释、action 注释、action url
     * @param string $controllerFile
     * @param object $module
     * @throws \ReflectionException
     */
    public function parseControllerFile($controllerFile, $module = null)
    {
        // $controllerFile = 'backend/modules/sys/controllers/SysuserController.php';
        // 提取 url
        $controllerUrl = str_replace(static::$replace, '', $controllerFile); // todo
        $controllerUrl = strtolower(FileHelpers::normalizePath($controllerUrl));
        Yii::debug($controllerUrl);

        $className = str_replace(['/', '.php'], ['\\', ''], $controllerFile);
        $controlClass = new \ReflectionClass($className);
        $controllerComment = Helper::getComment($controlClass);
        Yii::debug($controllerComment);
        if (empty($controllerComment)) exit($controllerFile . '  controllerComment is empty!');

        $pid = !empty($module->id) ? $module->id : 0;
        // save action methods as auth_item
        $controlItem = $this->saveAuthItem($controllerComment, $pid, $controllerUrl, 1);
        if (empty($controlItem)) exit("{$controllerFile} save saveAuthItem failed !");

        if (!empty($module)) {
            // save module comment for controller url parent -> child to auth_child
            $this->saveItemChild($module->name, $controllerComment);
        }

        // save action methods as auth_item
        $methods = $controlClass->getMethods(\ReflectionMethod::IS_PUBLIC);
        foreach ($methods as $method)
        {
            if (!StringHelper::startsWith($method->name, 'action')) continue;
            if ($method->name === 'actions') continue; // todo

            // save action as auth_item
            $actionComment = Helper::getComment($method);
            if (empty($actionComment)) exit("{$method->name}  comment is empty !");
            $actionUrl = $controllerUrl . '/' . substr(strtolower($method->name), strlen('action'));
            $actionItem = $this->saveAuthItem($actionComment, $controlItem->id, '');
            if (empty($actionItem)) exit("{$actionComment} save saveAuthItem failed !");

            // save url as auth_item
            $urlItem = $this->saveAuthItem($actionUrl, $actionItem->id, '');
            if (empty($urlItem)) exit("{$urlItem} save saveAuthItem failed !");

            // set parent --> child
            $this->saveItemChild($controllerComment, $actionComment);
            // save action comment for action url parent -> child to auth_child
            $this->saveItemChild($actionComment, $actionUrl);

            // Yii::debug($actionComment);
            // Yii::debug($actionUrl);
        }
    }

    /**
     * 保存item
     * @param $name
     * @param int $pid
     * @param string $path
     * @param int $is_menu
     * @return string
     */
    public function saveAuthItem($name, $pid = 0, $path = '', $is_menu = 0)
    {
        if (!$name ) return '';
        $model = AuthItemItem::findOne(['name' => $name]);
        if (empty($model)) {
            $model = new AuthItemItem();
            $model->name = $name;
            $model->type = Item::TYPE_PERMISSION;
            $model->pid = $pid;
            $model->path = $path;
            $model->is_menu = $is_menu;
            if ($is_menu == 1) {
                $model->menu_level = substr_count($path, '/');
            }
            if (!$model->save()) {
                var_dump($model->getErrors()); die();
            }
            echo " save item '{$name}' item succeed ...\r\n ";
        } else {
            echo "item was existed \r\n ";
        }
        return $model;
    }

    /**
     * 保存到itemChild
     * @param $parent
     * @param $child
     * @param $auth_type
     * @return AuthItemChild|null|string|static
     */
    protected function saveItemChild($parent, $child, $auth_type = Item::TYPE_PERMISSION)
    {
        if (!($parent && $child)) return '';
        if ($parent == $child) exit('parent can not be same with child');
        $model = AuthItemChild::findOne(['parent' => $parent, 'child' => $child]);
        if (empty($model)) {
            $model = new AuthItemChild();
            $model->parent = $parent;
            $model->child = $child;
            $model->auth_type = $auth_type;

            if (!$model->save()) {
                var_dump($model->getErrors()); die();
            }
            echo " build '{$parent} --> {$child}' item child succeed ...\n ";
        } else {
            echo " the item_child '{$parent} --> {$child}' is existed ... \n ";
        }
        return $model;
    }

    /**
     * 默认将把所有权限赋予 administrator 角色
     * @param $child
     * @param string $parent
     * @return GenitemController|AuthItemChild|null|string
     */
    protected function saveToAdmin($child, $parent = 'Admin')
    {
        return $this->saveItemChild($parent, $child, Item::TYPE_ROLE);
    }

    /**
     * run 前先从数据库中删除 item 项, child 是中的字段外键来自item表所以同时会被删除
     * @throws \yii\db\Exception
     */
    protected function deleteItem()
    {
        $delete = " Delete from auth_item Where type=" . Item::TYPE_PERMISSION;
        Yii::$app->db->createCommand($delete)->execute();
    }







}