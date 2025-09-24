<div class="flex h-screen bg-gray-100">
    <!-- Sidebar catégories -->
    <aside class="w-1/5 bg-white shadow-lg p-4">
        <h2 class="text-lg font-bold mb-4">Catégories</h2>
        <ul class="space-y-3">
            <?php foreach ($categories as $cat): ?>
                <li>
                    <a href="<?= $this->Url->build(['controller' => 'Navigation', 'action' => 'home', '?' => ['category_id' => $cat->id]]) ?>"
                       class="flex items-center gap-3 p-2 rounded-lg transition
                              <?= ($cat->id == $categoryId) ? 'bg-blue-500 text-white' : 'hover:bg-gray-200' ?>">
                        
                            <img src="<?= '/img/category/' . $category_path ?>" alt="<?= h($cat->name) ?>" class="w-10 h-10 rounded object-cover">
                        <span><?= h($cat->name) ?></span>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </aside>

    <!-- Zone produits -->
    <main class="flex-1 p-6 overflow-y-auto">
        <h1 class="text-2xl font-bold mb-6">
            Produits de la catégorie : 
            <span class="text-blue-600">
                <?= h($categories->firstMatch(['id' => $categoryId])->name ?? 'Produits') ?>
            </span>
        </h1>

        <?php if (empty($products)): ?>
            <p class="text-gray-500">Aucun produit disponible dans cette catégorie.</p>
        <?php else: ?>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                <?php foreach ($products as $product): ?>
                    <div class="bg-white rounded-xl shadow hover:shadow-lg transition p-4 flex flex-col">
                        <img src="<?= '/img/products/' . $product_path ?>" 
                             alt="<?= h($product->name) ?>" 
                             class="w-full h-32 object-cover rounded-lg mb-3">
                        <h3 class="text-lg font-semibold"><?= h($product->name) ?></h3>
                        <p class="text-gray-500 text-sm mb-2"><?= number_format($product->price, 2, ',', ' ') ?> €</p>
                        <button class="mt-auto bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition">
                            Ajouter
                        </button>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>
</div>
