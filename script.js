// Ice cream menu data
const appRootUrl = new URL('.', document.currentScript.src);

const menuData = [
    {
        id: 1,
        name: 'Vanilla Dream',
        image: 'assets/images/products/Vanilla.jpg',
        category: 'vanilla',
        price: 100,
        calories: 180,
        sugar: 18,
        protein: 3,
        fat: 9,
        description: 'Classic creamy vanilla ice cream with a smooth texture and rich flavor',
        emoji: '🍨 '
    },
    {
        id: 2,
        name: 'Chocolate Bliss',
        image: 'assets/images/products/Chocolate bliss.webp',
        category: 'chocolate',
        price: 150,
        calories: 220,
        sugar: 22,
        protein: 4,
        fat: 12,
        description: 'Rich dark chocolate ice cream for chocolate lovers everywhere',
        emoji: '🍫'
    },
    {
        id: 3,
        name: 'Strawberry Swirl',
        image: 'assets/images/products/Strawberry swirl.jpg',
        category: 'fruity',
        price: 100,
        calories: 190,
        sugar: 20,
        protein: 2,
        fat: 8,
        description: 'Fresh strawberry ice cream with real fruit pieces and a sweet swirl',
        emoji: '🍓'
    },
    {
        id: 4,
        name: 'Mint Chocolate Chip',
        image: 'assets/images/products/Mint Chocochip.jpg',
        category: 'chocolate',
        price: 100,
        calories: 200,
        sugar: 20,
        protein: 3,
        fat: 10,
        description: 'Refreshing mint with dark chocolate chips for a perfect balance of flavors',
        emoji: '🌿'
    },
    {
        id: 5,
        name: 'Mango Paradise',
        image: 'assets/images/products/Mango.avif',
        category: 'fruity',
        price: 100,
        calories: 180,
        sugar: 18,
        protein: 2,
        fat: 7,
        description: 'Tropical mango flavor with a smooth finish that transports you to paradise',
        emoji: '🥭'
    },
    {
        id: 6,
        name: 'Cookie Dough',
        image: 'assets/images/products/Cookie.png',
        category: 'vanilla',
        price: 180,
        calories: 220,
        sugar: 22,
        protein: 3,
        fat: 10,
        description: 'Vanilla ice cream with chunks of cookie dough',
        emoji: '🍪'
    },
    {
        id: 7,
        name: 'Rocky Road',
        image: 'assets/images/products/RockyRoad.jpg',
        category: 'chocolate',
        price: 180,
        calories: 240,
        sugar: 24,
        fat:14,
        protein: 4,
        description: 'Chocolate with marshmallows and nuts for a rocky road adventurous treat',
        emoji: '🛣️'
    },
    {
        id: 8,
        name: 'Pistachio Dream',
        image: 'assets/images/products/Pistachio.gif',
        category: 'fruity',
        price: 100,
        calories: 180,
        sugar: 18,
        protein: 4,
        fat: 9,
        description: 'Nutty pistachio flavor, a delightful experience',
        emoji: '🥒'
    },
    {
        id: 9,
        name: 'Sugar Free Vanilla',
        image: 'assets/images/products/SugarFreeVanilla.jpg',
        category: 'sugarfree',
        price: 160,
        calories: 180,
        sugar: 0,
        protein: 3,
        fat: 9,
        description: 'Healthy vanilla ice cream with zero added sugar',
        emoji: '🍦'
    },
    {
        id: 10,
        name: 'Sugar Free Chocolate',
        image: 'assets/images/products/SugarFreeChocolate.jpg',
        category: 'sugarfree',
        price: 160,
        calories: 200,
        sugar: 0,
        protein: 4,
        fat: 11,
        description: 'Rich chocolate taste without added sugar',
        emoji: '🍫'
    },
    {
        id: 11,
        name: 'Strawberry + Vanilla',
        image: 'assets/images/products/Vanilla-ice-cream-with-strawberries.jpg',
        category: 'customize',
        price: 140,
        calories: 185,
        sugar: 19,
        protein: 3,
        fat: 8,
        description: 'A smooth combination of strawberry and vanilla ice cream',
        emoji: '🍓🍦'
    },
    {
        id: 12,
        name: 'Chocolate + Strawberry',
        image: 'assets/images/products/chocolate strawberry.jpg',
        category: 'customize',
        price: 160,
        calories: 210,
        sugar: 21,
        protein: 3,
        fat: 10,
        description: 'Rich chocolate blended with sweet strawberry ice cream',
        emoji: '🍫🍓'
    },
    {
        id: 13,
        name: 'Mango + Vanilla',
        image: 'assets/images/products/mango-vanilla.jpg',
        category: 'customize',
        price: 150,
        calories: 180,
        sugar: 18,
        protein: 3,
        fat: 8,
        description: 'Tropical mango paired with creamy vanilla ice cream',
        emoji: '🥭🍦'
    },
    {
        id: 14,
        name: 'Chocolate + Mango',
        image: 'assets/images/products/chocolate-mango.jpg',
        category: 'customize',
        price: 165,
        calories: 215,
        sugar: 21,
        protein: 4,
        fat: 10,
        description: 'A delicious mix of chocolate and tropical mango',
        emoji: '🍫🥭'
    }
];

