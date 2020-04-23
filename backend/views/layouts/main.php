<?php

/* @var $this \yii\web\View */
/* @var $content string */

use backend\assets\AppAsset;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use common\widgets\Alert;
use yii\helpers\Url;
use izyue\admin\components\MenuHelper;
use yii\helpers\VarDumper;
use console\models\AuthItemItem;
use common\helpers\ArrayHelpers;

AppAsset::register($this);

$route = Yii::$app->controller->getRoute();
$routeArray = explode('/', $route);
array_pop($routeArray);
$controllerName = implode('/', $routeArray);

$this->registerCssFile('@web/statics/css/slidebars.css', ['depends'=>'backend\assets\AppAsset']);
$roles = array_keys(Yii::$app->getAuthManager()->getRolesByUser(Yii::$app->user->id));
$where = "";

// Where 如果是非admin则过滤菜单ids
if (!in_array(Yii::$app->params['adminRole'], $roles)) {
    $roles = implode("','", $roles);
    $sql = "select menuids from role_item_ids Where role in ('$roles')";
    $items = Yii::$app->db->createCommand($sql)->queryColumn();
    $ids = [];
    foreach($items as $val) {
        $ids[] = $val;
    }
    $ids = implode(',', array_unique($ids));
    if (!empty($ids)) $where = " id in ($ids) ";
}
$items = AuthItemItem::find()->select('id, pid, name as label')
    ->addSelect(['url' => "CONCAT(path, '/index')"])
    ->where('is_menu=1 And menu_level <=2')
    ->andWhere($where)
    ->orderBy('id Asc')
    ->asArray()
    ->all();

$menuRows = ArrayHelpers::toTree($items, 'pid', 'id', 'items');
//VarDumper::dump($menuRows, 100, true);

function isSubUrl($menuArray, $route)
{
    if (isset($menuArray) && is_array($menuArray)) {
        if (isset($menuArray['items'])) {
            foreach ($menuArray['items'] as $item)
            {
                if (isSubUrl($item, $route)) {
                    return true;
                }
            }
        } else {
            $url = is_array($menuArray['url']) ? $menuArray['url'][0] : $menuArray['url'];
            if (strpos($url, $route)) {
                return true;
            }
        }
    } else {
        $url = is_array($menuArray['url']) ? $menuArray['url'][0] : $menuArray['url'];
        if (strpos($url, $route)) {
            return true;
        }
    }
    return false;
}

function isSubMenu($menuArray, $controllerName)
{
    if (isset($menuArray) && is_array($menuArray)) {
        if (isset($menuArray['items'])) {
            foreach ($menuArray['items'] as $item)
            {
                if (isSubMenu($item, $controllerName)) {
                    return true;
                }
            }
        } else {
            $url = is_array($menuArray['url']) ? $menuArray['url'][0] : $menuArray['url'];
            if (strpos($url, $controllerName.'/')) {
                return true;
            }
        }
    } else {
        $url = is_array($menuArray['url']) ? $menuArray['url'][0] : $menuArray['url'];
        if (strpos($url, $controllerName.'/')) {
            return true;
        }
    }
    return false;
}

