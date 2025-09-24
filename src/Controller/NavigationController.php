<?php
declare(strict_types=1);

namespace App\Controller;

class NavigationController extends AppController
{
    public function index($categoryId = null)
    {
        $categorysTable = $this->fetchTable('Categorys');
        $productsTable   = $this->fetchTable('Products');

        // get all categorys
        $category = $categorysTable->find()->all();

        // get the default category => 
        $defaultCategoryName = "Pain";
        if (!$categoryId) {
            $defaultCategory = $categorysTable->find()
                ->where(['name' => $defaultCategoryName])
                ->first();

            $categoryId = $defaultCategory ? $defaultCategory->id : null;
        }

        // get the product of the active category
        $products = [];
        if ($categoryId) {
            $products = $productsTable->find()
                ->where(['category_id' => $categoryId])
                ->all();
        }


        $this->set('product_path', 'eclair.jpg');
        $this->set('category_path', 'pain.jpg');
        $this->set(compact('categorys', 'products', 'categoryId'));
    }
}
