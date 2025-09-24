<?= $this->Form->create(null) ?>
<?= $this->Form->end() ?>
<div id="product-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-xl p-6 w-96 max-w-full mx-4 relative">
        <button id="modal-close" class="absolute top-3 right-3 text-gray-500 hover:text-gray-800">&times;</button>
        
        <h2 id="modal-name" class="text-xl font-bold mb-2"></h2>
        <p id="modal-price" class="text-lg font-semibold mb-4"></p>

        <div class="mb-4">
            <label for="modal-quantity" class="block mb-1 font-semibold">Quantité :</label>
            <input type="number" id="modal-quantity" min="0" step="0.1" value="0" class="w-full border rounded px-3 py-2">
        </div>

        <div class="flex justify-between space-x-2 mb-4">
            <button class="quantity-preset-btn w-full bg-gray-200 text-gray-700 px-4 py-2 rounded hover:bg-gray-300 transition font-bold" data-quantity="0.5">0.5</button>
            <button class="quantity-preset-btn w-full bg-gray-200 text-gray-700 px-4 py-2 rounded hover:bg-gray-300 transition font-bold" data-quantity="1">1</button>
            <button class="quantity-preset-btn w-full bg-gray-200 text-gray-700 px-4 py-2 rounded hover:bg-gray-300 transition font-bold" data-quantity="2">2</button>
            <button class="quantity-preset-btn w-full bg-gray-200 text-gray-700 px-4 py-2 rounded hover:bg-gray-300 transition font-bold" data-quantity="3">3</button>
        </div>

        <button id="modal-add" class="w-full bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition font-bold">
            Ajouter
        </button>
    </div>
</div>