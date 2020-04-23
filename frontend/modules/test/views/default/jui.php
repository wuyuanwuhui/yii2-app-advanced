<?php
use  yii\jui\Accordion;
use  yii\jui\Dialog;
use  yii\jui\Draggable;
use  yii\jui\Droppable;
use  yii\jui\ProgressBar;
use  yii\jui\Resizable;
use  yii\jui\Selectable;
use  yii\jui\SliderInput;
use  yii\jui\Spinner;
use  yii\jui\Tabs;

echo Accordion::widget([
    'items' => [
        [
            'header' => 'Section 1',
            'content' => 'Mauris mauris ante, blandit et, ultrices a, suscipit eget...',
        ],
        [
            'header' => 'Section 2',
            'headerOptions' => ['tag' => 'h3'],
            'content' => 'Sed non urna. Phasellus eu ligula. Vestibulum sit amet purus...',
            'options' => ['tag' => 'div'],
        ],
    ],
    'options' => ['tag' => 'div'],
    'itemOptions' => ['tag' => 'div'],
    'headerOptions' => ['tag' => 'h3'],
    'clientOptions' => ['collapsible' => false],
]);

//Dialog::begin([
//    'clientOptions' => [
//        'modal' => true,
//    ],
//]);
//
//echo 'Dialog contents here...';
//
//Dialog::end();

//Draggable::begin([
//    'clientOptions' => ['grid' => [50, 20]],
//]);
//
//echo 'Draggable contents here...';
//
//Draggable::end();


//Resizable::begin([
//    'clientOptions' => [
//        'grid' => [20, 10],
//    ],
//]);
//
//echo 'Resizable contents here...';
//
//Resizable::end();


//echo Sortable::widget([
//    'items' => [
//        'Item 1',
//        ['content' => 'Item2'],
//        [
//            'content' => 'Item3',
//            'options' => ['tag' => 'li'],
//        ],
//    ],
//    'options' => ['tag' => 'ul'],
//    'itemOptions' => ['tag' => 'li'],
//    'clientOptions' => ['cursor' => 'move'],
//]);

echo Spinner::widget([
    'name'  => 'country',
    'clientOptions' => ['step' => 2],
]);

echo Tabs::widget([
    'items' => [
        [
            'label' => 'Tab one',
            'content' => 'Mauris mauris ante, blandit et, ultrices a, suscipit eget...',
        ],
        [
            'label' => 'Tab two',
            'content' => 'Sed non urna. Phasellus eu ligula. Vestibulum sit amet purus...',
            'options' => ['tag' => 'div'],
            'headerOptions' => ['class' => 'my-class'],
        ],
        [
            'label' => 'Tab with custom id',
            'content' => 'Morbi tincidunt, dui sit amet facilisis feugiat...',
            'options' => ['id' => 'my-tab'],
        ],
        [
            'label' => 'Ajax tab',
            'url' => ['ajax/content'],
        ],
    ],
    'options' => ['tag' => 'div'],
    'itemOptions' => ['tag' => 'div'],
    'headerOptions' => ['class' => 'my-class'],
    'clientOptions' => ['collapsible' => false],
]);



?>


