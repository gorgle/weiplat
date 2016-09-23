<?php

namespace app\modules\models;

use app;
use yii\db\ActiveRecord;

class UserService extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_service}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [['user_id'], 'integer'],
            [['service_type', 'service_id'], 'string', 'max' => 255],
            [
                ['service_type', 'service_id'],
                'unique',
                'targetAttribute' => ['service_type', 'service_id'],
                'message' => 'The combination of Service Type and Service ID has already been taken.'
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return [
            'default' => ['user_id', 'service_type', 'service_id'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'service_type' => 'Service Type',
            'service_id' => 'Service ID',
        ];
    }

    /**
     * Relation to User model
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }


    /**
     * Find user service_id through user_id
     * @param $uid
     * @return array|null|ActiveRecord
     */
    public static function getServicerByUid($uid)
    {
        return self::find()
            ->where([
                'user_id' => $uid,
                'service_type' => \app\modules\user\authclients\MpWeixin::className(),
            ])
            ->one();
    }
}