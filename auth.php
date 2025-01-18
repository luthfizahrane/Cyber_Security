<?php
require 'vendor/autoload.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

// Mendefinisikan kunci rahasia
$secret_key = 'your_secret_key_here';

function validate_jwt()
{
    // Menggunakan variabel $secret_key dari global scope
    global $secret_key;

    // Mengecek apakah cookie token tersedia
    if (!isset($_COOKIE['token'])) {
        header('Location: login.php');
        exit();
    }

    $token = $_COOKIE['token'];

    try {
        // Mendekode token menggunakan kunci rahasia
        $decoded = JWT::decode($token, new Key($secret_key, 'HS256'));
        return $decoded->data;
    } catch (Exception $e) {
        // Mengarahkan pengguna kembali ke login jika token tidak valid
        header('Location: login.php');
        exit();
    }
}