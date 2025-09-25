<div class="flex h-screen bg-gray-100">
    <!-- Sidebar categorys -->
    <aside class="w-1/6 bg-white shadow-lg p-4 h-full overflow-y-auto">
        <h2 class="text-lg font-bold mb-4">Catégories</h2>
        
        <ul class="space-y-3">
        <!-- all product page option -->
            <li>
               <a href="<?= $this->Url->build(['controller' => 'Navigation', 'action' => 'index', 'all']) ?>" 
                    class="block px-3 py-2 rounded-lg font-semibold text-blue-600 hover:bg-blue-100 transition">
                        🛒 Tous les produits
                </a>

            </li>

            <!-- Separator -->
            <li>
                <hr class="my-2 border-gray-300">
            </li>

            <!-- Other categories -->
            <?php foreach ($categorys as $cat): ?>
                <li>
                    <?= $this->element('category_item_plus', ['category' => $cat, 'categoryId' => $categoryId]) ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </aside>


    <!-- products -->
    <main class="flex-1 p-6 h-full overflow-y-auto">
        <h1 class="text-2xl font-bold mb-6">Produits</h1>
        <?php if (!empty($isAllProducts) && $isAllProducts): ?>    
            <div class="mb-4">
                <input type="text" id="search-products" placeholder="Rechercher un produit..."
                    class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>
        <?php endif; ?>

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


    <!-- cart -->
    <aside class="w-1/5  bg-white shadow-xl p-4 h-full flex flex-col">
        <h2 class="text-xl font-bold mb-4 border-b pb-2">Mon Panier</h2>
        
        <!-- list of product in the card -->
        <div id="cart-items" class="flex-1 overflow-y-auto space-y-4">
          
        </div>

        <!-- total + proceed to payment -->
        <div class="border-t pt-4">
            <div class="flex justify-between mb-4">
                <span class="font-bold text-lg">Total :</span>
                <span id="cart-total" class="font-bold text-lg">0,00 €</span>
            </div>
            <button id="checkout-button" class="w-full bg-green-500 text-white py-3 rounded-lg font-bold hover:bg-green-600 transition">
                Passer au paiement
            </button>
        </div>
    </aside>
</div>

<!-- Modal produit -->
<?= $this->element('modal_product') ?>
<?= $this->Html->script(['modal-product']) ?>

<?php if (!empty($isAllProducts) && $isAllProducts): ?>
    <script>
        const allProducts = <?= json_encode($products) ?>;
    </script>
    <?= $this->Html->script('all_products') ?>
<?php endif; ?>
