<?php
use Yii;
use yii\bootstrap\Alert;
use yii\bootstrap\Button;
use yii\bootstrap\ButtonDropdown;
use yii\bootstrap\ButtonGroup;
use yii\bootstrap\Carousel;
use yii\bootstrap\Collapse;
use yii\bootstrap\Dropdown;
use yii\bootstrap\Html;
use yii\bootstrap\Modal;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\bootstrap\Progress;
use yii\bootstrap\Tabs;

?>
<h3>Alert</h3>
<?php
echo Alert::widget([
    'options' => [
        'class' => 'alert-info',
    ],
    'body' => 'This is alert tips',
]);

echo Button::widget([
    'label' => 'Action',
    'options' => ['class' => 'btn btn-primary'],
]);

echo ButtonDropdown::widget([
    'label' => 'Action',
    'dropdown' => [
        'items' => [
            ['label' => 'DropdownA', 'url' => '/'],
            ['label' => 'DropdownB', 'url' => '#'],
        ],
    ],
]);

echo ButtonGroup::widget([
    'buttons' => [
        ['label' => 'A'],
        ['label' => 'B'],
        ['label' => 'C', 'visible' => false],
        Button::widget(['label' => 'A']),
        ['label' => 'B'],
    ]
]);

//echo Carousel::widget([
//    'items' => [
//        // the item contains only the image
//        '<img src="/images/a.jpg" />',
//        // equivalent to the above
//        ['content' => '<img src="/images/b.jpg"/ >'],
//        // the item contains both the image and the caption
//        [
//            'content' => '<a href="http://www.baidu.com"><img src="/images/c.jpg" /></a>', //width="300" height="200"
//            'caption' => '<h4>This is title</h4><p>This is the caption text</p>',
//            'options' => [],
//          ],
//      ]
//  ]);

//echo Collapse::widget([
//    'items' => [
//        // equivalent to the above
//        [
//            'label' => 'Collapsible Group Item #1',
//            'content' => 'Anim pariatur cliche...',
//            // open its content by default
//            'contentOptions' => ['class' => 'in']
//        ],
//        // another group item
//        [
//            'label' => 'Collapsible Group Item #1',
//            'content' => 'Anim pariatur cliche...',
//            'contentOptions' => [],
//              'options' => [],
//          ],
//      ]
//  ]);

// echo Html::input();

Modal::begin([
    'header' => '<h2>Hello world</h2>',
    'toggleButton' => ['label' => 'popup', 'class' => 'btn btn-info'],
]);
echo 'Say hello...';
Modal::end();

?>
<div class="dropdown">
    <a href="#" data-toggle="dropdown" class="dropdown-toggle">Label <b class="caret"></b></a>
    <?php
    echo Dropdown::widget([
        'items' => [
            ['label' => 'DropdownA', 'url' => '/'],
            ['label' => 'DropdownB', 'url' => '#'],
        ],
    ]);
    ?>
</div>

<?php
echo Nav::widget([
    'items' => [
        [
            'label' => 'Home',
            'url' => ['site/index'],
            'linkOptions' => [],
        ],
        [
            'label' => 'Dropdown',
            'items' => [
                ['label' => 'Level 1 - Dropdown A', 'url' => '#'],
                '<li class="divider"></li>',
                '<li class="dropdown-header">Dropdown Header</li>',
                ['label' => 'Level 1 - Dropdown B', 'url' => '#'],
            ],
        ],
        [
            'label' => 'Login',
            'url' => ['site/login'],
            'visible' => Yii::$app->user->isGuest
        ],
    ],
    'options' => ['class' =>'nav-pills'], // set this to nav-tab to get tab-styled navigation
]);

NavBar::begin(['brandLabel' => 'NavBar Test']);
echo Nav::widget([
    'items' => [
        ['label' => 'Home', 'url' => ['/site/index']],
        ['label' => 'About', 'url' => ['/site/about']],
        [
            'label' => 'Dropdown',
            'items' => [
                ['label' => 'Level 1 - Dropdown A', 'url' => '#'],
                '<li class="divider"></li>',
                '<li class="dropdown-header">Dropdown Header</li>',
                ['label' => 'Level 1 - Dropdown B', 'url' => '#'],
            ],
        ],
    ],
    'options' => ['class' => 'navbar-nav'],
]);
NavBar::end();

// default with label
echo Progress::widget([
    'percent' => 60,
    'label' => 'test',
]);

// styled
echo Progress::widget([
    'percent' => 65,
    'barOptions' => ['class' => 'progress-bar-danger']
]);

echo Tabs::widget([
    'items' => [
        [
            'label' => 'One',
            'content' => 'Anim pariatur cliche',
            'active' => true
        ],
        [
            'label' => 'Two',
            'content' => 'Anim pariatur cliche',
            'headerOptions' => [],
            'options' => ['id' => 'myveryownID'],
        ],
        [
            'label' => 'Example',
            'url' => 'http://www.example.com',
        ],
        [
            'label' => 'Dropdown',
            'items' => [
                [
                    'label' => 'DropdownA',
                    'content' => 'DropdownA, Anim pariatur cliche',
                ],
                [
                    'label' => 'DropdownB',
                    'content' => 'DropdownB, Anim pariatur cliche',
                ],
                [
                    'label' => 'External Link',
                    'url' => 'http://www.example.com',
                ],
            ],
        ],
    ],
]);


?>


