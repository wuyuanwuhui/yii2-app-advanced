<?php
use yii\helpers\Html;
use yii\widgets\DetailView;
$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('rbac-admin', 'Roles'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<?php
// $this->registerJsFile('@web/statics/my-assets/my.js', ['depends'=>'backend\assets\AppAsset']);
$this->registerJs(
    "$(function(){
        $('input:checkbox').click(function(){
            var is_menu = $(this).attr('is_menu')
            if (is_menu == 1) {
                var id = $(this).attr('id')
                var checked = $(this).prop('checked')
                $('input:checkbox').each(function(){
                    if($(this).attr('pid') == id){
                        $(this).prop('checked', checked);
                    }
                })
                // if pid = 0
                $('input[type=checkbox][value^='+id+'_]').prop('checked', checked);
            }
        });
    })"
);
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

                            <?=Html::beginForm('', '', [
                                'class' => "form-inline",
                            ])?>
                            <div class="panel panel-default">
                                <?=$itemTreeStr?>
                            </div><!-- end panel-default -->
                            <div class="form-group">
                                <div class="col-lg-offset-1 col-lg-10">
                                    <?=Html::submitButton('Submit' , [ 'class' => 'btn btn-primary']); ?>
                                </div>
                            </div>

                            <?=Html::endForm()?>

                    </div><!--- end row --->

                </div>
            </section>
        </div>

    </div>
</section>
