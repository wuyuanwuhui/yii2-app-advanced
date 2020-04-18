<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\Json;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model backend\modules\sys\models\AuthItem */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('rbac-admin', 'Roles'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

//$opts = Json::htmlEncode([
//        'assignUrl' => Url::to(['assign', 'id' => $model->name]),
//        'items' => $items
//    ]);
//$this->registerJs("var _opts = {$opts};");
//$this->registerJs($this->render('_script.js'));
?>

<section class="wrapper site-min-height">
    <div class="row">
        <div class="col-lg-12">
            <section class="panel">
                <header class="panel-heading">
                    <?=$this->title?>
                </header>
                <div class="panel-body">
                    <p>
                        <?= Html::a(Yii::t('rbac-admin', 'Update'), ['update', 'id' => $model->name], ['class' => 'btn btn-primary']) ?>
                        <?php
                        echo Html::a(Yii::t('rbac-admin', 'Delete'), ['delete', 'id' => $model->name], [
                            'class' => 'btn btn-danger',
                            'data-confirm' => Yii::t('rbac-admin', 'Are you sure to delete this item?'),
                            'data-method' => 'post',
                        ]);
                        ?>
                    </p>
                    <div class="row">
                        <div class="col-lg-11">
                            <?=
                            DetailView::widget([
                                'model' => $model,
                                'attributes' => [
                                    'name',
                                    'description:ntext',
                                    'ruleName',
                                    'data:ntext',
                                ],
                                'template' => '<tr><th style="width:25%">{label}</th><td>{value}</td></tr>'
                            ]);
                            ?>
                        </div>
                    </div>

                    <div class="row">


                        <div class="col-lg-12">
                            <?=Html::beginForm()?>

                            <?=$itemTreeStr?>

                            <div class="form-group">
                                <div class="col-lg-offset-2 col-lg-10">
                                    <?=Html::submitButton('Submit' , [ 'class' => 'btn btn-primary']); ?>
                                </div>
                            </div>
                            <?=Html::endForm()?>

                        </div>


                    </div><!--- end row --->

                </div>
            </section>
        </div>

    </div>
</section>
