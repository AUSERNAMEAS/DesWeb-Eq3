// Arreglo de productos con sus detalles
let allProducts = [];
  
// selecciona los elementos d elhtml
const productGrid = document.getElementById('product-grid');
const loadMoreBtn = document.getElementById('load-more-btn');
const cartItemsDiv = document.getElementById('cart-items');
const cartSubtotalSpan = document.getElementById('cart-subtotal');
const cartShippingSpan = document.getElementById('cart-shipping');
const cartTotalSpan = document.getElementById('cart-total');
const checkoutBtn = document.getElementById('checkout-btn');

let cart = [];
let productsShown = 3;

/**
 * Renderiza los productos en la cuadrícula de la página.
 */
function renderProducts() {
    productGrid.innerHTML = '';
    for (let i = 0; i < productsShown; i++) {
        if (allProducts[i]) {
            const product = allProducts[i];
            const productCard = document.createElement('div');
            productCard.className = 'product-card';
            productCard.innerHTML = `
                <h3>${product.name}</h3>
                <img src="${product.image}" alt="${product.name}">
                <p>$${product.price.toFixed(2)} MXN</p>
                <button onclick="addToCart(${product.id})">Agregar al Carrito</button>
            `;
            productGrid.appendChild(productCard);
        }
    }
}

async function fetchProducts(){
    try {
        const response = await fetch('backend/backend.php');
        allProducts = await response.json();
        renderProducts();
    } catch (error) {
        console.error('Error al cargar productos:', error);
    }
}

/**
 funcion para agregar al producto o incrementae
 */
function addToCart(productId) {
    //funcion find() busca en un array y regresa el objeto
    const productToAdd = allProducts.find(arrayProduct => arrayProduct.id === productId);
    const existingItem = cart.find(item => item.id === productId);

    if (existingItem) {
        existingItem.quantity++;
    } else {
        cart.push({ ...productToAdd, quantity: 1 });
    }
    alert(productToAdd.name + ' ha sido agregado al carrito.');
    renderCart();
}


  //Elimina un producto del carrito usando su ID
 
function removeFromCart(productId) {
    //filter regresa un array,mete el onjeto si es true
    cart = cart.filter(item => item.id !== productId);
    renderCart();
}

//suma en el carrtio si se agrega un nuevo
function increaseQuantity(productId) {
    const item = cart.find(arrayProduct => arrayProduct.id === productId);
    if (item) {
        item.quantity++;
        renderCart();
    }
}

//disminuye o quita del carrito
function decreaseQuantity(productId) {
    const item = cart.find(arrayProduct => arrayProduct.id === productId);
    //si es true solo le resta
    if (item && item.quantity > 1) {
        item.quantity--;
        renderCart();
    } 
    
        //si es true lo quit
    else if (item && item.quantity === 1) {
        removeFromCart(productId);
    }
}

//renderiza el apartado del carrito
function renderCart() {
    //si el carrito esta vacio muestra eso
    if (cart.length === 0) {
        cartItemsDiv.innerHTML = '<p>Tu carrito está vacío.</p>';
        cartSubtotalSpan.textContent = '$0.00 MXN';
        cartShippingSpan.textContent = '$0.00 MXN';
        cartTotalSpan.textContent = '$0.00 MXN';
        checkoutBtn.style.display = 'none';
        return;
    }

    checkoutBtn.style.display = 'block';

    let subtotal = 0;
    cartItemsDiv.innerHTML = '';
    //hara un nuevvo div y se renombra cartitem por cada elemento,
    cart.forEach(item => {
        const itemDiv = document.createElement('div');
        itemDiv.className = 'cart-item';
        itemDiv.innerHTML = `
            <span>${item.name}</span>
            <div class="cart-quantity">
                <button onclick="decreaseQuantity(${item.id})">-</button>
                <span>${item.quantity}</span>
                <button onclick="increaseQuantity(${item.id})">+</button>
            </div>
            <span>$${(item.price * item.quantity).toFixed(2)} MXN</span>
        `;
        cartItemsDiv.appendChild(itemDiv);
        subtotal += item.price * item.quantity;
    });

    // si no hay productos no hay envio
    let shipping;
    if(subtotal>0){
        shipping= 80;
    }
    else{
        shipping = 0;
    }

    const total = subtotal + shipping;

    cartSubtotalSpan.textContent = `$${subtotal.toFixed(2)} MXN`;
    cartShippingSpan.textContent = `$${shipping.toFixed(2)} MXN`;
    cartTotalSpan.textContent = `$${total.toFixed(2)} MXN`;
}

// se agregan 7 productos mas,entonces se renderizan mas
loadMoreBtn.addEventListener('click', () => {
    productsShown += 7;
    if (productsShown >= allProducts.length) {
        productsShown = allProducts.length;
        loadMoreBtn.style.display = 'none';
    }
    renderProducts();
});

// te lleva al apartrado del checkout
checkoutBtn.addEventListener('click', () => {
    sessionStorage.setItem('carritoTemporal', JSON.stringify(cart)); //gaurda el carrito en session storage
    window.location.href = 'checkout.html';
});

// espera a que todo se cargue primero
document.addEventListener('DOMContentLoaded', () => {
    fetchProducts();
    renderCart();
    document.getElementById("btn-personalized").addEventListener('click',()=>{
        alert('su solicitud ha sido enviada');
    })
});
