<?php
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\captcha\Captcha;

$this->title = 'Contact Form';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="site-contact">
    <h3><?=$this->title?></h3>

    <p>
        If you have business inquiries or other questions, please fill out the following form to contact us. Thank you.
    </p>

    <div class="row">
        <div class="col-lg-6">
            <?php $form = ActiveForm::begin();?>

            <?= $form->field($model, 'name', [
                'inputOptions' => [
                    'is_unique' => 1,
                    'class' => 'form-control',
                    //'type' => 'password',
                    //'autofocus' => true,
                    // 'value' => 'hello'
                    'disabled' => true,
                ],
            ])
//            ->checkbox([
//                //'template' => '{input}{label}{error}',
//                'checked' => true,
//            ])
//            ->checkboxList([1 => 'CN', 2 => 'US'])->inline()
//            ->hint('this is name')
//            ->textInput(['autofocus' => true])
//            ->listBox([1=>'11111', 2=> '22222'])
//            ->dropDownList([1=> 'Male', 2=> 'Female', 3=> [20=>'No', 33=> 'abc']])
            ;
            ?>

            <?= $form->field($model, 'email'); ?>

            <?= $form->field($model, 'subject'); ?>

            <?=$form->field($model, 'date')
                ->widget(\yii\jui\DatePicker::className(), [
                    'options' => ['class' => 'form-control'],
                    //'value' => date('Y-m-d', time()),
                    'dateFormat' => 'php:Y-m-d',
                ]);?>

            <?= $form->field($model, 'body')->textarea(['rows' => 5]); ?>

            <?= $form->field($model, 'verifyCode')->widget(Captcha::className(), [
                'captchaAction' => '/site/captcha',
                'template' => '<div class="row"><div class="col-lg-3">{image}</div><div class="col-lg-6">{input}</div></div>',
            ]); ?>

            <div class="form-group">
                <?=Html::submitButton('Submit', ['class' => 'btn btn-info']);?>
            </div>

            <?php ActiveForm::end();?>
        </div>
    </div><!--- end row --->


</div>