function initMenu($menuArray, $controllerName, $isSubUrl, $isShowIcon=false)
{
    if (isset($menuArray) && is_array($menuArray)) {

        $url = is_array($menuArray['url']) ? $menuArray['url'][0] : $menuArray['url'];

        if (empty($isSubUrl)) {
            $isSubMenu = isSubMenu($menuArray, $controllerName);
        } else {
            $route = Yii::$app->controller->getRoute();
            $routePath = substr($route, 0, strrpos($route, '/')); // only path accept action
            $isSubMenu = isSubUrl($menuArray, $routePath);
        }
        if ($isSubMenu) {
            $class = ' active ';
        } else {
            $class = '';
        }

        if (isset($menuArray['items'])) {
            echo '<li class="sub-menu">';
        } else {
            echo '<li class="'.$class.'">';
        }
        $url = $url == '#' ? 'javascript:;' : Url::toRoute($url);
        echo '<a href="'.$url.'"  class="'.$class.'">'.($isShowIcon ? '<i class="fa fa-sitemap"></i>' : '').'<span>'.$menuArray['label'].'</span></a>';

        if (isset($menuArray['items'])) {
            echo '<ul class="sub">';
            foreach ($menuArray['items'] as $item)
            {
                echo initMenu($item, $controllerName, $isSubUrl);
            }
            echo '</ul>';
        }

        echo '</li>';
    }
}

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<section id="container" >
    <!--header start-->
    <header class="header white-bg">
        <div class="sidebar-toggle-box">
            <div class="fa fa-bars tooltips" data-placement="right" data-original-title="Toggle Navigation"></div>
        </div>
        <!--logo start-->
        <a href="<?=Url::home()?>" class="logo">AD<span>MIN</span></a>
        <!--logo end-->
        <div class="nav notify-row" id="top_menu">
            <!--  notification start -->
            <ul class="nav top-menu">
                <!-- notification dropdown start-->
                <li id="header_notification_bar" class="dropdown">
                    <a data-toggle="dropdown" class="dropdown-toggle" href="#">

                        <i class="fa fa-bell-o"></i>
                        <span class="badge bg-warning">7</span>
                    </a>
                    <ul class="dropdown-menu extended notification">
                        <div class="notify-arrow notify-arrow-yellow"></div>
                        <li>
                            <p class="yellow">You have 7 new notifications</p>
                        </li>
                        <li>
                            <a href="#">See all notifications</a>
                        </li>
                    </ul>
                </li>
                <!-- notification dropdown end -->
            </ul>
            <!--  notification end -->
        </div>
        <div class="top-nav ">
            <!--search & user info start-->
            <ul class="nav pull-right top-menu">
                <li>
                    <input type="text" class="form-control search" placeholder="Search">
                </li>
                <!-- user login dropdown start-->
                <li class="dropdown">
                    <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                        <img alt="" src="<?=Yii::getAlias('@web')?>/statics/img/avatar1_small.jpg">
                        <span class="username"><?=Yii::$app->user->identity['username']?></span>
                        <b class="caret"></b>
                    </a>
                    <ul class="dropdown-menu extended logout">
                        <div class="log-arrow-up"></div>
                        <li><a href="#"><i class=" fa fa-suitcase"></i>Profile</a></li>
                        <li><a href="#"><i class="fa fa-cog"></i> Settings</a></li>
                        <li><a href="#"><i class="fa fa-bell-o"></i> Notification</a></li>
                        <li><a href="<?=Url::toRoute('/site/logout')?>" data-method="post"><i class="fa fa-key"></i> Log Out</a></li>
                    </ul>
                </li>
                <li class="sb-toggle-right">
                    <i class="fa  fa-align-right"></i>
                </li>
                <!-- user login dropdown end -->
            </ul>
            <!--search & user info end-->
        </div>
    </header>
    <!--header end-->
    <!--sidebar start-->
    <aside>
        <div id="sidebar"  class="nav-collapse ">
            <!-- sidebar menu start-->
            <ul class="sidebar-menu" id="nav-accordion">
                <li>
                    <a class="<?=($controllerName == 'site' ? 'active' : '')?>" href="<?=Url::home()?>">
                        <i class="fa fa-dashboard"></i>
                        <span><?=Yii::t('admin', 'dashboard')?></span>
                    </a>
                </li>
                <?php
                    if(isset($menuRows)){

                        $isSubUrl = false;
                        foreach($menuRows as $menuRow){

                            $isSubUrl = isSubUrl($menuRow, $route);

                            if ($isSubUrl) {
                                break;
                            }


                        }
                        foreach($menuRows as $menuRow){

                            initMenu($menuRow, $controllerName, $isSubUrl, true);
                        }
                    }
                ?>

            </ul>
            <!-- sidebar menu end-->
        </div>
    </aside>
    <!--sidebar end-->
    <!--main content start-->
    <section id="main-content">
        <?=$content?>
    </section>
    <!--main content end-->

    <!-- Right Slidebar start -->
    <div class="sb-slidebar sb-right sb-style-overlay">
        <h5 class="side-title">Online Customers</h5>
        <ul class="quick-chat-list">
            <li class="online">
                <div class="media">
                    <a href="#" class="pull-left media-thumb">
                        <img alt="" src="<?=Yii::getAlias('@web')?>/statics/img/chat-avatar2.jpg" class="media-object">
                    </a>
                    <div class="media-body">
                        <strong>John Doe</strong>
                        <small>Dream Land, AU</small>
                    </div>
                </div><!-- media -->
            </li>
        </ul>
        <h5 class="side-title"> pending Task</h5>

    </div>
    <!-- Right Slidebar end -->

    <!--footer start-->
    <footer class="site-footer">
        <div class="text-center">
            2013 &copy; FlatLab by VectorLab.
            <a href="#" class="go-top">
                <i class="fa fa-angle-up"></i>
            </a>
        </div>
    </footer>
    <!--footer end-->
</section>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