// Shopping cart
let cart = [];
let currentFilter = 'all';
const cartStorageKey = document.getElementById('table-order-form') ? 'tableOrderCart' : 'cart';

// Initialize the page
document.addEventListener('DOMContentLoaded', () => {
    renderMenu();
    setupFilterButtons();
    setupReviewPhoto();
    renderUserReviews();
    loadCart();
    setupCheckoutButton();
    setupOrderTracking();
    showOrderSuccessMessage();
    const orderForm = document.getElementById('order-details-form');
    orderForm?.addEventListener('submit', async function(e) {
        if (orderForm.dataset.locationReady === 'true') {
            delete orderForm.dataset.locationReady;
            return;
        }

        e.preventDefault();
        if (cart.length === 0) {
            alert('Please add an item to your cart before placing the order.');
            return;
        }

        const updateOrderData = () => {
            const totalAmount = document.getElementById('total').textContent.replace('₹', '').trim();
            document.getElementById('total-input').value = totalAmount;
            document.getElementById('cart-items-input').value = JSON.stringify(cart.map(item => ({
                id: item.id,
                name: item.name,
                quantity: item.quantity,
                price: item.price,
                customization: item.customization || ''
            })));
        };
        if (orderForm.dataset.locationPending === 'true') return;
        orderForm.dataset.locationPending = 'true';
        const locationStatus = document.getElementById('order-location-status');
        document.getElementById('customer-latitude').value = '';
        document.getElementById('customer-longitude').value = '';
        if (navigator.geolocation) {
            locationStatus.textContent = 'Checking your location. Your delivery address will be used if location is unavailable...';
            try {
                const position = await new Promise((resolve, reject) => {
                    navigator.geolocation.getCurrentPosition(resolve, reject, {
                        enableHighAccuracy: true,
                        maximumAge: 0,
                        timeout: 15000
                    });
                });
                document.getElementById('customer-latitude').value = position.coords.latitude;
                document.getElementById('customer-longitude').value = position.coords.longitude;
                locationStatus.textContent = 'Location found. Placing your order...';
            } catch {
                locationStatus.textContent = 'Using the delivery address you provided.';
            }
        } else {
            locationStatus.textContent = 'Using the delivery address you provided.';
        }

        updateOrderData();
        orderForm.dataset.locationPending = 'false';
        orderForm.dataset.locationReady = 'true';
        orderForm.requestSubmit(e.submitter);
    });
});

function showOrderSuccessMessage() {
    const params = new URLSearchParams(window.location.search);
    if (params.get('order') !== 'success') return;

    const orderId = params.get('id');
    cart = [];
    saveCart();
    updateCartUI();
    showNotification(`Thank you for your order! Your order ID is #${orderId}.`);
    if (orderId) {
        const trackingInput = document.getElementById('tracking-order-id');
        const trackingForm = document.getElementById('tracking-form');
        if (trackingInput) trackingInput.value = orderId;
        if (trackingForm && trackingForm.requestSubmit) trackingForm.requestSubmit();
    }
    window.history.replaceState({}, document.title, window.location.pathname);
    document.getElementById('track-order')?.scrollIntoView({ behavior: 'smooth' });
}

