// Transaction handling dengan barcode scanner

let cart = [];
let total = 0;

document.addEventListener('DOMContentLoaded', function() {
    const barcodeInput = document.getElementById('barcodeInput');
    const paymentInput = document.getElementById('paymentAmount');
    const processBtn = document.getElementById('processPayment');

    if (barcodeInput) {
        barcodeInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                searchProductByBarcode(this.value);
                this.value = '';
            }
        });
    }

    if (paymentInput) {
        paymentInput.addEventListener('input', calculateChange);
    }

    if (processBtn) {
        processBtn.addEventListener('click', processPayment);
    }
});

function searchProductByBarcode(barcode) {
    if (!barcode) return;

    fetch(BASE_URL + 'products/searchByBarcode', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'barcode=' + encodeURIComponent(barcode)
    })
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            alert(data.error);
        } else {
            addToCart(data);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat mencari produk');
    });
}

function addToCart(product) {
    const existingItem = cart.find(item => item.product_id === product.id);

    if (existingItem) {
        existingItem.quantity++;
        existingItem.subtotal = existingItem.quantity * existingItem.price;
    } else {
        cart.push({
            product_id: product.id,
            name: product.name,
            price: parseFloat(product.price),
            quantity: 1,
            subtotal: parseFloat(product.price)
        });
    }

    updateCartDisplay();
}

function removeFromCart(index) {
    cart.splice(index, 1);
    updateCartDisplay();
}

function updateQuantity(index, newQuantity) {
    if (newQuantity < 1) {
        removeFromCart(index);
        return;
    }

    cart[index].quantity = parseInt(newQuantity);
    cart[index].subtotal = cart[index].quantity * cart[index].price;
    updateCartDisplay();
}

function updateCartDisplay() {
    const cartItems = document.getElementById('cartItems');
    const totalElement = document.getElementById('totalAmount');

    if (cart.length === 0) {
        cartItems.innerHTML = '<tr><td colspan="5" class="text-center text-muted">Keranjang kosong</td></tr>';
        total = 0;
    } else {
        cartItems.innerHTML = cart.map((item, index) => `
            <tr>
                <td>${item.name}</td>
                <td>Rp ${item.price.toLocaleString('id-ID')}</td>
                <td>
                    <input type="number" class="form-control form-control-sm" style="width: 80px;" 
                           value="${item.quantity}" min="1" 
                           onchange="updateQuantity(${index}, this.value)">
                </td>
                <td>Rp ${item.subtotal.toLocaleString('id-ID')}</td>
                <td>
                    <button class="btn btn-sm btn-danger" onclick="removeFromCart(${index})">Hapus</button>
                </td>
            </tr>
        `).join('');

        total = cart.reduce((sum, item) => sum + item.subtotal, 0);
    }

    totalElement.textContent = 'Rp ' + total.toLocaleString('id-ID');
    calculateChange();
}

function calculateChange() {
    const paymentInput = document.getElementById('paymentAmount');
    const changeElement = document.getElementById('changeAmount');
    
    const payment = parseFloat(paymentInput.value) || 0;
    const change = payment - total;

    changeElement.textContent = change >= 0 ? 'Rp ' + change.toLocaleString('id-ID') : 'Rp 0';
    changeElement.className = change >= 0 ? 'text-success' : 'text-danger';
}

function processPayment() {
    if (cart.length === 0) {
        alert('Keranjang masih kosong');
        return;
    }

    const payment = parseFloat(document.getElementById('paymentAmount').value) || 0;
    
    if (payment < total) {
        alert('Pembayaran kurang!');
        return;
    }

    const change = payment - total;

    const data = {
        items: JSON.stringify(cart),
        total: total,
        payment: payment,
        change: change
    };

    fetch(BASE_URL + 'transactions/store', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams(data)
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            alert('Transaksi berhasil!\nKode: ' + result.transaction_code);
            cart = [];
            total = 0;
            updateCartDisplay();
            document.getElementById('paymentAmount').value = '';
            document.getElementById('barcodeInput').focus();
        } else {
            alert('Transaksi gagal: ' + result.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat memproses transaksi');
    });
}
