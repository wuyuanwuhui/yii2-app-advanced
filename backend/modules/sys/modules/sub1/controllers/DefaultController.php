<?php

namespace backend\modules\sys\modules\sub1\controllers;

use yii\web\Controller;

/**
 * Default controller for the `sub1` module
 */
class DefaultController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        return 'default index';
    }
}
