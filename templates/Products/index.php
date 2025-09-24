<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Product> $products
 */
?>

<div class="products index content w-full max-w-7xl mx-auto p-6 md:p-10">
    <div class="flex flex-col sm:flex-row justify-between items-center mb-6">
        <h3 class="text-2xl font-bold text-gray-900">Produits</h3>
        <?= $this->Html->link(__('Nouveau Produit'), ['action' => 'add'], ['class' => 'button bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg shadow transition-colors duration-200 mt-4 sm:mt-0']) ?>
    </div>

    <div class="table-responsive bg-white rounded-xl shadow-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"><?= $this->Paginator->sort('id') ?></th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"><?= $this->Paginator->sort('name') ?></th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"><?= $this->Paginator->sort('category_id') ?></th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"><?= $this->Paginator->sort('price') ?></th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"><?= $this->Paginator->sort('created_at') ?></th>
                    <th class="actions px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php foreach ($products as $product): ?>
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= $this->Number->format($product->id) ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= h($product->name) ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        <?= $product->hasValue('category') ? $this->Html->link($product->category->name, ['controller' => 'Categorys', 'action' => 'view', $product->category->id], ['class' => 'text-blue-600 hover:text-blue-900']) : '' ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= $this->Number->format($product->price) ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= h($product->created_at) ?></td>
                    <td class="actions px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                        <?= $this->Html->link(__('Voir'), ['action' => 'view', $product->id], ['class' => 'text-indigo-600 hover:text-indigo-900']) ?>
                        <?= $this->Html->link(__('Modifier'), ['action' => 'edit', $product->id], ['class' => 'text-green-600 hover:text-green-900']) ?>
                        <?= $this->Form->postLink(
                            __('Supprimer'),
                            ['action' => 'delete', $product->id],
                            [
                                'method' => 'delete',
                                'confirm' => __('Êtes-vous sûr de vouloir supprimer # {0}?', $product->id),
                                'class' => 'text-red-600 hover:text-red-900'
                            ]
                        ) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <div class="paginator flex flex-col sm:flex-row justify-between items-center text-sm text-gray-600 mt-6">
        <ul class="pagination flex items-center space-x-2">
            <li class="rounded-lg border border-gray-300 bg-white hover:bg-gray-100 p-2 text-gray-500">
                <?= $this->Paginator->first('<< ' . __('premier')) ?>
            </li>
            <li class="rounded-lg border border-gray-300 bg-white hover:bg-gray-100 p-2 text-gray-500">
                <?= $this->Paginator->prev('< ' . __('précédent')) ?>
            </li>
            <li class="rounded-lg border border-gray-300 bg-white p-2 text-gray-900 font-bold shadow">
                <?= $this->Paginator->numbers() ?>
            </li>
            <li class="rounded-lg border border-gray-300 bg-white hover:bg-gray-100 p-2 text-gray-500">
                <?= $this->Paginator->next(__('suivant') . ' >') ?>
            </li>
            <li class="rounded-lg border border-gray-300 bg-white hover:bg-gray-100 p-2 text-gray-500">
                <?= $this->Paginator->last(__('dernier') . ' >>') ?>
            </li>
        </ul>
        <p class="mt-4 sm:mt-0"><?= $this->Paginator->counter(__('Page {{page}} sur {{pages}}, montrant {{current}} enregistrement(s) sur {{count}} au total')) ?></p>
    </div>
</div>
