<?php

namespace backend\modules\Sales\modules\ProductCategory\controllers;

use Yii;
use yii\web\Controller;
use yii\data\ActiveDataProvider;
use backend\modules\Sales\modules\ProductCategory\models\ProductCategory;

/**
 * Default controller for the `product-category` module
 */
class DefaultController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {   
        $dataProvider = new ActiveDataProvider([
            'query' => ProductCategory::find(),
        ]);
        $data = (ProductCategory::getCategoryTree());
        return $this->render('index', [
            'data' => $data,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView()
    {
        $lists = ProductCategory::find()->orderBy('id')->asArray()->all();
        $parent = [];
        $arr_list = [];
        foreach ($lists as $item) {
            if (count($parent)) {
                while (count($parent) - 1 > 0 && $parent[count($parent) - 1]['rgt'] < $item['rgt']) {
                    array_pop($parent);
                }
            }

            $item['depath'] = count($parent);
            $parent[] = $item;
            $arr_list[] = $item;
        }

        return $this->render('view', ['data' => $arr_list]);
    }

    public function actionAdd($pid = 0)
    {
        if (!$pid) {
            return 'fail';
        }
        $parent_category = ProductCategory::findOne($pid);
        Yii::$app->db->createCommand()->update('{{%product_category}}', ['lft' => 'lft + 2'], 'lft > '. $parent_category["lft"])->execute();
        Yii::$app->db->createCommand()->update('{{%product_category}}', ['rgt' => 'rgt + 2'], 'lft > '. $parent_category["lft"])->execute();
        Yii::$app->db->createCommand()->insert('{{%product_category}}', ['lft' => $parent_category['lft'] + 1, 'rgt' => $parent_category['rgt'] + 2])->execute();

        return 'success';
    }

    public function actionDelete($id = 0)
    {   
        if (!$id) 
            return 'fail';

        if ($category = ProductCategory::findOne($id)) {
            $width = $category['rgt'] - $category['lft'] + 1;
            Yii::$app->db->createCommand()->delete('{{%product_category}}', 'id = '.$id)->execute();
            Yii::$app->db->createCommand('DELETE FROM {{%product_category}} WHERE lft > '.$category['lft'].' and lft < '.$category['rgt'])->execute();
            Yii::$app->db->createCommand('UPDATE {{%product_category}} SET lft = lft - '. $width . ' WHERE lft > '.$category["rgt"])->execute();
            Yii::$app->db->createCommand('UPDATE {{%product_category}} SET rgt = rgt - '. $width . ' WHERE rgt > '.$category["rgt"])->execute();
        };
        return 'success';
    }

    public function actionEdit($id = 0)
    {

    }
}
