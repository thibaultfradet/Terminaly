<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Product $product
 */
?>
<div class="column column-80 w-full max-w-4xl mx-auto p-6 md:p-10 bg-white rounded-xl shadow-lg">
    <div class="products view content">
        <div class="flex flex-col sm:flex-row justify-between items-center mb-6 border-b border-gray-200 pb-4">
            <h3 class="text-3xl font-bold text-gray-900"><?= h($product->name) ?></h3>
            <div class="flex items-center space-x-2 mt-4 sm:mt-0">
                <?= $this->Html->link(__('Modifier'), ['action' => 'edit', $product->id], ['class' => 'inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-200']) ?>
                <?= $this->Form->postLink(__('Supprimer'), ['action' => 'delete', $product->id], ['confirm' => __('Êtes-vous sûr de vouloir supprimer # {0}?', $product->id), 'class' => 'inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-200']) ?>
                <?= $this->Html->link(__('Liste des Produits'), ['action' => 'index'], ['class' => 'inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-gray-700 bg-gray-200 hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors duration-200']) ?>
            </div>
        </div>
        
        <table class="w-full text-gray-700">
            <tbody class="divide-y divide-gray-200">
                <tr>
                    <th class="py-3 px-4 text-left font-bold w-1/4">Nom</th>
                    <td class="py-3 px-4"><?= h($product->name) ?></td>
                </tr>
                <tr>
                    <th class="py-3 px-4 text-left font-bold w-1/4">Catégorie</th>
                    <td class="py-3 px-4"><?= $product->hasValue('category') ? $this->Html->link($product->category->name, ['controller' => 'Categorys', 'action' => 'view', $product->category->id], ['class' => 'text-blue-600 hover:underline']) : '' ?></td>
                </tr>
                <tr>
                    <th class="py-3 px-4 text-left font-bold w-1/4">Id</th>
                    <td class="py-3 px-4"><?= $this->Number->format($product->id) ?></td>
                </tr>
                <tr>
                    <th class="py-3 px-4 text-left font-bold w-1/4">Prix</th>
                    <td class="py-3 px-4"><?= $this->Number->format($product->price) ?></td>
                </tr>
                <tr>
                    <th class="py-3 px-4 text-left font-bold w-1/4">Créé le</th>
                    <td class="py-3 px-4"><?= h($product->created_at) ?></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