function setupOrderTracking() {
    const trackingForm = document.getElementById('tracking-form');
    const result = document.getElementById('tracking-result');
    if (!trackingForm || !result) return;
    let refreshTimer;
    let trackingMap;
    let trackingMarkers = {};
    let trackedOrderId;

    async function loadOrder(orderId, refreshing = false) {
        if (!refreshing || orderId !== trackedOrderId) result.textContent = 'Checking order...';

        try {
            const trackUrl = new URL('server/core/track_order.php', appRootUrl);
            trackUrl.searchParams.set('id', orderId);
            const response = await fetch(trackUrl);
            const order = await response.json();
            if (!response.ok) throw new Error(order.error || 'Order not found.');

            if (orderId !== trackedOrderId) {
                trackedOrderId = orderId;
                trackingMap = undefined;
                trackingMarkers = {};
                result.innerHTML = `
                    <h3>Order #${order.id}</h3>
                    <p><strong>Status:</strong> <span id="tracked-status"></span></p>
                    <p><strong>Customer:</strong> <span id="tracked-customer"></span></p>
                    <p><strong>Total:</strong> ₹<span id="tracked-total"></span></p>
                    <p><strong>Placed:</strong> <span id="tracked-created"></span></p>
                    <div id="live-location-message" class="live-location-message"></div>
                    <div id="order-map" class="order-map" aria-label="Map showing your location, the shop, and live delivery location"></div>
                    <a id="order-google-map-link" class="map-btn" target="_blank" rel="noopener noreferrer" hidden>Open order location in Google Maps</a>
                `;
            }

            document.getElementById('tracked-status').textContent = order.status;
            document.getElementById('tracked-customer').textContent = order.customer_name;
            document.getElementById('tracked-total').textContent = Number(order.total).toFixed(2);
            document.getElementById('tracked-created').textContent = order.created_at;
            const locationMessage = document.getElementById('live-location-message');
            const mapLocations = [
                {
                    key: 'customer',
                    available: order.customer_location_available,
                    point: [Number(order.customer_latitude), Number(order.customer_longitude)],
                    label: 'Your order location'
                },
                {
                    key: 'shop',
                    available: order.shop_location_available,
                    point: [Number(order.shop_latitude), Number(order.shop_longitude)],
                    label: 'Shop location'
                },
                {
                    key: 'delivery',
                    available: order.location_available,
                    point: [Number(order.latitude), Number(order.longitude)],
                    label: 'Live delivery location'
                }
            ].filter(location => location.available);
            if (mapLocations.length && window.L) {
                const googleMapLink = document.getElementById('order-google-map-link');
                const primaryLocation = mapLocations.find(location => location.key === 'delivery')
                    || mapLocations.find(location => location.key === 'customer')
                    || mapLocations[0];
                if (googleMapLink) {
                    googleMapLink.href = `https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(primaryLocation.point.join(','))}`;
                    googleMapLink.hidden = false;
                }
                if (!trackingMap) {
                    trackingMap = L.map('order-map');
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '&copy; OpenStreetMap contributors'
                    }).addTo(trackingMap);
                }
                mapLocations.forEach(location => {
                    if (!trackingMarkers[location.key]) {
                        trackingMarkers[location.key] = L.marker(location.point)
                            .addTo(trackingMap)
                            .bindPopup(location.label);
                    } else {
                        trackingMarkers[location.key].setLatLng(location.point);
                    }
                });
                Object.keys(trackingMarkers).forEach(key => {
                    if (!mapLocations.some(location => location.key === key)) {
                        trackingMap.removeLayer(trackingMarkers[key]);
                        delete trackingMarkers[key];
                    }
                });
                const points = mapLocations.map(location => location.point);
                if (points.length > 1) {
                    trackingMap.fitBounds(L.latLngBounds(points), { padding: [24, 24], maxZoom: 16 });
                } else {
                    trackingMap.setView(points[0], 16);
                }
                const messages = [];
                if (order.customer_location_available) messages.push('Your order location');
                if (order.shop_location_available) messages.push('shop location');
                if (order.location_available) {
                    messages.push(order.location_updated_at
                        ? `live delivery location updated ${order.location_updated_at}`
                        : 'live delivery location');
                } else {
                    messages.push('live delivery location will appear when staff starts sharing it');
                }
                locationMessage.textContent = `${messages.join(' · ')}.`;
            } else {
                const googleMapLink = document.getElementById('order-google-map-link');
                if (googleMapLink) googleMapLink.hidden = true;
                locationMessage.textContent = 'Location map is not available right now.';
            }
            clearInterval(refreshTimer);
            refreshTimer = setInterval(() => loadOrder(orderId, true), 5000);
        } catch (error) {
            if (!refreshing) result.textContent = error.message;
        }
    }

    trackingForm.addEventListener('submit', event => {
        event.preventDefault();
        clearInterval(refreshTimer);
        trackedOrderId = undefined;
        loadOrder(document.getElementById('tracking-order-id').value);
    });

    const requestedOrderId = new URLSearchParams(window.location.search).get('track_order');
    if (requestedOrderId && /^[1-9]\d*$/.test(requestedOrderId)) {
        document.getElementById('tracking-order-id').value = requestedOrderId;
        trackingForm.requestSubmit();
    }
}

