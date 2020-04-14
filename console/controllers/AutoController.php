<?php

namespace console\controllers;

use Yii;
use yii\helpers\Console;
use yii\helpers\VarDumper;
use yii\db\Migration;
use yii\console\ExitCode;

class AutoController extends \yii\console\Controller
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










}
