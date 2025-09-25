document.addEventListener("DOMContentLoaded", () => {
    // -----------------------
    // VARIABLES GLOBALES
    // -----------------------
    const productsContainer = document.querySelector("main .grid");
    let selectedProduct = {};

    // Créer input recherche si on est sur "Tous les produits"
    let searchInput = document.getElementById("search-products");
    if (!searchInput && typeof allProducts !== "undefined") {
        searchInput = document.createElement("input");
        searchInput.type = "text";
        searchInput.placeholder = "Rechercher un produit...";
        searchInput.className =
            "w-full p-3 mb-4 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400";
        productsContainer.parentNode.insertBefore(
            searchInput,
            productsContainer
        );
    }

    // -----------------------
    // RENDER PRODUCTS
    // -----------------------
    function renderProducts(list) {
        if (!productsContainer) return;
        productsContainer.innerHTML = "";

        if (list.length === 0) {
            productsContainer.innerHTML = `<p class="text-gray-500">Aucun produit trouvé.</p>`;
            return;
        }

        list.forEach((product) => {
            const card = document.createElement("div");
            card.className =
                "product-card border rounded-lg p-4 hover:shadow-lg cursor-pointer";
            card.dataset.id = product.id;
            card.dataset.name = product.name;
            card.dataset.price = product.price;
            card.innerHTML = `
                <h3 class="font-bold text-lg mb-2">${product.name}</h3>
                <p class="text-gray-600 mb-2">${
                    product.category ? product.category.name : "Non catégorisé"
                }</p>
                <p class="text-gray-800 font-semibold">${parseFloat(
                    product.price
                ).toFixed(2)} €</p>
            `;
            productsContainer.appendChild(card);
        });

        attachProductClick();
    }

    // -----------------------
    // FILTRAGE EN TEMPS RÉEL
    // -----------------------
    if (searchInput) {
        searchInput.addEventListener("input", () => {
            const term = searchInput.value.toLowerCase();
            const filtered = allProducts.filter((p) =>
                p.name.toLowerCase().includes(term)
            );
            renderProducts(filtered);
        });

        // initial render
        renderProducts(allProducts);
    }

    // -----------------------
    // GESTION MODAL PRODUIT
    // -----------------------
    function attachProductClick() {
        const productCards = document.querySelectorAll(".product-card");
        productCards.forEach((card) => {
            card.addEventListener("click", () => {
                selectedProduct = {
                    id: card.dataset.id,
                    name: card.dataset.name,
                    price: parseFloat(card.dataset.price),
                };

                const modal = document.getElementById("product-modal");
                document.getElementById("modal-name").textContent =
                    selectedProduct.name;
                document.getElementById("modal-price").textContent =
                    selectedProduct.price.toFixed(2) + " €";
                document.getElementById("modal-quantity").value = 0;
                modal.classList.remove("hidden");
            });
        });
    }

    // -----------------------
    // AJAX ADD TO CART
    // -----------------------
    function addProductToCart(idProduct, quantity) {
        const csrfToken = document.querySelector(
            'form input[name="_csrfToken"]'
        ).value;

        fetch(`/ajax/addToCart/${idProduct}/${quantity}`, {
            method: "POST",
            headers: { "X-CSRF-Token": csrfToken },
        })
            .then((r) => r.json())
            .then((data) => {
                if (data.response.success) {
                    getCart();
                } else {
                    console.error(
                        "Erreur ajout panier:",
                        data.response.message
                    );
                }
            })
            .catch((err) => console.error("Erreur AJAX:", err));

        document.getElementById("product-modal").classList.add("hidden");
    }

    // -----------------------
    // GESTION DU PANIER
    // -----------------------
    function getCart() {
        $.ajax({
            url: "/ajax/getCart",
            type: "GET",
            success: function (response) {
                if (response.response.success) {
                    renderCart(response.response.cart);
                } else {
                    console.error(
                        "Impossible de récupérer le panier :",
                        response.response.message
                    );
                }
            },
            error: function (xhr, status, error) {
                console.error("Erreur lors du chargement du panier :", error);
            },
        });
    }

    function renderCart(cart) {
        const cartContainer = $("#cart-items");
        cartContainer.empty();

        if (!cart || cart.length === 0) {
            cartContainer.append(
                `<p class="text-gray-500">Votre panier est vide.</p>`
            );
            $("#cart-total").text("0,00 €");
            return;
        }

        let total = 0;
        Object.values(cart).forEach((item) => {
            const lineTotal = item.price * item.quantity;
            total += lineTotal;
            cartContainer.append(`
                <div class="flex items-center justify-between border-b pb-2">
                    <div>
                        <p class="font-semibold">${item.name}</p>
                        <p class="font-semibold">${lineTotal.toFixed(2)} €</p>
                        <p class="text-sm text-gray-500"><b>x${
                            item.quantity
                        }</b></p>
                    </div>
                    <button class="remove-from-cart bg-red-600 text-white text-lg px-6 py-3 rounded font-black" data-id="${
                        item.idProduct
                    }">-</button>
                </div>
            `);
        });
        $("#cart-total").text(total.toFixed(2) + " €");
    }

    // -----------------------
    // EVENTS MODAL
    // -----------------------
    $("#modal-close").on("click", () =>
        document.getElementById("product-modal").classList.add("hidden")
    );
    $("#modal-add").on("click", () => {
        const quantity = parseFloat($("#modal-quantity").val());
        if (!isNaN(quantity) && quantity > 0)
            addProductToCart(selectedProduct.id, quantity);
    });
    $(".quantity-preset-btn").on("click", function () {
        const quantity = parseFloat($(this).data("quantity"));
        const idProduct = $(this).data("id") || selectedProduct.id;
        addProductToCart(idProduct, quantity);
    });

    // -----------------------
    // REMOVE FROM CART
    // -----------------------
    $(document).on("click", ".remove-from-cart", function () {
        const idProduct = $(this).data("id");
        const csrfToken = $('form input[name="_csrfToken"]').val();
        $.ajax({
            url: "/ajax/removeFromCart/" + idProduct,
            type: "POST",
            headers: { "X-CSRF-Token": csrfToken },
            success: function (response) {
                if (response.response.success) getCart();
            },
            error: function (xhr, status, error) {
                console.error("Erreur suppression :", error);
            },
        });
    });

    // -----------------------
    // INITIAL LOAD
    // -----------------------
    getCart();
});
