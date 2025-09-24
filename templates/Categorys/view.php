<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Category $category
 */
?>
<div class="max-w-5xl mx-auto p-6 bg-white rounded shadow">
    <h3 class="text-2xl font-semibold mb-4"><?= h($category->name) ?></h3>
    <table class="min-w-full border border-gray-300 rounded mb-6">
        <tr class="bg-gray-100">
            <th class="px-4 py-2 text-left">Nom</th>
            <td class="px-4 py-2"><?= h($category->name) ?></td>
        </tr>
        <tr>
            <th class="px-4 py-2 text-left">ID</th>
            <td class="px-4 py-2"><?= $this->Number->format($category->id) ?></td>
        </tr>
        <tr class="bg-gray-100">
            <th class="px-4 py-2 text-left">Créé le</th>
            <td class="px-4 py-2"><?= h($category->created_at) ?></td>
        </tr>
    </table>

    <div class="flex gap-4 mb-8">
        <?= $this->Html->link('Modifier la catégorie', ['action' => 'edit', $category->id], ['class' => 'bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600']) ?>
        <?= $this->Form->postLink(
            'Supprimer la catégorie',
            ['action' => 'delete', $category->id],
            [
                'confirm' => 'Êtes-vous sûr de vouloir supprimer la catégorie n° ' . $category->id . ' ?',
                'class' => 'bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700'
            ]
        ) ?>
        <?= $this->Html->link('Liste des catégories', ['action' => 'index'], ['class' => 'text-blue-600 hover:underline']) ?>
        <?= $this->Html->link('Nouvelle catégorie', ['action' => 'add'], ['class' => 'text-blue-600 hover:underline']) ?>
    </div>
</div>
