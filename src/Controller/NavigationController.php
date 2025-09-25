<?php
declare(strict_types=1);

namespace App\Controller;

class NavigationController extends AppController
{
    public function index($categoryId = null)
    {
        $categorysTable = $this->fetchTable('Categorys');
        $productsTable  = $this->fetchTable('Products');

        $categorys = $categorysTable->find()->all();

        $products = [];

         if ($categoryId === "all") {
            // all products
            $products = $productsTable
            ->find()
            ->contain(['Categorys'])
            ->all();
            $this->set('isAllProducts', true); // boolean for the view
        }  elseif ($categoryId) {
            // specific category
            $products = $productsTable->find()
                ->where(['category_id' => $categoryId])
                ->all();
        } else {
            // default category fallback
            $defaultCategoryName = "Pain";
            $defaultCategory = $categorysTable->find()
                ->where(['name' => $defaultCategoryName])
                ->first();

            $categoryId = $defaultCategory ? $defaultCategory->id : null;

            if ($categoryId) {
                $products = $productsTable->find()
                    ->where(['category_id' => $categoryId])
                    ->all();
            }
        }

        $this->set('product_path', 'eclair.jpg');
        $this->set('category_path', 'pain.jpg');
        $this->set(compact('categorys', 'products', 'categoryId'));
    }

}
