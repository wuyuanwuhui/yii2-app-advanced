<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/4/17 0017
 * Time: 23:38
 */

namespace common\helpers;

use yii\helpers\StringHelper;

class StringHelpers extends StringHelper
{

    /**
     * 打印出树形结构：前提数组本身已经是树形结构数组
     *
     * @param $arrTree
     * @param string $l
     * @return string
     */
    public static function printCheckboxesTree($arrTree, $l = '-|', $pids = '')
    {
        static $l = ''; static $str = ''; static $pids = '';

        foreach ($arrTree as $key => $val) {
            $ids = $val['id'];
            $str .= $l . $val['label'] . "($pids$ids)" . "<br/>";
            // 如果有子节点则递归
            if (!empty($arrTree[$key]['children']) && is_array($arrTree[$key]['children'])) {
                $l .= '-|';  // 加前缀
                $pids .= $ids . '_'; // 并带上级pid
                self::printCheckboxesTree($arrTree[$key]['children'], $l, $pids);
            }
        }
        // 如果无子节点则置空变量
        $l = $pids = '';
        // 返回所拼接的字符串
        return $str;
    }




}