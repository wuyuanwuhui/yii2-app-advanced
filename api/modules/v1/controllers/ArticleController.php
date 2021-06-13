<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2019-05-11 17:33
 */

namespace api\modules\v1\controllers;


use yii\web\HttpException;

class ArticleController extends \api\controllers\ArticleController
{

    public function actionMatchlist()
    {
        self::error('what the fuck !');
    }

    /**
     * @param string $message      error message
     * @param int $code             error code
     * @param int $status       HTTP status code
     * @throws \yii\web\HttpException
     */
    public static function error($message, $code = 0, $status = 200){
        throw new \yii\web\HttpException ($status, $message, $code);
    }



}