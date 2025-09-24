<?php
/**
 * @var \App\Model\Entity\Category $category
 * @var int|null $categoryId
 */
?>
<a href="<?= $this->Url->build(['controller' => 'Navigation', 'action' => 'index', $category->id]) ?>" class="flex flex-col items-center bg-white shadow rounded-xl p-4 hover:shadow-lg transition
          <?= ($category->id == $categoryId) ? 'ring-2 ring-blue-500' : '' ?>">
            
          <img src="<?= '/img/category/' . $category_path ?>"
            alt="<?= h($category->name) ?>" 
            class="w-20 h-20 object-cover rounded-lg mb-3">
    <span class="font-semibold"><?= h($category->name) ?></span>
</a>
