<?php

namespace backend\modules\sys;

/**
 * AutocompleteAsset
 *
 * @author Misbahul D Munir <misbahuldmunir@gmail.com>
 * @since 1.0
 */
class AutocompleteAsset extends \yii\web\AssetBundle
{
    /**
     * @inheritdoc
     */
    public $sourcePath = '@backend/modules/sys/assets';

    /**
     * @inheritdoc
     */
    public $css = [
        'jquery-ui.css',
    ];

    /**
     * @inheritdoc
     */
    public $js = [
        'jquery-ui.js',
    ];

    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
