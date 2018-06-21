<?php
namespace backend\modules\Sales\modules\ProductCategory\models;

use yii\db\ActiveRecord;
use yii\caching\TagDependency;
// use common\traits\AttrTrait;
use yii\helpers\ArrayHelper;

class ProductCategory extends ActiveRecord
{
    // use AttrTrait;

    private static $categorys = null;

    public static function tableName()
    {
        return '{{%product_category}}';
    }

    public static function getAllCategorys()
    {
        return self::find()->all();
    }

    /**
     * 获取所有分类Model
     * @return mixed|null
     * @throws \Exception
     */
    public static function getCategoryList()
    {
        if (is_null(self::$categorys)) {
            $dep = new TagDependency(['tags' => 'product-category']);
            self::$categorys = ProductCategory::getDb()->cache(function ($db) {
                $data = [];
                $categorys = self::getAllCategorys();
                foreach ($categorys as $category) {
                    $data[$category->id] = $category;
                }
                return $data;
            }, '', $dep);
        }
        return self::$categorys;
    }

    /**
     * fancyTree所需Array结构
     * @param $data
     * @param $key
     * @param $buffer
     * @return array
     */
    public static function generateTree($data, $key, &$buffer)
    {
        $categorys = ProductCategory::getCategoryList();
        $category = ArrayHelper::getValue($categorys, $key);
        $tree = array(
            'title' => $category ? $category->name : '',
            'key' => $category ? $category->id : 0,
            'children' => [],
        );
        if (isset($buffer[$key])) {
            return $tree;
        }
        if (!empty($data[$key])) {
            foreach ($data[$key] as $subkey) {
                $tree['children'][] = self::generateTree($data, $subkey, $buffer);
            }
        }
        return $tree;
    }

    /**
     * 获取分类展示树形结构数据
     * @return array
     */
    public static function getCategoryTree()
    {
        $buffer = $treeData = $treeDict = [];
        $categorys = ProductCategory::getCategoryList();
        $first = [];
        foreach ($categorys as $category) {
            if ($category->lvl == 0) {
                $first[] = $category->id;
            }
            $treeDict[$category->lvl][] = $category->id;
        }
        foreach ($first as $value) {
            $treeData[] = self::generateTree($treeDict, $value, $buffer);

        }
        return $treeData;
    }

    
}