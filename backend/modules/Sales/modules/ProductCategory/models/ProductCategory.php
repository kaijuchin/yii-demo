<?php
namespace backend\modules\Sales\modules\ProductCategory\models;

use yii\db\ActiveRecord;


class ProductCategory extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%product_category}}';
    }
    
}