// Render menu items
function renderMenu(filter = currentFilter) {
    currentFilter = filter;
    const menuGrid = document.getElementById('menu-grid');
    if (!menuGrid) return;
    menuGrid.innerHTML = '';

    const filteredMenu = filter === 'all'
        ? menuData
        : menuData.filter(item => item.category === filter);

    filteredMenu.forEach(item => {
        const card = createIceCreamCard(item);
        menuGrid.appendChild(card);
    });
}

// Create ice cream card
function createIceCreamCard(item) {
    const card = document.createElement('div');
    card.className = 'ice-cream-card';
    card.innerHTML = `
        <div class="ice-cream-image">
            <img src="${item.image}" alt="${item.name}">
        </div>
        <div class="ice-cream-content">
            <h3>${item.name}</h3>
            <p>${item.description || ''}</p>
            <div class="nutrition-info">
                <p>🔥 Calories: ${item.calories} kcal</p>
                <p>🍬 Sugar: ${item.sugar} g</p>
                <p>💪 Protein: ${item.protein } g</p>
                <p>🥛 Fat: ${item.fat  } g</p>
            </div>
            <div class="ice-cream-footer">
                <span class="price">₹${item.price.toFixed(2)}</span>
                <div class="qty-selector">
                    <button class="qty-btn" type="button" data-action="decrease" data-id="${item.id}">-</button>
                    <input type="number" id="qty-${item.id}" class="qty-input" value="1" min="1" max="10">
                    <button class="qty-btn" type="button" data-action="increase" data-id="${item.id}">+</button>
                </div>
            </div>
            <button class="btn btn-primary add-to-cart-btn" type="button" data-add="${item.id}">
                Add to Cart
            </button>
        </div>
    `;

    card.querySelector('[data-action="decrease"]').addEventListener('click', () => changeQty(`qty-${item.id}`, -1));
    card.querySelector('[data-action="increase"]').addEventListener('click', () => changeQty(`qty-${item.id}`, 1));
    card.querySelector('[data-add]').addEventListener('click', () => addToCart(item.id));

    return card;
}

// Setup filter buttons
function setupFilterButtons() {
    const filterBtns = document.querySelectorAll('.filter-btn');
    filterBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            filterBtns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            currentFilter = btn.dataset.filter;
            renderMenu(currentFilter);
        });
    });
}

// Change quantity
function changeQty(inputId, change) {
    const input = document.getElementById(inputId);
    if (!input) return;
    let currentValue = parseInt(input.value, 10) || 1;
    currentValue += change;
    if (currentValue < 1) currentValue = 1;
    if (currentValue > 10) currentValue = 10;
    input.value = currentValue;
}

