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


    <fieldset>
        <legend >
            <label><input type="checkbox" name="items[系统管理]" value="943" has_children="0"> 系统管理</label>
        </legend>
        <fieldset >
            <legend>
                <label><input type="checkbox" name="items[日志管理]" value="943_944" has_children="0"> 日志管理</label>
            </legend>
            <label><input type="checkbox" name="items[查看列表]" value="943_944_945" has_children="0"> 查看列表</label>
            <label><input type="checkbox" name="items[查看日志详情]" value="943_944_947" has_children="0"> 查看日志详情</label>
        </fieldset>
        <fieldset>
            <label><input type="checkbox" name="items[角色管理]" value="943_949" has_children="0"> 角色管理</label>
        </fieldset>
    </fieldset>




                            <div class="col-sm-6"> </div>

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
