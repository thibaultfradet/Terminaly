// payment-modal.js
$(document).ready(function () {
    const $payModal = $("#checkout-modal");
    const payModal = new bootstrap.Modal($payModal[0], { backdrop: true, keyboard: true });
    const $checkoutButton = $("#checkout-button");
    const $confirmPaymentButton = $("#confirm-pay");

    const $paymentSelectionStep = $(".payment-selection");
    const $cashStep = $(".cash-step");
    const $owingStep = $(".owing-step");
    const $confirmStep = $(".confirm-step");

    const $cashAmountInput = $("#cash-amount");
    const $cashNextButton = $("#cash-next");

    const $owingNextButton = $("#owing-next");
    const $owingClientNameInput = $("#owing-client-name");

    const $confirmMethodText = $(".confirm-method");
    const $confirmAmountText = $(".confirm-amount");
    const $modalCartTotal = $("#modal-cart-total");

    let selectedMethod = null;
    let owingClientName = "";

    const paymentTypeLabels = { card: "Carte", cash: "Espèce", check: "Chèque", owing: "Dû" };

    function processPayment(paymentType, extraData = {}) {
        const cart = getCart();
        if (!cart || Object.keys(cart).length === 0) return;

        if (paymentType === "owing") {
            extraData.clientName = owingClientName;
            extraData.owingCompleted = false;
        }

        const dataToSend = { paymentType: paymentType, cart: cart, ...extraData };

        $.ajax({
            url: "/ajax/sale",
            method: "POST",
            data: JSON.stringify(dataToSend),
            contentType: "application/json",
            success: function (response) {},
            error: function (xhr, status, error) {
                console.error("Error processing payment:", error);
            }
        });
    }

    // Open modal
    $checkoutButton.on("click", function () {
        const cart = getCart(); // récupère le panier actuel
        if (!cart || Object.keys(cart).length === 0) {
            alert("Votre panier est vide.");
            return;
        }

        // Compute cart total only if there are items
        let total = 0;
        Object.values(cart).forEach(item => {
            const price = parseFloat(item.price) || 0;
            const quantity = parseFloat(item.quantity) || 0;
            total += price * quantity;
        });

        // Update modal cart total
        $("#modal-cart-total").text(total.toFixed(2) + " €");

        $paymentSelectionStep.removeClass("d-none");
        $cashStep.addClass("d-none");
        $owingStep.addClass("d-none");
        $confirmStep.addClass("d-none");

        $cashAmountInput.val("");
        $owingClientNameInput.val("");
        selectedMethod = null;

        payModal.show();
    });

    // Payment selection
    $(".payment-card").on("click", function () {
        selectedMethod = $(this).data("method");

        $paymentSelectionStep.addClass("d-none");
        $cashStep.addClass("d-none");
        $owingStep.addClass("d-none");
        $confirmStep.addClass("d-none");

        if (selectedMethod === "cash") {
            $cashStep.removeClass("d-none");
        } else if (selectedMethod === "owing") {
            $owingStep.removeClass("d-none");
        } else {
            $confirmStep.removeClass("d-none");
            $confirmMethodText.text(`Méthode : ${paymentTypeLabels[selectedMethod]}`);
            $confirmAmountText.text(`Montant : ${$modalCartTotal.text()}`);
        }
    });

    // Cash next
    $cashNextButton.on("click", function () {
        const cashReceived = parseFloat($cashAmountInput.val().replace(",", "."));
        const totalAmount = parseFloat($modalCartTotal.text().replace("€", "").trim().replace(",", "."));

        if (isNaN(cashReceived) || cashReceived < totalAmount) {
            alert("Le montant reçu est insuffisant !");
            return;
        }

        $cashStep.addClass("d-none");
        $confirmStep.removeClass("d-none");

        $confirmMethodText.text(`Méthode : ${paymentTypeLabels["cash"]}`);
        $confirmAmountText.text(
            `Montant reçu : ${cashReceived.toFixed(2)} € | Total : ${totalAmount.toFixed(2)} € | Rendu : ${(cashReceived - totalAmount).toFixed(2)} €`
        );
    });

    // Owing next
    $owingNextButton.on("click", function () {
        owingClientName = $owingClientNameInput.val().trim();

        if (!owingClientName) {
            alert("Veuillez entrer le nom du client.");
            return;
        }

        $owingStep.addClass("d-none");
        $confirmStep.removeClass("d-none");

        $confirmMethodText.text(`Méthode : ${paymentTypeLabels["owing"]}`);
        $confirmAmountText.text(`Client : ${owingClientName} | Montant total : ${$modalCartTotal.text()}`);
    });

    // Confirm payment
    $confirmPaymentButton.on("click", function () {
        if (!selectedMethod) return;

        processPayment(selectedMethod);

        clearCart();
        payModal.hide();

        if (selectedMethod === "owing") {
            alert(`Vente enregistrée comme dû pour ${owingClientName}.`);
        } else {
            alert("Paiement réussi ! Merci pour votre achat 🥖");
        }
    });
});