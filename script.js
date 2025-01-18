// Fungsi untuk mengirim permintaan ke server rest1
async function sendRequest(url, method, body = null) {
    const options = {
        method: method,
        headers: { "Content-Type": "application/json" },
        body: body ? JSON.stringify(body) : null,
    };

    const response = await fetch(url, options);
    return response.json();
}

// Fungsi untuk memilih produk
async function selectProduct() {
    const productId = document.getElementById("productSelect").value;

    try {
        const response = await sendRequest("http://localhost/rest1/server.php/product", "POST", { productId });
        displayNotification(response.message);
    } catch (error) {
        console.error("Error:", error);
        displayNotification("Terjadi kesalahan saat memilih produk.");
    }
}

// Fungsi untuk mengatur metode pengiriman
async function setShippingMethod() {
    const shippingMethod = document.getElementById("shippingMethodSelect").value;

    try {
        const response = await sendRequest("http://localhost/rest1/server.php/shipping", "POST", { shippingMethod });
        displayNotification(response.message);
    } catch (error) {
        console.error("Error:", error);
        displayNotification("Terjadi kesalahan saat mengatur metode pengiriman.");
    }
}

// Fungsi untuk mengirim data pelanggan
async function submitCheckout() {
    const name = document.getElementById("name").value;
    const number = document.getElementById("number").value;
    const address = document.getElementById("address").value;

    try {
        const response = await sendRequest("http://localhost/rest1/server.php/checkout", "POST", {
            name: name,
            number: number,
            address: address,
        });
        displayNotification(response.message);
    } catch (error) {
        console.error("Error:", error);
        displayNotification("Terjadi kesalahan saat mengirim data checkout.");
    }
}

// Fungsi untuk mendapatkan ringkasan checkout
async function getCheckoutSummary() {
    try {
        const response = await sendRequest("http://localhost/rest1/server.php/summary", "GET");

        document.getElementById("checkoutSummary").innerHTML = `
            <strong>Produk:</strong> ${response.product_name || "Tidak ada"} <br>
            <strong>Harga:</strong> ${response.price || "Tidak ada"} <br>
            <strong>Metode Pengiriman:</strong> ${response.shipping_method || "Tidak ada"} <br>
            <strong>Nama Pelanggan:</strong> ${response.customer_name || "Tidak ada"} <br>
            <strong>Alamat:</strong> ${response.customer_address || "Tidak ada"} <br>
            <strong>Nomor Resi:</strong> ${response.resi_number || "Tidak ada"} <br>
        `;
    } catch (error) {
        console.error("Error:", error);
        displayNotification("Terjadi kesalahan saat mengambil ringkasan checkout.");
    }
}

// Fungsi untuk menampilkan notifikasi
function displayNotification(message) {
    const notificationDiv = document.getElementById("notification");
    notificationDiv.innerHTML = `
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;
}