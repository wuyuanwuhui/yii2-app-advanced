<?php

namespace backend\modules\sys\controllers;

use common\helpers\ArrayHelpers;
use common\helpers\StringHelpers;
use yii\rbac\Item;
use Yii;
use backend\modules\sys\models\AuthItem;
use backend\modules\sys\models\searchs\AuthItem as AuthItemSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use backend\modules\sys\components\Helper;
use console\models\AuthItemItem;
use console\models\AuthItemChild;
use yii\helpers\VarDumper;
use backend\modules\sys\models\RoleItemIds;

/**
 * 角色管理
 *
 * RoleController implements the CRUD actions for AuthItem model.
 *
 */
class RoleController extends Controller
{
    public $type = Item::TYPE_ROLE;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * 角色列表
     *
     * Lists all AuthItem models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AuthItemSearch(['type' => $this->type]);
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * 查看角色详情
     *
     * Displays a single AuthItem model.
     * @param  string $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        $items = AuthItemItem::find()->select('id, pid, name as label, is_menu')
            ->where(['type' => Item::TYPE_PERMISSION])->andWhere("left(`name`, 1) != '/'")->orderBy('id Asc')->asArray()
            ->all();
        $id = trim($id);
        $itemTreeStr = '';
        $roleItemIdsModel = RoleItemIds::findOne(['role' => $id]);
        if (!empty($items)) {
            $itemids = $roleItemIdsModel->itemids ?? '';
            $itemids = explode(',', $itemids);
            $itemTree = ArrayHelpers::toTree($items);
            $itemTreeStr = StringHelpers::printCheckboxesTree($id, $itemids, $itemTree);
        }
        // submit post
        $post = Yii::$app->request->post();
        if (!empty($post)) {
            if ($id && ($id != Yii::$app->params['adminRole'])) {
                if (!$roleItemIdsModel) $roleItemIdsModel = new RoleItemIds;
                $postItems = $post['items'];
                AuthItemChild::deleteAll(['parent' => $id]);
                $authType = Item::TYPE_PERMISSION;
                $menuids = $itemids = [];

                foreach($postItems as $key => $val)
                {
                    $clondeModel = new AuthItemChild;
                    $clondeModel->parent = $id;
                    $clondeModel->child = $key;
                    $clondeModel->auth_type = $authType;
                    if ( !$clondeModel->save()) {
                        var_dump($clondeModel->getErrors()); exit;
                    }
                    $ids = explode('_', $val);
                    if (!empty($ids)) {
                        if (!empty($ids[0])) $menuids[] = $ids[0];
                        if (!empty($ids[1])) $menuids[] = $ids[1];
                        $itemids[] = $ids[count($ids)-1];
                    }
                }
                $menuids = array_unique($menuids);
                $roleItemIdsModel->role = $id;
                // 保存到菜单权限表：菜单ids(存两级即可)、权限ids
                $roleItemIdsModel->menuids = implode(',', $menuids);
                $roleItemIdsModel->itemids = implode(',', $itemids);
                if (!$roleItemIdsModel->save()) {
                    var_dump($roleItemIdsModel->getErrors()); exit;
                }
            }
            return $this->refresh(); // refresh to get new data
        }
        return $this->render('view', ['model' => $model, 'itemTreeStr' => $itemTreeStr]);
    }

    /**
     * 新增角色
     *
     * Creates a new AuthItem model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new AuthItem(null);
        $model->type = $this->type;
        if ($model->load(Yii::$app->getRequest()->post()) && $model->save()) {
            Helper::invalidate();

            return $this->redirect(['view', 'id' => $model->name]);
        } else {
            return $this->render('create', ['model' => $model]);
        }
    }

    /**
     * 修改角色
     *
     * Updates an existing AuthItem model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param  string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->getRequest()->post()) && $model->save()) {
            Helper::invalidate();

            return $this->redirect(['view', 'id' => $model->name]);
        }

        return $this->render('update', ['model' => $model]);
    }

    /**
     * 删除角色
     *
     * Deletes an existing AuthItem model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param  string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        if (Yii::$app->params['adminRole'] == $id) { // 不可删除

        } else {
            $model = $this->findModel($id);
            Yii::$app->getAuthManager()->remove($model->item);
            Helper::invalidate();
        }

        return $this->redirect(['index']);
    }

    /**
     * 角色权限分配
     *
     * Assign or remove items
     * @param string $id
     * @param string $action
     * @return array
     */
    public function actionAssign($id)
    {
        $post = Yii::$app->getRequest()->post();
        $action = $post['action'];
        $roles = $post['roles'];
        $manager = Yii::$app->getAuthManager();
        $parent = $this->type === Item::TYPE_ROLE ? $manager->getRole($id) : $manager->getPermission($id);

        $error = [];
        if ($action == 'assign') {
            foreach ($roles as $role) {
                $child = $manager->getPermission($role);
                if ($this->type === Item::TYPE_ROLE && $child === null) {
                    $child = $manager->getRole($role);
                }
                try {
                    $manager->addChild($parent, $child);
                } catch (\Exception $e) {
                    $error[] = $e->getMessage();
                }
            }
        } else {
            foreach ($roles as $role) {
                $child = $manager->getPermission($role);
                if ($this->type === Item::TYPE_ROLE && $child === null) {
                    $child = $manager->getRole($role);
                }
                try {
                    $manager->removeChild($parent, $child);
                } catch (\Exception $e) {
                    $error[] = $e->getMessage();
                }
            }
        }
        Helper::invalidate();
        Yii::$app->getResponse()->format = 'json';
        return $this->getItems($id);
    }

    /**
     * @param string $id
     * @return array
     */
    protected function getItems($id)
    {
        $manager = Yii::$app->getAuthManager();
        $avaliable = [];
        if ($this->type === Item::TYPE_ROLE) {
            foreach (array_keys($manager->getRoles()) as $name) {
                $avaliable[$name] = 'role';
            }
        }
        foreach (array_keys($manager->getPermissions()) as $name) {
            $avaliable[$name] = $name[0] == '/' ? 'route' : 'permission';
        }

        $assigned = [];
        foreach ($manager->getChildren($id) as $item) {
            $assigned[$item->name] = $item->type == 1 ? 'role' : ($item->name[0] == '/' ? 'route' : 'permission');
            unset($avaliable[$item->name]);
        }
        unset($avaliable[$id]);
        return[
            'avaliable' => $avaliable,
            'assigned' => $assigned
        ];
    }

    /**
     * Finds the AuthItem model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param  string        $id
     * @return AuthItem      the loaded model
     * @throws HttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        $item = $this->type === Item::TYPE_ROLE ? Yii::$app->getAuthManager()->getRole($id) :
            Yii::$app->getAuthManager()->getPermission($id);
        if ($item) {
            return new AuthItem($item);
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    // ----------------------------------------------------------------------------------------------------------------

    public function actionTest()
    {
        return $this->render('test');
    }






}
