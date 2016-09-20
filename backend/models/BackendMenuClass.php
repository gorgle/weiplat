<?php

namespace app\backend\models;

use Yii;
use yii\data\ActiveDataProvider;
use yii\caching\TagDependency;
use app;
use app\backend\components\ActiveRecordHelper;
use app\backend\components\Tree;
/**
 * This is the model class for table "backend_menu".
 *
 * @property integer $id
 * @property integer $parent_id
 * @property string $name
 * @property string $route
 * @property string $icon
 * @property integer $sort_order
 * @property string $added_by_ext
 * @property string $rbac_check
 * @property string $css_class
 * @property string $translation_category
 */
class BackendMenuClass extends \yii\db\ActiveRecord
{
    private static $identity_map = [];


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'backend_menu';
    }

    public function behaviors()
    {
        return [
            [
                'class' => ActiveRecordHelper::className(),
            ],
            [
                'class' => Tree::className(),
                'activeAttribute' => false,
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['parent_id', 'sort_order'], 'integer'],
            [['name', 'route'], 'required'],
            [['name', 'route', 'icon', 'added_by_ext', 'css_class'], 'string', 'max' => 255],
            [['rbac_check'], 'string', 'max' => 64],
            [['translation_category'], 'string', 'max' => 120],
        ];
    }

    /**
     * Scenarios
     * @return array
     */
    public function scenarios()
    {
        return [
            'default' => [
                'parent_id',
                'name',
                'route',
                'icon',
                'rbac_check',
                'added_by_ext',
                'css_class',
                'sort_order',
                'translation_category',
            ],
            'search' => [
                'id',
                'parent_id',
                'name',
                'route',
                'icon',
                'added_by_ext'
            ],
        ];
    }
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'parent_id' => 'Parent ID',
            'name' => 'Name',
            'route' => 'Route',
            'icon' => 'Icon',
            'sort_order' => 'Sort Order',
            'added_by_ext' => 'Added By Ext',
            'rbac_check' => 'Rbac Check',
            'css_class' => 'Css Class',
            'translation_category' => 'Translation Category',
        ];
    }

    /**
     * Search support for GridView and etc.
     * @param $params
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        /* @var $query \yii\db\ActiveQuery */
        $query = self::find()
            ->where(['parent_id'=>$this->parent_id]);
        $dataProvider = new ActiveDataProvider(
            [
                'query' => $query,
                'pagination' => [
                    'pageSize' => 10,
                ],
            ]
        );
        if (!($this->load($params))) {
            return $dataProvider;
        }
        $query->andFilterWhere(['id' => $this->id]);
        $query->andFilterWhere(['like', 'name', $this->name]);
        $query->andFilterWhere(['like', 'route', $this->route]);
        $query->andFilterWhere(['like', 'icon', $this->icon]);
        $query->andFilterWhere(['like', 'added_by_ext', $this->added_by_ext]);
        return $dataProvider;
    }

    /**
     * Returns model instance by ID(primary key) with cache support
     * @param  integer $id ID of record
     * @return BackendMenu BackendMenu instance
     */
    public static function findById($id)
    {
        if (!isset(static::$identity_map[$id])) {
            $cacheKey = static::tableName().":$id";
            if (false === $model = Yii::$app->cache->get($cacheKey)) {
                $model = static::find()->where(['id' => $id]);

                if (null !== $model = $model->one()) {
                    Yii::$app->cache->set(
                        $cacheKey,
                        $model,
                        86400,
                        new TagDependency([
                            'tags' => [
                                ActiveRecordHelper::getCommonTag(static::className())
                            ]
                        ])
                    );
                }
            }
            static::$identity_map[$id] = $model;
        }

        return static::$identity_map[$id];
    }

    /**
     * Returns all available to logged user BackendMenu items in yii\widgets\Menu acceptable format
     * @return BackendMenu[] Tree representation of items
     */
    public static function getAllMenu()
    {
        $rows = Yii::$app->cache->get("BackendMenu:all");
        if (false === is_array($rows)) {
            $rows = static::find()
                ->orderBy('parent_id ASC, sort_order ASC')
                ->asArray()
                ->all();
            Yii::$app->cache->set(
                "BackendMenu:all",
                $rows,
                86400,
                new TagDependency([
                    'tags' => [
                        ActiveRecordHelper::getCommonTag(static::className())
                    ]
                ])
            );
        }
        // rebuild rows to tree $all_menu_items
        $all_menu_items = Tree::rowsArrayToMenuTree($rows, 1, 1, false);
        return $all_menu_items;
    }
}
