// payment-modal.js
document.addEventListener("DOMContentLoaded", () => {
    const payModalElement = document.getElementById("checkout-modal");
    if (!payModalElement) return;

    const payModal = new bootstrap.Modal(payModalElement);
    const checkoutButton = document.getElementById("checkout-button");
    const confirmPaymentButton = document.getElementById("confirm-pay"); 

    if (!checkoutButton) return;

    checkoutButton.addEventListener("click", () => {
        const cart = getCart(); // global function from cart.js
        if (Object.keys(cart).length === 0) {
            alert("Votre panier est vide.");
            return;
        }
        payModal.show();
    });

    if (confirmPaymentButton) {
        confirmPaymentButton.addEventListener("click", () => {
            clearCart(); // global function from cart.js
            payModal.hide();
            alert("Paiement réussi ! Merci pour votre achat 🥖");
        });
    }
});