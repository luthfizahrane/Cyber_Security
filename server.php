<?php

$method = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['PATH_INFO'];
$body = json_decode(file_get_contents("php://input"), true);

// Konfigurasi koneksi database
$host = "localhost";
$username = "root"; // Sesuaikan dengan konfigurasi MySQL Anda
$password = "";     // Kosongkan jika tidak ada password
$database = "dns2";

// Membuat koneksi ke database
$conn = new mysqli($host, $username, $password, $database);

// Periksa koneksi
if ($conn->connect_error) {
    die("Koneksi ke database gagal: " . $conn->connect_error);
}

// Fungsi untuk rate limiting
function rateLimit($maxRequests, $timeWindow) {
    $ipAddress = $_SERVER['REMOTE_ADDR'];
    $currentTime = time();
    $rateLimitKey = "rate_limit_$ipAddress";

    // Simpan data sementara di database atau file
    global $conn; // Menggunakan koneksi database
    $conn->query("
        CREATE TABLE IF NOT EXISTS rate_limit (
            ip_address VARCHAR(45) PRIMARY KEY,
            request_count INT DEFAULT 0,
            last_request_time INT DEFAULT 0
        )
    ");

    // Ambil data rate limit
    $result = $conn->query("SELECT * FROM rate_limit WHERE ip_address = '$ipAddress'");
    $rateData = $result->fetch_assoc();

    if ($rateData) {
        $timeSinceLastRequest = $currentTime - $rateData['last_request_time'];

        if ($timeSinceLastRequest > $timeWindow) {
            // Reset setelah window waktu berlalu
            $conn->query("UPDATE rate_limit SET request_count = 1, last_request_time = $currentTime WHERE ip_address = '$ipAddress'");
        } else {
            if ($rateData['request_count'] >= $maxRequests) {
                header('HTTP/1.1 429 Too Many Requests');
                out(["error" => "Terlalu banyak permintaan. Coba lagi nanti."]);
            } else {
                $conn->query("UPDATE rate_limit SET request_count = request_count + 1 WHERE ip_address = '$ipAddress'");
            }
        }
    } else {
        $conn->query("INSERT INTO rate_limit (ip_address, request_count, last_request_time) VALUES ('$ipAddress', 1, $currentTime)");
    }
}

function out($data) {
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

// Rate limiting: Batas 100 permintaan per 60 detik
rateLimit(100, 60);

switch ($uri) {
    case "/product":
        if ($method === "POST") {
            $productId = $body['productId'];

            // Ambil produk dari database
            $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
            $stmt->bind_param("i", $productId);
            $stmt->execute();
            $result = $stmt->get_result();
            $product = $result->fetch_assoc();

            if ($product) {
                $_SESSION['product'] = $product; // Simpan sementara di session
                out(["message" => "Produk dipilih: " . json_encode($product)]);
            } else {
                out(["error" => "Produk tidak ditemukan."]);
            }
        }
        break;

    case "/shipping":
        if ($method === "POST") {
            $shippingMethod = $body['shippingMethod'];
            $allowedMethods = ['Same-Day', 'Express', 'Longer'];

            if (in_array($shippingMethod, $allowedMethods)) {
                $_SESSION['shippingMethod'] = $shippingMethod; // Simpan sementara di session
                out(["message" => "Metode pengiriman diatur ke $shippingMethod."]);
            } else {
                out(["error" => "Metode pengiriman tidak valid."]);
            }
        }
        break;

        case "/checkout":
            if ($method === "POST") {
                if (!isset($_SESSION['product'], $_SESSION['shippingMethod'])) {
                    out(["error" => "Lengkapi informasi checkout terlebih dahulu."]);
                }
        
                $product = $_SESSION['product'];
                $shippingMethod = $_SESSION['shippingMethod'];
                $name = $body['name'];
                $number = $body['number'];
                $address = $body['address'];
        
                // Generate nomor resi otomatis
                $lastResiQuery = $conn->query("SELECT resi_number FROM orders ORDER BY id DESC LIMIT 1");
                $lastResi = $lastResiQuery->fetch_assoc()['resi_number'] ?? 'DNS00000000';
                $nextResi = 'DNS' . str_pad((int)substr($lastResi, 3) + 1, 8, '0', STR_PAD_LEFT);
        
                // Simpan data ke database
                $stmt = $conn->prepare("
                    INSERT INTO orders (product_name, price, shipping_method, customer_name, customer_number, customer_address, courier_name, resi_number) 
                    VALUES (?, ?, ?, ?, ?, ?, 'DnS Express', ?)
                ");
                $stmt->bind_param(
                    "sdssss",
                    $product['name'],
                    $product['price'],
                    $shippingMethod,
                    $name,
                    $number,
                    $address,
                    $nextResi
                );
        
                if ($stmt->execute()) {
                    out(["message" => "Checkout berhasil. Nomor Resi: {$nextResi}"]);
                } else {
                    out(["error" => "Gagal menyimpan data checkout."]);
                }
            }
            break;
        

    case "/summary":
        if ($method === "GET") {
            // Ambil data checkout terakhir dari database
            $result = $conn->query("SELECT * FROM orders ORDER BY id DESC LIMIT 1");
            $order = $result->fetch_assoc();

            if ($order) {
                out($order);
            } else {
                out(["error" => "Belum ada data checkout."]);
            }
        }
        break;

    default:
        out(["error" => "Endpoint tidak ditemukan."]);
        break;
}

// Tutup koneksi database
$conn->close();
