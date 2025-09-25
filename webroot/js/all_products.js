// document.addEventListener("DOMContentLoaded", () => {
//     const productsContainer = document.querySelector("main .grid");
//     if (!productsContainer) return;

//     // Création input recherche si pas présent
//     let searchInput = document.getElementById("search-products");
//     if (!searchInput) {
//         searchInput = document.createElement("input");
//         searchInput.type = "text";
//         searchInput.placeholder = "Rechercher un produit...";
//         searchInput.className =
//             "w-full p-3 mb-4 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400";
//         productsContainer.parentNode.insertBefore(
//             searchInput,
//             productsContainer
//         );
//     }

//     // Fonction pour afficher la liste de produits
//     function renderProducts(list) {
//         productsContainer.innerHTML = "";

//         if (list.length === 0) {
//             productsContainer.innerHTML = `<p class="text-gray-500">Aucun produit trouvé.</p>`;
//             return;
//         }

//         list.forEach((product) => {
//             const card = document.createElement("div");
//             card.className =
//                 "product-card border rounded-lg p-4 hover:shadow-lg cursor-pointer";
//             card.dataset.id = product.idProduct;
//             card.dataset.name = product.name;
//             card.dataset.price = product.price;

//             card.innerHTML = `
//                 <h3 class="font-bold text-lg mb-2">${product.name}</h3>
//                 <p class="text-gray-600 mb-2">${
//                     product.category ? product.category.name : "Non catégorisé"
//                 }</p>
//                 <p class="text-gray-800 font-semibold">${parseFloat(
//                     product.price
//                 ).toFixed(2)} €</p>
//             `;

//             productsContainer.appendChild(card);
//         });

//         attachProductClick();
//     }

//     // Filtrage en temps réel
//     searchInput.addEventListener("input", () => {
//         const term = searchInput.value.toLowerCase();
//         const filtered = allProducts.filter((p) =>
//             p.name.toLowerCase().includes(term)
//         );
//         renderProducts(filtered);
//     });

//     // Gestion modal produit
//     function attachProductClick() {
//         const productCards = document.querySelectorAll(".product-card");
//         productCards.forEach((card) => {
//             card.addEventListener("click", () => {
//                 const selectedProduct = {
//                     id: card.dataset.id,
//                     name: card.dataset.name,
//                     price: parseFloat(card.dataset.price),
//                 };

//                 // Remplir le modal
//                 const modal = document.getElementById("product-modal");
//                 document.getElementById("modal-name").textContent =
//                     selectedProduct.name;
//                 document.getElementById("modal-price").textContent =
//                     selectedProduct.price.toFixed(2) + " €";
//                 document.getElementById("modal-quantity").value = 0;

//                 // Afficher le modal
//                 modal.classList.remove("hidden");

//                 // Bouton ajouter au panier
//                 const addBtn = document.getElementById("modal-add");
//                 addBtn.onclick = () => {
//                     const quantity = parseFloat(
//                         document.getElementById("modal-quantity").value
//                     );
//                     if (!isNaN(quantity) && quantity > 0) {
//                         addProductToCart(selectedProduct.id, quantity);
//                         modal.classList.add("hidden");
//                     }
//                 };

//                 // Bouton fermeture
//                 document.getElementById("modal-close").onclick = () => {
//                     modal.classList.add("hidden");
//                 };

//                 // Boutons quantité rapide
//                 const presetBtns = document.querySelectorAll(
//                     ".quantity-preset-btn"
//                 );
//                 presetBtns.forEach((btn) => {
//                     btn.onclick = () => {
//                         const q = parseFloat(btn.dataset.quantity);
//                         addProductToCart(selectedProduct.id, q);
//                         modal.classList.add("hidden");
//                     };
//                 });
//             });
//         });
//     }

//     // Fonction AJAX pour ajouter produit au panier
//     function addProductToCart(idProduct, quantity) {
//         const csrfToken = document.querySelector(
//             'form input[name="_csrfToken"]'
//         ).value;

//         fetch(`/ajax/addToCart/${idProduct}/${quantity}`, {
//             method: "POST",
//             headers: {
//                 "X-CSRF-Token": csrfToken,
//             },
//         })
//             .then((r) => r.json())
//             .then((data) => {
//                 if (data.response.success) {
//                     console.log(
//                         "Produit ajouté au panier:",
//                         data.response.cart
//                     );
//                     updateCartUI(data.response.cart); // tu peux créer cette fonction
//                 } else {
//                     console.error(
//                         "Erreur ajout panier:",
//                         data.response.message
//                     );
//                 }
//             })
//             .catch((err) => console.error("Erreur AJAX:", err));
//     }

//     // Initial render
//     renderProducts(allProducts);
// });
