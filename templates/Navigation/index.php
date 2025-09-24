<div class="flex h-screen bg-gray-100">
    <aside class="w-1/5 bg-white shadow-lg p-4 h-full overflow-y-auto">
        <h2 class="text-lg font-bold mb-4">Catégories</h2>
        <ul class="space-y-3">
            <?php foreach ($categories as $cat): ?>
                <li>
                    <?= $this->element('category_item_plus', ['category' => $cat, 'categoryId' => $categoryId]) ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </aside>

    <main class="flex-1 p-6 h-full overflow-y-auto">
        <h1 class="text-2xl font-bold mb-6">Produits</h1>

        <?php if (empty($products)): ?>
            <p class="text-gray-500">Aucun produit disponible.</p>
        <?php else: ?>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                <?php foreach ($products as $product): ?>
                    <?= $this->element('product_item', ['product' => $product]) ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>
</div>