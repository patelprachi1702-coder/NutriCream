document.addEventListener('DOMContentLoaded', () => {
    const params = new URLSearchParams(window.location.search);
    const requestedTableNumber = params.get('table') || '';
    const notice = document.getElementById('table-order-notice');
    const tableInput = document.getElementById('table-number-input');
    const tableNumberForm = document.getElementById('table-number-form');
    const tableNumberEntry = document.getElementById('table-number-entry');
    const form = document.getElementById('table-order-form');
    const message = document.getElementById('table-order-message');

    const setTableNumber = tableNumber => {
        tableInput.value = tableNumber;
        notice.textContent = `Welcome! You are ordering from Table ${tableNumber}.`;
        tableNumberForm.hidden = true;
        document.body.classList.remove('table-number-required');
    };

    if (/^[A-Za-z0-9-]{1,20}$/.test(requestedTableNumber)) {
        setTableNumber(requestedTableNumber);
    } else {
        tableNumberEntry.focus();
    }

    tableNumberForm.addEventListener('submit', event => {
        event.preventDefault();
        const tableNumber = tableNumberEntry.value.trim();
        if (!/^[A-Za-z0-9-]{1,20}$/.test(tableNumber)) {
            notice.textContent = 'Enter a valid table number using letters, numbers, or hyphens.';
            tableNumberEntry.focus();
            return;
        }
        setTableNumber(tableNumber);
    });

    if (params.get('offline_order') === 'success') {
        localStorage.removeItem('tableOrderCart');
        cart = [];
        updateCartUI();
        message.textContent = `Order #${params.get('id')} has been sent to the shop.`;
        window.history.replaceState({}, document.title, `${window.location.pathname}?table=${encodeURIComponent(requestedTableNumber)}`);
        document.getElementById('order-form').scrollIntoView({ behavior: 'smooth' });
    }

    document.getElementById('checkout-btn').addEventListener('click', () => {
        if (cart.length === 0) {
            showNotification('Add at least one item before placing your order.');
            return;
        }
        document.getElementById('order-form').scrollIntoView({ behavior: 'smooth' });
    });

    form.addEventListener('submit', event => {
        if (cart.length === 0) {
            event.preventDefault();
            showNotification('Add at least one item before placing your order.');
            return;
        }

        document.getElementById('offline-items').value = JSON.stringify(cart.map(item => ({
            id: item.id,
            name: item.name,
            quantity: item.quantity,
            price: item.price,
            customization: item.customization || ''
        })));
        document.getElementById('offline-total-input').value =
            document.getElementById('total').textContent.replace('₹', '').trim();
    });
});
