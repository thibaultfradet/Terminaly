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

<footer class="fixed bottom-0 left-0 right-0 bg-white shadow-xl p-4 flex justify-between items-center z-50">
    <div class="text-lg font-bold">
        Total : <span id="total-price">0,00</span> €
    </div>
    <button id="checkout-button" class="bg-green-500 text-white px-6 py-3 rounded-lg font-bold hover:bg-green-600 transition">
        Passer au paiement
    </button>
</footer>

<?= $this->element('modal_product') ?>
<?= $this->Html->script(['modal-product']) ?>


