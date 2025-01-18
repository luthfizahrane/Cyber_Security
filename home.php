<?php
require 'auth.php';
$user_data = validate_jwt();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-commerce Checkout Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script defer src="script.js"></script>
</head>
<body>
    <div class="container my-5">
        <h1 class="mb-4">E-commerce Checkout Dashboard</h1>
        
        <!-- Pilih Produk -->
        <section class="mb-5">
            <h2>Pilih Produk</h2>
            <form>
                <div class="mb-3">
                    <label for="productSelect" class="form-label">Pilih Produk:</label>
                    <select id="productSelect" class="form-select">
                        <option value="1">Laptop</option>
                        <option value="2">Smartphone</option>
                        <option value="3">Headphones</option>
                        <option value="4">Smartwatch</option>
                        <option value="5">Tablet</option>
                        <option value="6">Mouse</option>
                    </select>
                </div>
                <button type="button" class="btn btn-primary" onclick="selectProduct()">Pilih Produk</button>
            </form>
        </section>

        <!-- Metode Pengiriman -->
        <section class="mb-5">
            <h2>Metode Pengiriman</h2>
            <form>
                <div class="mb-3">
                    <label for="shippingMethodSelect" class="form-label">Pilih Metode Pengiriman:</label>
                    <select id="shippingMethodSelect" class="form-select">
                        <option value="Same-Day">Same-Day</option>
                        <option value="Express">Express</option>
                        <option value="Longer">Longer</option>
                    </select>
                </div>
                <button type="button" class="btn btn-primary" onclick="setShippingMethod()">Atur Metode</button>
            </form>
        </section>

        <!-- Data Pelanggan -->
        <section class="mb-5">
            <h2>Data Pelanggan</h2>
            <form>
                <div class="mb-3">
                    <label for="name" class="form-label">Nama Anda:</label>
                    <input type="text" id="name" class="form-control">
                </div>
                <div class="mb-3">
                    <label for="number" class="form-label">Nomor Telepon:</label>
                    <input type="text" id="number" class="form-control">
                </div>
                <div class="mb-3">
                    <label for="address" class="form-label">Alamat:</label>
                    <input type="text" id="address" class="form-control">
                </div>
                <button type="button" class="btn btn-primary" onclick="submitCheckout()">Kirim Data</button>
            </form>
        </section>

        <!-- Notifikasi -->
        <div id="notification" class="mt-4"></div>

        <!-- Ringkasan Checkout -->
        <section>
            <h2>Ringkasan Checkout</h2>
            <button type="button" class="btn btn-secondary mb-3" onclick="getCheckoutSummary()">Lihat Ringkasan</button>
            <div id="checkoutSummary" class="alert alert-secondary"></div>
        </section>
    </div>
</body>
</html>