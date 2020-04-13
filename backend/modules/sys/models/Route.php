<?php
namespace backend\modules\sys\models;
/**
 * Route
 *
 */
use yii;

class Route extends yii\base\Model
{
    /**
     * @var string Route value. 
     */
    public $route;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return[
            [['route'],'safe'],
        ];
    }
}
