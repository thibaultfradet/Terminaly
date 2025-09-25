<div id="checkout-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-xl p-6 w-96 max-w-full mx-4 relative flex flex-col">
        <!-- Bouton fermeture -->
        <button id="checkout-close" class="absolute top-3 right-3 text-gray-500 hover:text-gray-800">&times;</button>

        <!-- Titre -->
        <h2 class="text-xl font-bold mb-4">Choisissez votre moyen de paiement</h2>

        <!-- Étape 1 : choix du paiement -->
        <div class="grid grid-cols-1 gap-4 payment-selection">
            <div class="payment-card border border-gray-200 rounded-xl p-6 cursor-pointer hover:shadow-lg transition-shadow flex flex-col items-center text-center" data-method="cash">
                <div class="w-16 h-16 bg-gray-200 rounded-full flex items-center justify-center mb-4">
                    <span class="text-2xl">💵</span>
                </div>
                <div class="font-bold text-lg mb-1">Espèces</div>
                <div class="text-gray-500 text-sm">Paiement en espèce.</div>
            </div>

            <div class="payment-card border border-gray-200 rounded-xl p-6 cursor-pointer hover:shadow-lg transition-shadow flex flex-col items-center text-center" data-method="card">
                <div class="w-16 h-16 bg-gray-200 rounded-full flex items-center justify-center mb-4">
                    <span class="text-2xl">💳</span>
                </div>
                <div class="font-bold text-lg mb-1">Carte bancaire</div>
                <div class="text-gray-500 text-sm">Paiement en carte bancaire.</div>
            </div>

            <div class="payment-card border border-gray-200 rounded-xl p-6 cursor-pointer hover:shadow-lg transition-shadow flex flex-col items-center text-center" data-method="check">
                <div class="w-16 h-16 bg-gray-200 rounded-full flex items-center justify-center mb-4">
                    <span class="text-2xl">🧾</span>
                </div>
                <div class="font-bold text-lg mb-1">Chèque</div>
                <div class="text-gray-500 text-sm">Paiement en chèque.</div>
            </div>
        </div>

        <!-- Étape 2 : saisie espèces -->
        <div class="cash-step hidden flex flex-col items-center mt-4">
            <h3 class="text-lg font-bold mb-2">Montant reçu :</h3>
            <input id="cash-amount" type="number" min="0" step="0.01" class="border rounded px-3 py-2 mb-4 w-full text-center" placeholder="0.00 €">
            <button id="cash-next" class="w-full bg-blue-500 text-white py-2 rounded hover:bg-blue-600 font-bold">Valider</button>
        </div>

        <!-- Étape 3 : confirmation -->
        <div class="confirm-step hidden flex flex-col items-center mt-4">
            <h3 class="text-lg font-bold mb-2">Confirmez le paiement</h3>
            <p class="confirm-method mb-2"></p>
            <p class="confirm-amount mb-4"></p>
            <button id="confirm-pay" class="w-full bg-green-500 text-white py-2 rounded hover:bg-green-600 font-bold">Confirmer</button>
        </div>
    </div>
</div>
