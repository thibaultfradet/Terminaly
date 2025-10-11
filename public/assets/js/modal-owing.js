document.addEventListener("DOMContentLoaded", () => {
    const modalElement = document.getElementById("modal-owing-details");
    const modal = new bootstrap.Modal(modalElement);
    const modalItems = document.getElementById("modal-items");
    const modalTotal = document.getElementById("modal-total");

    document.querySelectorAll(".open-modal-btn").forEach(btn => {
        btn.addEventListener("click", () => {
            const sale = JSON.parse(btn.dataset.sale);

            // Reset
            modalItems.innerHTML = "";
            let total = 0;

            sale.saleProducts.forEach(item => {
                const tr = document.createElement("tr");
                tr.innerHTML = `
                    <td>${item.product.name}</td>
                    <td>${item.quantity}</td>
                    <td>${item.price}</td>
                `;
                modalItems.appendChild(tr);
                total += parseFloat(item.price) * parseFloat(item.quantity);
            });

            modalTotal.textContent = total.toFixed(2);
            modal.show();
        });
    });
});