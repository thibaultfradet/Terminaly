$(function () {
    // stock product information
    let selectedProduct = {};

    // call ajax and add data to cart
    function addProductToCart(quantity) {
        const productToAdd = {
            idProduct: selectedProduct.id,
            quantity: quantity,
        };

        const csrfToken = $('form input[name="_csrfToken"]').val();

        $.ajax({
            url:
                "/ajax/addToCart/" +
                productToAdd.idProduct +
                "/" +
                productToAdd.quantity,
            type: "POST",
            headers: {
                "X-CSRF-Token": csrfToken,
            },
            success: function (response) {
                console.log("Produit ajouté avec succès :", response);
                // update user interface
                if (response.response.success) {
                    console.log(
                        "Nouveau contenu du panier:",
                        response.response.cart
                    );
                } else {
                    console.error(
                        "Échec de l'ajout au panier:",
                        response.response.message
                    );
                }
            },
            error: function (xhr, status, error) {
                console.error("Erreur lors de l'ajout du produit :", error);
            },
        });

        // close modal
        $("#product-modal").addClass("hidden");
    }

    // open the modal
    $(".product-card").on("click", function () {
        const card = $(this);
        selectedProduct = {
            id: card.data("id"),
            name: card.data("name"),
            price: parseFloat(card.data("price")),
        };

        $("#modal-name").text(selectedProduct.name);
        $("#modal-price").text(selectedProduct.price.toFixed(2) + " €");
        $("#modal-quantity").val(0); //reset quantity field
        $("#product-modal").removeClass("hidden");
    });

    // close the modal
    $("#modal-close").on("click", function () {
        $("#product-modal").addClass("hidden");
    });

    // deal with quick quantity button
    $(".quantity-preset-btn").on("click", function () {
        const quantity = parseFloat($(this).data("quantity"));
        addProductToCart(quantity);
    });

    // add button => Handle creation
    $("#modal-add").on("click", function () {
        let quantity = parseFloat($("#modal-quantity").val());
        if (quantity <= 0 || isNaN(quantity)) {
            return;
        }
        addProductToCart(quantity);
    });
});
