<?php

namespace frontend\modules\test\controllers;

use Yii;
use yii\web\Controller;
use frontend\models\ContactForm;

/**
 * Default controller for the `test` module
 */
class DefaultController extends Controller
{

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        $model = new ContactForm;
        $model->setAttributes([
            'name' => 'HY',
            'date' => date('Y-m-d', time())
        ], true); // if true the attribute must be in rules

        Yii::$app->session->setFlash('success', 'Thank you for contacting us. ');

        return $this->render('index', ['model' => $model]);
    }

    public function actionHtml()
    {
        return $this->render('html');
    }

    public function actionJui()
    {
        return $this->render('jui');
    }

}
