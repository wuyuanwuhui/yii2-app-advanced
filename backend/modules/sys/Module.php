<?php
namespace backend\modules\sys;

use Yii;
use yii\helpers\Inflector;

/**
 * 系统管理
 *
 * GUI manager for RBAC.
 * 
 * Use [[\yii\base\Module::$controllerMap]] to change property of controller. 
 * To change listed menu, use property [[$menus]].
 * 
 * ~~~
 * 'layout' => 'left-menu', // default to null mean use application layout.
 * 'controllerMap' => [
 *     'assignment' => [
 *         'class' => 'backend\modules\sys\controllers\SysuserController',
 *         'userClassName' => 'app\models\User',
 *         'idField' => 'id'
 *     ]
 * ],
 * 'menus' => [
 *     'assignment' => [
 *         'label' => 'Grand Access' // change label
 *     ],
 *     'route' => null, // disable menu
 * ],
 * ~~~
 * 
 * @property string $mainLayout Main layout using for module. Default to layout of parent module.
 * Its used when `layout` set to 'left-menu', 'right-menu' or 'top-menu'.
 * @property array $menus List avalible menu of module.
 * It generated by module items .
 * 
 * @author Misbahul D Munir <misbahuldmunir@gmail.com>
 * @since 1.0
 */
class Module extends \yii\base\Module
{
    /**
     * @inheritdoc
     */
    public $defaultRoute = 'assignment';

    /**
     * @var array 
     * @see [[items]]
     */
    private $_menus = [];

    /**
     * @var array 
     * @see [[items]]
     */
    private $_coreItems = [
        'assignment' => 'Assignments',
        'role' => 'Roles',
        'permission' => 'Permissions',
        'route' => 'Routes',
        'rule' => 'Rules',
        'menu' => 'Menus',
    ];

    /**
     * @var array 
     * @see [[items]]
     */
    private $_normalizeMenus;

    /**
     * Nav bar items
     * @var array  
     */
    public $navbar;

    /**
     * @var string Main layout using for module. Default to layout of parent module.
     * Its used when `layout` set to 'left-menu', 'right-menu' or 'top-menu'.
     */
    public $mainLayout = '@backend/modules/sys/views/layouts/main.php';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        if (!isset(Yii::$app->i18n->translations['rbac-admin'])) {
            Yii::$app->i18n->translations['rbac-admin'] = [
                'class' => 'yii\i18n\PhpMessageSource',
                'sourceLanguage' => 'en',
                'basePath' => '@izyue/admin/messages'
            ];
        }
        //user did not define the Navbar?
        if ($this->navbar === null) {
            $this->navbar = [
                ['label' => Yii::t('rbac-admin', 'Help'), 'url' => 'https://github.com/liuilpeng/yii2-admin/blob/master/docs/guide/basic-usage.md'],
                [
                    'label' => Yii::t('rbac-admin', 'Application'),
                    'url' => Yii::$app->homeUrl ?? ''
                ]
            ];
        }
        if (class_exists('yii\jui\JuiAsset')) {
            Yii::$container->set('backend\modules\sys\AutocompleteAsset', 'yii\jui\JuiAsset');
        }
    }

    /**
     * Get avalible menu.
     * @return array
     */
    public function getMenus()
    {
        if ($this->_normalizeMenus === null) {
            $mid = '/' . $this->getUniqueId() . '/';
            // resolve core menus
            $this->_normalizeMenus = [];
            $config = components\Configs::instance();
            foreach ($this->_coreItems as $id => $lable) {
                if ($id !== 'menu' || ($config->db !== null && $config->db->schema->getTableSchema($config->menuTable) !== null)) {
                    $this->_normalizeMenus[$id] = ['label' => Yii::t('rbac-admin', $lable), 'url' => [$mid . $id]];
                }
            }
            foreach (array_keys($this->controllerMap) as $id) {
                $this->_normalizeMenus[$id] = ['label' => Yii::t('rbac-admin', Inflector::humanize($id)), 'url' => [$mid . $id]];
            }

            // user configure menus
            foreach ($this->_menus as $id => $value) {
                if (empty($value)) {
                    unset($this->_normalizeMenus[$id]);
                } else {
                    if (is_string($value)) {
                        $value = [
                            'label' => $value,
                        ];
                    }
                    $this->_normalizeMenus[$id] = isset($this->_normalizeMenus[$id]) ? array_merge($this->_normalizeMenus[$id], $value)
                            : $value;
                    if (!isset($this->_normalizeMenus[$id]['url'])) {
                        $this->_normalizeMenus[$id]['url'] = [$mid . $id];
                    }
                }
            }
        }
        return $this->_normalizeMenus;
    }

    /**
     * Set or add avalible menu.
     * @param array $menus
     */
    public function setMenus($menus)
    {
        $this->_menus = array_merge($this->_menus, $menus);
        $this->_normalizeMenus = null;
    }

    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        if (parent::beforeAction($action)) {
            /* @var $action \yii\base\Action */
            $action->controller->getView()->params['breadcrumbs'][] = [
                'label' => Yii::t('rbac-admin', 'Admin'),
                'url' => ['/' . $this->uniqueId]
            ];
            return true;
        }
        return false;
    }
}
