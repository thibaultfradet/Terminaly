// payment-modal.js
document.addEventListener("DOMContentLoaded", () => {
    const payModalElement = document.getElementById("checkout-modal");
    if (!payModalElement) return;

    const payModal = new bootstrap.Modal(payModalElement);
    const checkoutButton = document.getElementById("checkout-button");
    const confirmPaymentButton = document.getElementById("confirm-pay");
    const paymentCards = document.querySelectorAll(".payment-card");
    const paymentSelectionStep = document.querySelector(".payment-selection");
    const cashStep = document.querySelector(".cash-step");
    const confirmStep = document.querySelector(".confirm-step");
    const cashAmountInput = document.getElementById("cash-amount");
    const cashNextButton = document.getElementById("cash-next");
    const confirmMethodText = document.querySelector(".confirm-method");
    const confirmAmountText = document.querySelector(".confirm-amount");

    let selectedMethod = null;

    // Function to handle payment via AJAX
    function processPayment(paymentType) {
        const cart = getCart(); // get cart from localStorage
        if (!cart || Object.keys(cart).length === 0) return;

        const dataToSend = {
            paymentType: paymentType,
            cart: cart
        };

        $.ajax({
            url: "/ajax/sale",
            method: "POST",
            data: JSON.stringify(dataToSend),
            contentType: "application/json",
            success: function(response) {
                console.log("Payment processed successfully:", response);
            },
            error: function(xhr, status, error) {
                console.error("Error processing payment:", error);
            }
        });
    }

    // Open modal and reset steps
    checkoutButton.addEventListener("click", () => {
        const cart = getCart();
        if (!cart || Object.keys(cart).length === 0) {
            alert("Votre panier est vide.");
            return;
        }

        paymentSelectionStep.classList.remove("d-none");
        cashStep.classList.add("d-none");
        confirmStep.classList.add("d-none");
        cashAmountInput.value = "";
        selectedMethod = null;

        payModal.show();
    });

    // Payment method selection
    paymentCards.forEach(card => {
        card.addEventListener("click", () => {
            selectedMethod = card.dataset.method;

            if (!selectedMethod) return;

            if (selectedMethod === "cash") {
                paymentSelectionStep.classList.add("d-none");
                cashStep.classList.remove("d-none");
            } else {
                paymentSelectionStep.classList.add("d-none");
                confirmStep.classList.remove("d-none");

                confirmMethodText.textContent = `Méthode : ${card.querySelector(".fw-bold").textContent}`;
                confirmAmountText.textContent = `Montant : ${document.getElementById("cart-total").textContent}`;
            }
        });
    });

    // Cash next button
    cashNextButton.addEventListener("click", () => {
        const cashReceived = parseFloat(cashAmountInput.value.replace(",", "."));
        const totalAmount = parseFloat(document.getElementById("cart-total").textContent.replace("€", "").trim().replace(",", "."));

        if (isNaN(cashReceived) || cashReceived < totalAmount) {
            alert("Le montant reçu est insuffisant !");
            return;
        }

        cashStep.classList.add("d-none");
        confirmStep.classList.remove("d-none");

        confirmMethodText.textContent = `Méthode : Espèces`;
        confirmAmountText.textContent = `Montant reçu : ${cashReceived.toFixed(2)} € | Total : ${totalAmount.toFixed(2)} € | Rendu : ${(cashReceived - totalAmount).toFixed(2)} €`;
    });

    // Confirm payment
    confirmPaymentButton.addEventListener("click", () => {
        if (!selectedMethod) return;

        // Call the common function before clearing the cart
        processPayment(selectedMethod);

        clearCart(); // global function from cart.js
        payModal.hide();
        alert("Paiement réussi ! Merci pour votre achat 🥖");
    });
});