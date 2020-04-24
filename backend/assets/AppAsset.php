<?php

namespace backend\assets;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'statics/css/bootstrap.min.css',
        'statics/css/bootstrap-reset.css',
        'statics/assets/font-awesome/css/font-awesome.css',
        'statics/css/style.css',
        'statics/css/style-responsive.css',

        'statics/css/my-style.css',
    ];
    public $js = [
        'statics/js/jquery.dcjqaccordion.2.7.js',
        'statics/js/jquery.scrollTo.min.js',
        'statics/js/jquery.nicescroll.js',
        'statics/js/jquery.sparkline.js',
        'statics/js/slidebars.min.js',
        'statics/js/common-scripts.js',

        // 'statics/my-assets/my.js',   // 自动更新问题，必须删除源文件再刷新一下再重新新建才行, 重新命名是一种方法
    ];
    public $depends = [
        'yii\web\YiiAsset',
//        'yii\bootstrap\BootstrapAsset',
        'yii\bootstrap\BootstrapPluginAsset',
    ];
}
