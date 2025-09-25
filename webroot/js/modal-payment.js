$(function () {
    const $checkoutBtn = $("#checkout-button");
    const $checkoutModal = $("#checkout-modal");
    const $closeBtn = $("#checkout-close");

    let selectedMethod = null;
    let cashAmount = null;

    // Fonction de reset complet de la modal
    function resetModal() {
        $checkoutModal
            .find(".payment-selection, .cash-step, .confirm-step")
            .addClass("hidden");
        $checkoutModal.find(".payment-selection").removeClass("hidden");
        $checkoutModal.find("h2").text("Choisissez votre moyen de paiement");
        selectedMethod = null;
        cashAmount = null;
        $checkoutModal.find("#cash-amount").val("");
        $checkoutModal.find(".confirm-method, .confirm-amount").text("");
    }

    // Ouvrir la modal
    $checkoutBtn.on("click", function () {
        resetModal();
        $checkoutModal.removeClass("hidden");
    });

    // Fermer la modal
    $closeBtn.on("click", function () {
        $checkoutModal.addClass("hidden");
        resetModal();
    });

    // Clic sur une carte de paiement
    $checkoutModal.on("click", ".payment-card", function () {
        selectedMethod = $(this).data("method");

        if (selectedMethod === "cash") {
            // Étape cash
            $checkoutModal.find(".payment-selection").addClass("hidden");
            $checkoutModal.find(".cash-step").removeClass("hidden");
            $checkoutModal.find("h2").text("Paiement en espèces");
        } else {
            showConfirmation();
        }
    });

    // Valider montant espèces
    $checkoutModal.on("click", "#cash-next", function () {
        const val = parseFloat($("#cash-amount").val());
        if (isNaN(val) || val <= 0) {
            alert("Veuillez entrer un montant valide");
            return;
        }
        cashAmount = val;
        showConfirmation();
    });

    // Confirmer paiement
    $checkoutModal.on("click", "#confirm-pay", function () {
        console.log("Paiement confirmé :", selectedMethod, cashAmount ?? "");
        $checkoutModal.addClass("hidden");
        resetModal();
    });

    // Fonction pour afficher la confirmation
    function showConfirmation() {
        $checkoutModal
            .find(".payment-selection, .cash-step")
            .addClass("hidden");
        $checkoutModal.find(".confirm-step").removeClass("hidden");
        $checkoutModal.find("h2").text("Confirmation du paiement");

        $checkoutModal
            .find(".confirm-method")
            .text(
                `Méthode : ${
                    selectedMethod === "cash"
                        ? "Espèces"
                        : selectedMethod === "card"
                        ? "Carte bancaire"
                        : "Chèque"
                }`
            );
        $checkoutModal
            .find(".confirm-amount")
            .text(
                cashAmount ? `Montant reçu : ${cashAmount.toFixed(2)} €` : ""
            );
    }
});