// Add to cart
function addToCart(itemId, customization = '') {
    const qtyInput = document.getElementById(`qty-${itemId}`);
    const quantity = parseInt(qtyInput.value, 10) || 1;
    const item = menuData.find(i => i.id === itemId);
    if (!item) return;

    const cartItem = cart.find(c => c.id === itemId && c.customization === customization);
    if (cartItem) {
        cartItem.quantity += quantity;
    } else {
        cart.push({
            ...item,
            customization,
            quantity: quantity
        });
    }

    qtyInput.value = 1;
    saveCart();
    updateCartUI();
    showNotification(`${item.name} added to cart!`);
}

// Show notification
function showNotification(message) {
    const notification = document.createElement('div');
    notification.style.cssText = `
        position: fixed;
        top: 80px;
        right: 20px;
        background-color: #27ae60;
        color: white;
        padding: 15px 20px;
        border-radius: 8px;
        z-index: 999;
        animation: slideIn 0.3s ease;
        box-shadow: 0 8px 20px rgba(0,0,0,0.15);
    `;
    notification.textContent = message;
    document.body.appendChild(notification);

    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => notification.remove(), 300);
    }, 2000);
}

function submitReview(){
    const name = document.getElementById('review-name').value.trim();
    const review = document.getElementById('review-text').value.trim();
    const photoInput = document.getElementById('review-photo');
    const photo = photoInput.files[0];

    if (!name || !review) {
        alert("Please fill all fields");
        return;
    }

    const reviewData = {
        name,
        review,
        photo: photo ? document.getElementById('review-photo-preview').src : ''
    };
    const savedReviews = JSON.parse(localStorage.getItem('customerReviews') || '[]');
    savedReviews.push(reviewData);
    localStorage.setItem('customerReviews', JSON.stringify(savedReviews));
    renderUserReviews();

    document.getElementById('review-name').value = '';
    document.getElementById('review-text').value = '';
    photoInput.value = '';
    resetReviewPhotoPreview();
    alert('Thank you for your review!');
}

function setupReviewPhoto() {
    const photoInput = document.getElementById('review-photo');
    if (!photoInput) return;

    photoInput.addEventListener('change', () => {
        const photo = photoInput.files[0];
        if (!photo) {
            resetReviewPhotoPreview();
            return;
        }
        if (!photo.type.startsWith('image/') || photo.size > 2 * 1024 * 1024) {
            alert('Please choose an image smaller than 2 MB.');
            photoInput.value = '';
            resetReviewPhotoPreview();
            return;
        }

        const reader = new FileReader();
        reader.addEventListener('load', () => {
            const preview = document.getElementById('review-photo-preview');
            preview.src = reader.result;
            preview.hidden = false;
        });
        reader.readAsDataURL(photo);
    });
}

function resetReviewPhotoPreview() {
    const preview = document.getElementById('review-photo-preview');
    preview.src = '';
    preview.hidden = true;
}

function renderUserReviews() {
    const reviewContainer = document.getElementById('user-reviews');
    if (!reviewContainer) return;

    const savedReviews = JSON.parse(localStorage.getItem('customerReviews') || '[]');
    reviewContainer.innerHTML = savedReviews.map(({ name, review, photo }) => `
        <div class="review-card user-review-card">
            ${photo ? `<img src="${photo}" alt="Photo shared by ${escapeReviewText(name)}">` : ''}
            <h3>${escapeReviewText(name)}</h3>
            <div class="stars">⭐⭐⭐⭐⭐</div>
            <p>"${escapeReviewText(review)}"</p>
        </div>
    `).join('');
}

function escapeReviewText(value) {
    return value.replace(/[&<>'"]/g, character => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        "'": '&#39;',
        '"': '&quot;'
    }[character]));
}

function paymentDone() {
    const paymentBtn = document.querySelector('.payment-card .btn');
    if (paymentBtn) {
        paymentBtn.disabled = true;
        paymentBtn.textContent = 'Payment Completed ✓';
    }
    alert('Payment completed. Thank you for ordering!');
}

