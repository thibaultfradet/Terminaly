<?php
declare(strict_types=1);

namespace App\Controller;

class NavigationController extends AppController
{
    public function index($categoryId = null)
    {
        $categoriesTable = $this->fetchTable('Categories');
        $productsTable   = $this->fetchTable('Products');

        // get all categories
        $categories = $categoriesTable->find()->all();

        // get the default category => 
        $defaultCategoryName = "Pain";
        if (!$categoryId) {
            $defaultCategory = $categoriesTable->find()
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
        $this->set(compact('categories', 'products', 'categoryId'));
    }
}
