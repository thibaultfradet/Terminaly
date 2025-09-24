<div class="bg-white rounded-xl shadow hover:shadow-lg transition p-4 flex flex-col items-center cursor-pointer product-card"
     data-id="<?= h($product->id) ?>"
     data-name="<?= h($product->name) ?>"
     data-price="<?= h($product->price) ?>"
     data-image="<?= '/img/products/' . 'eclair.jpg' ?>">
    
    <img src="<?= '/img/products/' . 'eclair.jpg'?>"
        alt="<?= h($product->name) ?>"
        class="w-full h-32 object-cover rounded-lg mb-3">
    
    <h3 class="text-lg font-semibold text-center"><?= h($product->name) ?></h3>
    
    <p class="bg-yellow-300 text-center font-bold text-black px-4 py-2 rounded mb-2">
        <?= number_format($product->price, 2, ',', ' ') ?> €
    </p>
</div>
