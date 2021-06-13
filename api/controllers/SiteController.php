<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-08-30 18:10
 */
namespace api\controllers;

use Yii;
use api\models\form\SignupForm;
use common\models\User;
use api\models\form\LoginForm;
use yii\base\UserException;
use yii\web\HttpException;
use yii\web\IdentityInterface;
use yii\web\Response;

class SiteController extends \yii\rest\ActiveController
{
    public $modelClass = "common\models\Article";

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['contentNegotiator']['formats']['text/html'] = Response::FORMAT_JSON;//默认浏览器打开返回json
        return $behaviors;
    }

    public function actions()
    {
        return [];
    }

    public function verbs()
    {
        return [
            'index' => ['GET', 'HEAD'],
            'login' => ['POST'],
            'register' => ['POST'],
        ];
    }

    public function actionIndex()
    {
        return [
            "feehi api service"
        ];
    }

    public function actionVue()
    {
        return [
            "feehi api service"
        ];
    }


    /**
     * api unify exception handler
     *
     * @return array
     */
    public function actionError()
    {
        if (($exception = Yii::$app->getErrorHandler()->exception) === null) {
            // action has been invoked not from error handler, but by direct route, so we display '404 Not Found'
            $exception = new HttpException(404, Yii::t('yii', 'Page not found.'));
        }

        if ($exception instanceof HttpException) {
            $code = $exception->statusCode;
        } else {
            $code = $exception->getCode();
        }
        if ($exception instanceof Exception) {
            $name = $exception->getName();
        } else {
            $name = Yii::t('yii', 'Error');
        }
        if ($code) {
            $name .= " (#$code)";
        }

        if ($exception instanceof UserException) {
            $message = $exception->getMessage();
        } else {
            $message = Yii::t('yii', 'An internal server error occurred.');
        }
        $statusCode = $exception->statusCode ? $exception->statusCode : 500;
        return [
            'code' => $statusCode,
            'name' => $name,
            'message' => $message
        ];
    }



}
