window.openDeleteModal = function (orderId, orderNumber) {
    const modal = document.getElementById('deleteOrderModal');
    const form = document.getElementById('deleteOrderForm');
    const label = document.getElementById('delOrderNumber');

    if (form) {
        form.action = '/admin/orders/' + orderId;
    }
    if (label) {
        label.textContent = orderNumber;
    }
    if (modal) {
        modal.classList.remove('hidden');
    }
};
