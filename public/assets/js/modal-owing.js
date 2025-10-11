$(document).ready(function() {
    var $modal = $('#modal-owing-details');
    var $modalItems = $('#modal-items');

    // Open modal
    $('.open-modal-btn').click(function() {
        var sale = $(this).data('sale');

        // Clear previous rows
        $modalItems.empty();

        // Fill modal with products
        if (sale.saleProducts && sale.saleProducts.length > 0) {
            $.each(sale.saleProducts, function(i, item) {
                $modalItems.append(
                    '<tr>' +
                        '<td class="px-4 py-2 text-sm text-gray-900">' + item.product.name + '</td>' +
                        '<td class="px-4 py-2 text-sm text-gray-900">' + item.quantity + '</td>' +
                        '<td class="px-4 py-2 text-sm text-gray-900">' + item.price + '</td>' +
                    '</tr>'
                );
            });
        }

        // Show modal
        $modal.show();
    });

    // Close modal
    $('#modal-close').click(function() {
        $modal.hide();
    });

    // Click outside modal to close
    $modal.click(function(e) {
        if(e.target === this) {
            $modal.hide();
        }
    });

    // Double confirmation for "Compléter"
    $('.complete-btn').click(function() {
        var saleId = $(this).data('sale-id');
        if(confirm('Êtes-vous sûr de vouloir compléter cette vente ?')) {
            if(confirm('Confirmez définitivement la complétion de la vente ?')) {
                console.log('Vente complétée :', saleId);
            }
        }
    });
});