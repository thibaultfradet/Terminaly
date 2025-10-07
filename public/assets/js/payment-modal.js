// payment-modal.js
document.addEventListener("DOMContentLoaded", () => {
    const payModalElement = document.getElementById("payment-modal");
    if (!payModalElement) return;

    const payModal = new bootstrap.Modal(payModalElement);
    const checkoutButton = document.getElementById("checkout-button");
    const confirmPaymentButton = document.getElementById("confirm-payment");

    if (!checkoutButton) return;

    checkoutButton.addEventListener("click", () => {
        const cart = getCart(); // uses global function from cart.js
        if (Object.keys(cart).length === 0) {
            alert("Votre panier est vide.");
            return;
        }
        payModal.show();
    });

    if (confirmPaymentButton) {
        confirmPaymentButton.addEventListener("click", () => {
            clearCart(); // uses global function from cart.js
            payModal.hide();
            alert("Paiement réussi ! Merci pour votre achat 🥖");
        });
    }
});