// product-modal.js
document.addEventListener("DOMContentLoaded", () => {
    const modalElement = document.getElementById("product-modal");
    const modal = new bootstrap.Modal(modalElement);
    const modalName = document.getElementById("modal-name");
    const modalPrice = document.getElementById("modal-price");
    const quantityInput = document.getElementById("modal-quantity");
    const addButton = document.getElementById("modal-add");

    let selectedProduct = null;

    // Open modal when product card is clicked
    document.querySelectorAll(".product-card").forEach(card => {
        card.addEventListener("click", () => {
            selectedProduct = {
                id: card.dataset.id,
                name: card.dataset.name,
                price: parseFloat(card.dataset.price),
                category: card.dataset.category,
                image: card.dataset.image
            };
            modalName.textContent = selectedProduct.name;
            modalPrice.textContent = `${selectedProduct.price.toFixed(2)} €`;
            quantityInput.value = 1;
            modal.show();
        });
    });

    // Preset quantity buttons (quick add)
    document.querySelectorAll(".quantity-preset-btn").forEach(btn => {
        btn.addEventListener("click", () => {
            if (!selectedProduct) return;
            const quantity = parseFloat(btn.dataset.quantity);
            if (isNaN(quantity) || quantity <= 0) return;

            addToCart(selectedProduct, quantity); // add immediately
            modal.hide(); // close modal
        });
    });

    // Add to cart button (manual input)
    addButton.addEventListener("click", () => {
        if (!selectedProduct) return;
        const quantity = parseFloat(quantityInput.value);
        if (isNaN(quantity) || quantity <= 0) return;

        addToCart(selectedProduct, quantity); // add from input
        modal.hide();
    });
});