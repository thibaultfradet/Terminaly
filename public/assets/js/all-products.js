document.addEventListener("DOMContentLoaded", () => {
    const searchInput = document.getElementById("search-products");
    if (!searchInput) return;

    const productCards = Array.from(document.querySelectorAll(".product-card")).map(card => ({
        card,
        parent: card.closest(".col")
    }));

    searchInput.addEventListener("input", () => {
        const query = searchInput.value.trim().toLowerCase();

        productCards.forEach(({ card, parent }) => {
            if (!parent) return;
            const name = card.dataset.name.toLowerCase();
            if (name.includes(query)) {
                parent.classList.remove("d-none");
            } else {
                parent.classList.add("d-none");
            }
        });
    });
});