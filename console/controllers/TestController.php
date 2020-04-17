<?php

namespace console\controllers;

use Yii;
use yii\helpers\Console;
use yii\helpers\VarDumper;
use yii\db\Migration;
use yii\console\ExitCode;
use common\helpers\ArrayHelpers;
use common\helpers\FileHelpers;

class TestController extends \yii\console\Controller
{

    public $color = false;

    public function actionIndex()
    {
        fwrite(\STDOUT, "What's your problem ? \n");
        while ($str = fread(\STDIN, 1000)){
            // quit or exit
            $enter = trim($str);
            if (in_array($enter, ['quit', 'exit'])) {
                if ($this->confirm("Are you sure to quit ? \n")) {
                    echo "You had quit ... done \n";
                    return ExitCode::OK;
                }
            }
            else {
                echo "Your enter is __ {$str} \n";
            }
        }
    }

    public $time = 1;
    public $config;
    public $dbName;

    public function options($actionID)
    {
        //return parent::options($actionID);
        return ['time', 'config', 'dbName'];
    }

    public function actionUsed()
    {
        echo $this->ansiFormat('This will be red and underlined.', Console::FG_RED, Console::UNDERLINE);
        $this->stdout("Hello");
        $this->stderr("error");
        $prompt = $this->prompt("Please enter your ID: ", ['required' => true]);
        $this->select('select one', ['yes', 'no']);
        echo "Your id is is $prompt";

        VarDumper::dump($this->getRoute());
    }

    public function actionProgress()
    {
        Console::startProgress(0, 100);

        for ($n = 1; $n <= 100; $n++)
        {
            usleep(10000);
            Console::updateProgress($n, 100);
        }
        Console::endProgress();
    }


    /**
     * backup something
     *
     * @param $path
     * @param int $deepth
     */
    public function actionBackup($path, $deepth = 1)
     {
         echo $path .'----'. $deepth . "\n";
         echo Console::moveCursorUp();
         echo $path .'----'. $deepth . "\n";
         echo $path .'----'. $deepth . "\n";
         echo Console::renderColoredString("%yHello%g");
         echo Console::prompt('Please input some thing here ...');
         echo $string = Console::stdin();
         echo Console::confirm('You sure ?');
         Console::stdout($string);
         // echo Console::error('error: You can not find it here .');
     }

    /**
     * @throws \yii\db\Exception
     */
     public function actionCreateTable()
     {
         $migration = new Migration();
         $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';

         $result = Yii::$app->db->createCommand()
             ->createTable('{{customer}}', [
                 'id' => $migration->primaryKey(),
                 'username' => $migration->string(32),
                 'age' => $migration->integer(2)
             ], $tableOptions)
             ->execute();

         var_dump($result);exit;
     }

    public function actionHua()
    {

        $className = 'backend\controllers\TestController';
//        $class = new \ReflectionClass($className);
//        $method = $class->getMethod('actionIndex');
//        $comment = Helper::getComment($method);

//        $className = 'backend\components\CenterComponent';
//        $class = new \ReflectionClass(new $className);
//        //var_dump(strlen('Controller')); die();
//        $method = $class->getMethod('hello');
//        $comment = Helper::getComment($method);
//        //echo Yii::getVersion();
//        var_dump($comment); die();

        //$dsn = 'mongodb://root:root@localhost:27017/admin';
        //$connection = new Connection(['dsn' => $dsn]);
        //var_dump($connection);die();
        //$result = $connection->getDatabase('admin')->getCollection('articles')->findOne();
//        Yii::beginProfile('begin mongodb get article');
//        $result = Yii::$app->mongodb->getDatabase('admin')->getCollection('articles')->findOne();
//        Yii::beginProfile('begin mongodb get article');
//
//        var_dump($result);

        $d = 100;
        $arr = ['a', 'b', 'c' ];

        var_dump($arr);

        // ok

    }

    // ---------------------------------------------------------------------------------------------------------------

    public function actionTest()
    {
        $str = '/vagrant/www/yii2-app-advanced/backend/modules/sys/modules/sub1/controllers/DefaultController.php';
//        var_dump(substr($str, 0, strrpos($str, 'controllers')));
//        $arr = [
//            ['id' => 100, 'n' => 'abc',],
//            ['id' => 105, 'n' => 'edf',],
//        ];
//        foreach ($arr as &$value) {
//            // unset($value);
//        }
//        print_r($arr);
//        foreach($arr as $value) {}
//        print_r($arr);

        $dir = '/vagrant/www/yii2-app-advanced/backend/modules/sys';
        $files = FileHelpers::findFiles($dir, [
            // 'filter' => function($path) {},
            'only' => ['pattern' => '*Controller.php'],
            'recursive' => true,
        ]);
        Yii::debug($files);
    }

    public function actionTree()
    {
        $arr = [
            ['id' => 1, 'pid' => 0, 'name' => '系统管理'],
            ['id' => 10, 'pid' => 1, 'name' => '用户管理'],
            ['id' => 20, 'pid' => 10, 'name' => '用户创建'],
            ['id' => 30, 'pid' => 10, 'name' => '用户修改'],
            ['id' => 50, 'pid' => 30, 'name' => '用户修改制定'],

            ['id' => 2, 'pid' => 1, 'name' => '游戏管理'],
            ['id' => 200, 'pid' => 2, 'name' => '游戏信息'],
            ['id' => 210, 'pid' => 2, 'name' => '游戏对接'],
            ['id' => 310, 'pid' => 210, 'name' => '对接腾讯'],
        ];
        $treeArr = ArrayHelpers::toTree($arr);
        echo FileHelpers::printTree($treeArr);
    }

    public function actionMenu()
    {
        $arr = [
            '1_20_27', '1_20_25', '1_20_30', '1_20_33',
            '2_30_50',  '2_30_55',  '2_30_50_100',
            '3_39_70_555',
        ];
        // ids: 1, 20, 2, 30, 3, 39

        // 对应的表数据取出来 id, pid, name
        /**
         * 1, 0, 系统管理
         * 2, 0, 游戏管理
         * 3, 0, 运营管理
         * 20, 1, 用户管理
         * 27, 20, 用户修改
         */
        $ids = [];
        // 取出两级
        foreach ($arr as $key => $val ) {
            $temp = explode('_', $val);
            $id = $temp[count($temp)-1];  // save assign
            $ids[]= $temp[0];
            $ids[]= $temp[1];
        }
        $ids = array_unique($ids);
        // save ids for role menu ids
        print_r($ids);
    }

    public function actionT()
    {
        // var_dump(Yii::$app->cache->get('auth_item_child_all'));
        // var_dump(Yii::$app->cache->get('auth_item'));
        //var_dump(Yii::$app->cache->get('auth_item_child_role'));

        //$optionValues = $this->getOptionValues('time');
        //var_dump($optionValues);
        $arr = ['a', 'b', 'c'];
        array_pop($arr);
        var_dump($arr);

        array_shift($arr);
        var_dump($arr);
    }





}
