<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Category> $categorys
 */
?>
<div class="max-w-5xl mx-auto p-6 bg-white rounded shadow">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-2xl font-semibold">Catégories</h3>
        <?= $this->Html->link('Nouvelle catégorie', ['action' => 'add'], ['class' => 'bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700']) ?>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full border border-gray-300 rounded">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2 text-left"><?= $this->Paginator->sort('id', 'ID') ?></th>
                    <th class="px-4 py-2 text-left"><?= $this->Paginator->sort('name', 'Nom') ?></th>
                    <th class="px-4 py-2 text-left"><?= $this->Paginator->sort('created_at', 'Date de création') ?></th>
                    <th class="px-4 py-2 text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categorys as $category): ?>
                <tr class="border-t">
                    <td class="px-4 py-2"><?= $this->Number->format($category->id) ?></td>
                    <td class="px-4 py-2"><?= h($category->name) ?></td>
                    <td class="px-4 py-2"><?= h($category->created_at) ?></td>
                    <td class="px-4 py-2 space-x-2">
                        <?= $this->Html->link('Voir', ['action' => 'view', $category->id], ['class' => 'text-blue-600 hover:underline']) ?>
                        <?= $this->Html->link('Modifier', ['action' => 'edit', $category->id], ['class' => 'text-yellow-600 hover:underline']) ?>
                        <?= $this->Form->postLink(
                            'Supprimer',
                            ['action' => 'delete', $category->id],
                            [
                                'method' => 'delete',
                                'confirm' => 'Êtes-vous sûr de vouloir supprimer la catégorie n° ' . $category->id . ' ?',
                                'class' => 'text-red-600 hover:underline'
                            ]
                        ) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="mt-6">
        <ul class="flex space-x-2">
            <?= $this->Paginator->first('<< premier') ?>
            <?= $this->Paginator->prev('< précédent') ?>
            <?= $this->Paginator->numbers() ?>
            <?= $this->Paginator->next('suivant >') ?>
            <?= $this->Paginator->last('dernier >>') ?>
        </ul>
        <p class="mt-2 text-sm text-gray-600">
            <?= $this->Paginator->counter('Page {{page}} sur {{pages}}, affichant {{current}} enregistrement(s) sur {{count}} au total') ?>
        </p>
    </div>
</div>
