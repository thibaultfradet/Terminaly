<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Product $product
 * @var \Cake\Collection\CollectionInterface|string[] $categorys
 */
?><div class="row flex flex-col md:flex-row w-full max-w-4xl mx-auto bg-white rounded-xl shadow-lg overflow-hidden">
    <div class="column column-80 w-full md:w-3/4 p-6 md:p-10">
        <div class="products form content">
            <form class="space-y-6">
                <fieldset>
                    <legend class="text-2xl font-bold text-gray-900 mb-6">Ajouter un produit</legend>
                    <div class="mb-4">
                        <label for="name" class="block text-sm font-medium text-gray-700">Nom</label>
                        <input type="text" id="name" name="name" class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="Nom">
                    </div>
                    <div class="mb-4">
                        <label for="category_id" class="block text-sm font-medium text-gray-700">Catégorie</label>
                        <select id="category_id" name="category_id" class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <option value="">-- Sélectionnez une catégorie --</option>
                            <!-- Les options de catégorie seraient générées ici -->
                        </select>
                    </div>
                    <div class="mb-4">
                        <label for="price" class="block text-sm font-medium text-gray-700">Prix</label>
                        <input type="number" id="price" name="price" class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="Prix">
                    </div>
                    <div class="mb-4">
                        <label for="created_at" class="block text-sm font-medium text-gray-700">Créé le</label>
                        <input type="date" id="created_at" name="created_at" class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>
                </fieldset>
                <div>
                    <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                        Soumettre
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
