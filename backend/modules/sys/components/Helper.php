<?php

namespace backend\modules\sys\components;

use Yii;
use yii\caching\TagDependency;
use yii\helpers\ArrayHelper;

class Helper
{
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
        $docComment = Helper::DocParser($object->getDocComment());
        $comment = $docComment[$key] ?? ($docComment['long_description'] ?? '');

        if (strlen($comment) > 64) {
            echo 'Name should contain at most 64 characters';
            var_dump($object);
            exit;
        }
        return $comment;
    }

    /**
     * 生成树形结构：数组必须带有索引主键
     * @param $items
     * @param string $pid
     * @param string $id
     * @param string $children
     * @return array
     */
    public static function toTree($items, $pid = 'pid', $id = 'id', $children = 'children'){
        // convert into index array by id
        $items = ArrayHelper::index($items, $id);
        // make tree
        $tree = array();
        foreach($items as $item){
            if(isset($items[$item[$pid]])){
                $items[$item[$pid]][$children][] = &$items[$item[$id]];
            }else{
                $tree[] = &$items[$item[$id]];
            }
        }
        return $tree;
    }

    // -------------------------------------------------------------------------------------------------------------

    private static $_userRoutes = [];
    private static $_defaultRoutes;

    /**
     * Get assigned routes by default roles
     * @return array
     */
    protected static function getDefaultRoutes()
    {
        if (self::$_defaultRoutes === null) {
            $manager = Yii::$app->getAuthManager();
            $roles = $manager->defaultRoles;
            $cache = Configs::cache();
            if ($cache && ($routes = $cache->get($roles)) !== false) {
                self::$_defaultRoutes = $routes;
            } else {
                $permissions = self::$_defaultRoutes = [];
                foreach ($roles as $role) {
                    $permissions = array_merge($permissions, $manager->getPermissionsByRole($role));
                }
                foreach ($permissions as $item) {
                    if ($item->name[0] === '/') {
                        self::$_defaultRoutes[$item->name] = true;
                    }
                }
                if ($cache) {
                    $cache->set($roles, self::$_defaultRoutes, Configs::cacheDuration(), new TagDependency([
                        'tags' => Configs::CACHE_TAG
                    ]));
                }
            }
        }
        return self::$_defaultRoutes;
    }

    /**
     * Get assigned routes of user.
     * @param integer $userId
     * @return array
     */
    public static function getRoutesByUser($userId)
    {
        if (!isset(self::$_userRoutes[$userId])) {
            $cache = Configs::cache();
            if ($cache && ($routes = $cache->get([__METHOD__, $userId])) !== false) {
                self::$_userRoutes[$userId] = $routes;
            } else {
                $routes = static::getDefaultRoutes();
                $manager = Yii::$app->getAuthManager();
                foreach ($manager->getPermissionsByUser($userId) as $item) {
                    if ($item->name[0] === '/') {
                        $routes[$item->name] = true;
                    }
                }
                self::$_userRoutes[$userId] = $routes;
                if ($cache) {
                    $cache->set([__METHOD__, $userId], $routes, Configs::cacheDuration(), new TagDependency([
                        'tags' => Configs::CACHE_TAG
                    ]));
                }
            }
        }
        return self::$_userRoutes[$userId];
    }

    /**
     * Check access route for user.
     * @param integer $userId
     * @param string $route
     * @return boolean
     */
    public static function checkRoute($userId, $route)
    {
        $routes = static::getRoutesByUser($userId);
        $route = '/' . ltrim($route, '/');
        if (isset($routes[$route])) {
            return true;
        }
        while (($pos = strrpos($route, '/')) > 0) {
            $route = substr($route, 0, $pos);
            if (isset($routes[$route . '/*'])) {
                return true;
            }
        }
        return isset($routes['/*']);
    }

    /**
     * Filter menu items
     * @param type $userId
     * @param type $items
     */
    public static function filter($userId, $items)
    {
        return static::filterRecursive($items, $userId);
    }

    /**
     * Filter menu recursive
     * @param array $items
     * @param integer $userId
     * @return array
     */
    protected static function filterRecursive($items, $userId)
    {
        $result = [];
        foreach ($items as $i => $item) {
            $allow = false;
            if (is_array($item) && isset($item['url']) && isset($item['url'][0])) {
                $allow = static::checkRoute($userId, $item['url'][0]);
            } else {
                $allow = true;
            }
            if (isset($item['items']) && is_array($item['items'])) {
                $subItems = self::filterRecursive($item['items'], $userId);
                if (count($subItems)) {
                    $allow = true;
                }
                $item['items'] = $subItems;
            }
            if ($allow) {
                $result[$i] = $item;
            }
        }
        return $result;
    }

    /**
     * Use to invalidate cache.
     */
    public static function invalidate()
    {
        if (Configs::cache() !== null) {
            TagDependency::invalidate(Configs::cache(), Configs::CACHE_TAG);
        }
    }









}
