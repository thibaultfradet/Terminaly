// cart.js
// Manage cart in localStorage

function getCart() {
    return JSON.parse(localStorage.getItem("cart")) || {};
}

function saveCart(cart) {
    localStorage.setItem("cart", JSON.stringify(cart));
    updateCartUI();
}

function addToCart(product, quantity) {
    let cart = getCart();
    const id = product.id;

    if (cart[id]) {
        cart[id].quantity += quantity;
    } else {
        cart[id] = { ...product, quantity };
    }

    saveCart(cart);
}

function removeFromCart(productId) {
    let cart = getCart();
    delete cart[productId];
    saveCart(cart);
}

function clearCart() {
    localStorage.removeItem("cart");
    updateCartUI();
}


function updateCartUI() {
    const cart = getCart();
    const cartItems = document.getElementById("cart-items");
    const totalElement = document.getElementById("cart-total");

    if (!cartItems || !totalElement) return;

    cartItems.innerHTML = "";
    let total = 0;

    Object.values(cart).forEach(item => {
        const div = document.createElement("div");
        div.className = "d-flex justify-content-between align-items-center border-bottom py-2";

        // Left: product info
        const infoDiv = document.createElement("div");
        infoDiv.className = "d-flex flex-column";
        infoDiv.innerHTML = `
            <strong>${item.name}</strong>
            <small>Quantité: ${item.quantity}</small>
            <small>Total: ${(item.price * item.quantity).toFixed(2)} € (${item.price.toFixed(2)} € / u)</small>
        `;

        // Right: remove button
        const removeBtn = document.createElement("button");
        removeBtn.textContent = "-";
        removeBtn.className = "btn btn-danger btn-sm";
        removeBtn.style.width = "40px";   // plus large pour "finger-friendly"
        removeBtn.style.height = "40px";
        removeBtn.addEventListener("click", () => {
            removeFromCart(item.id);
        });

        div.appendChild(infoDiv);
        div.appendChild(removeBtn);
        cartItems.appendChild(div);

        total += item.price * item.quantity;
    });

    totalElement.textContent = `${total.toFixed(2)} €`;
}
// Initialize cart on DOM load
document.addEventListener("DOMContentLoaded", updateCartUI);