// Update cart UI
function updateCartUI() {
    const cartItemsDiv = document.getElementById('cart-items');
    const cartCountSpan = document.getElementById('cart-count');
    const checkoutBtn = document.getElementById('checkout-btn');

    cartCountSpan.textContent = cart.reduce((sum, item) => sum + item.quantity, 0);
    checkoutBtn.disabled = cart.length === 0;

    if (cart.length === 0) {
        cartItemsDiv.innerHTML = '<p class="empty-cart">Your cart is empty</p>';
        updateCartSummary();
        return;
    }

    cartItemsDiv.innerHTML = cart.map((item, index) => `
        <div class="cart-item">
            <div class="item-info">
                <div class="item-name">${item.emoji} ${item.name}${item.customization ? ` <small>(Custom: ${escapeReviewText(item.customization)})</small>` : ''}</div>
                <div class="item-price">₹${item.price.toFixed(2)} each</div>
            </div>
            <div class="item-qty">
                <button class="qty-btn" type="button" data-update="${index}" data-change="-1">-</button>
                <input type="number" value="${item.quantity}" min="1" data-qty-input="${index}">
                <button class="qty-btn" type="button" data-update="${index}" data-change="1">+</button>
            </div>
            <div class="item-total">₹${(item.price * item.quantity).toFixed(2)}</div>
            <button class="btn btn-remove btn-small" type="button" data-remove="${index}">Remove</button>
        </div>
    `).join('');

    cartItemsDiv.querySelectorAll('[data-update]').forEach(button => {
        const cartIndex = Number(button.dataset.update);
        const change = Number(button.dataset.change);
        button.addEventListener('click', () => updateCartQty(cartIndex, change));
    });

    cartItemsDiv.querySelectorAll('[data-qty-input]').forEach(input => {
        const cartIndex = Number(input.dataset.qtyInput);
        input.addEventListener('change', () => updateCartQty(cartIndex, 0, input.value));
    });

    cartItemsDiv.querySelectorAll('[data-remove]').forEach(button => {
        const cartIndex = Number(button.dataset.remove);
        button.addEventListener('click', () => removeFromCart(cartIndex));
    });

    updateCartSummary();
}

// Update cart quantity
function updateCartQty(cartIndex, change, newValue = null) {
    const cartItem = cart[cartIndex];
    if (!cartItem) return;

    if (newValue !== null) {
        cartItem.quantity = Math.max(1, Math.min(10, parseInt(newValue, 10) || 1));
    } else {
        cartItem.quantity += change;
        if (cartItem.quantity < 1) cartItem.quantity = 1;
        if (cartItem.quantity > 10) cartItem.quantity = 10;
    }

    saveCart();
    updateCartUI();
}

// Remove from cart
function removeFromCart(cartIndex) {
    cart.splice(cartIndex, 1);
    saveCart();
    updateCartUI();
}

// Update cart summary
function updateCartSummary() {
    const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    const totalCalories = cart.reduce((sum, item) => sum + ((Number(item.calories) || 0) * item.quantity), 0);
    const totalSugar = cart.reduce((sum, item) => sum + ((Number(item.sugar) || 0) * item.quantity), 0);
    const tax = subtotal * 0.1;
    const total = subtotal + tax;

    document.getElementById('subtotal').textContent = `₹${subtotal.toFixed(2)}`;
    document.getElementById('tax').textContent = `₹${tax.toFixed(2)}`;
    document.getElementById('total-calories').textContent = `${totalCalories} kcal`;
    document.getElementById('total-sugar').textContent = `${totalSugar} g`;
    document.getElementById('total').textContent = `₹${total.toFixed(2)}`;
}

// Save cart to localStorage
function saveCart() {
    localStorage.setItem(cartStorageKey, JSON.stringify(cart));
}

// Load cart from localStorage
function loadCart() {
    const savedCart = localStorage.getItem(cartStorageKey);
    if (savedCart) {
        try {
            const parsedCart = JSON.parse(savedCart);
            cart = Array.isArray(parsedCart) ? parsedCart : [];
        } catch {
            cart = [];
            localStorage.removeItem(cartStorageKey);
        }
        updateCartUI();
    }
}

// Checkout button setup
function setupCheckoutButton() {
    const checkoutBtn = document.getElementById('checkout-btn');
    if (!checkoutBtn) return;

    checkoutBtn.addEventListener('click', () => {
        if (cart.length === 0) {
            alert('Cart Empty');
            return;
        }

        document.getElementById('order-form')?.scrollIntoView({ behavior: 'smooth' });
    });
}

// Add animation styles
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(400px);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(400px);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);