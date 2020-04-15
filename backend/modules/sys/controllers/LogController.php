<?php

namespace backend\modules\sys\controllers;

use backend\modules\sys\components\MenuHelper;
use Yii;
use backend\modules\sys\models\Log;
use backend\modules\sys\models\searchs\Log as LogSearch;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use backend\modules\sys\components\Helper;

/**
 * 日志管理
 *
 * LogController
 *
 */
class LogController extends Controller
{

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * 查看列表
     *
     * Lists all Log models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new LogSearch;
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

        return $this->render('index', [
                'dataProvider' => $dataProvider,
                'searchModel' => $searchModel,
        ]);
    }

    /**
     * 查看日志详情
     *
     * Displays a single Log model.
     * @param  integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Finds the Menu model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param  integer $id
     * @return Log the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Log::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
