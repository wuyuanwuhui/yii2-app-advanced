<?php

namespace backend\modules\sys\models;

use Yii;

/**
 * This is the model class for table "role_item_ids".
 *
 * @property int $id
 * @property string $role
 * @property string $menuids
 * @property string $itemids
 */
class RoleItemIds extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'role_item_ids';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['role'], 'required'],
            [['role'], 'string', 'max' => 64],
            [['menuids', 'itemids'], 'string', 'max' => 255],
            [['role'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'role' => 'Role',
            'menuids' => 'Menuids',
            'itemids' => 'Itemids',
        ];
    }
}
