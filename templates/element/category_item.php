<?php
/**
 * Variables attendues :
 * @var \App\Model\Entity\Category $cat
 * @var int|null $categoryId
 */
?>
<a href="<?= $this->Url->build(['controller' => 'Navigation', 'action' => 'home', '?' => ['category_id' => $cat->id]]) ?>"
   class="flex items-center gap-3 p-2 rounded-lg transition
          <?= ($cat->id == $categoryId) ? 'bg-blue-500 text-white' : 'hover:bg-gray-200' ?>">
    
        <img src="<?= '/img/category/' . $category_path ?>" alt="<?= h($cat->name) ?>" class="w-10 h-10 rounded object-cover">
    <span><?= h($cat->name) ?></span>
</a>
