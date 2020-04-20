<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/4/17 0017
 * Time: 23:38
 */

namespace common\helpers;

use yii\helpers\StringHelper;
use yii\helpers\Html;

class StringHelpers extends StringHelper
{

    /**
     * 打印出树状图：前提数组本身已经是树形结构数组
     * @param $arr
     * @param int $level
     * @param array $ppid
     * @return string
     */
    public static function printCheckboxesTree($arr, $level = 0, $ppid = [])
    {
        static $str = '';
        foreach ($arr as $key => $val)
        {
            if ($val['pid'] != 0) {
                $ppid[] = $val['pid'];
                $ppid = array_unique($ppid);
            }
            // $prefix = str_repeat('-|', $level);
            $pids = '';
            if (!empty($ppid)) {
                $pids = implode('_', $ppid) . '_';
            }
            $checkbox = Html::checkbox("items[{$val['label']}]", false, [
                'label' => $val['label'],
                'value' => "$pids{$val['id']}",
                'is_menu' => $val['is_menu'],
                'id' => $val['id'],
                'pid' => $val['pid'],
                // 'checked' => 'true' // depend for permission table
            ]);

            // 如果有子节点则递归
            if (!empty($arr[$key]['children']) && is_array($arr[$key]['children'])) {
                $str .= "<div class=\"panel-heading\">{$checkbox}</div>";
                $str .= "<div class=\"panel-body\">";
                self::printCheckboxesTree($arr[$key]['children'],$level+1, $ppid);
            } else {
                $str .= "<div class=\"checkbox\">" . $checkbox . '</div>';
            }
            if ($val['is_menu'] == 1) {
                $str .= '</div>';
            }
        }
        // 返回所拼接的字符串
        return $str;
    }

    // ---------------------------------------------------------------------------------------------------------------

    /**
     * 打印出树形结构：前提数组本身已经是树形结构数组
     * @param $arr
     * @param int $level
     * @param array $ppid
     * @return string
     */
    public static function printTree($arr, $level = 0, $ppid = [])
    {
        static $str = '';
        foreach ($arr as $key => $val)
        {
            if ($val['pid'] != 0) {
                $ppid[] = $val['pid'];
                $ppid = array_unique($ppid);
            }
            $prefix = str_repeat('-|', $level);
            // echo "{$val['name']} and level is : {$level}, prefix is: {$prefix} \r\n";
            $pids = '';
            if (!empty($ppid)) {
                $pids = implode('_', $ppid) . '_';
            }
            $str .= $prefix . $val['name'] . "($pids{$val['id']})" . "\r\n";

            // 如果有子节点则递归
            if (!empty($arr[$key]['children']) && is_array($arr[$key]['children'])) {
                self::printTree($arr[$key]['children'],$level+1, $ppid);
            }
        }
        // 返回所拼接的字符串
        return $str;
    }






}