<?php
$host = "localhost";
$username = "root"; // Ubah jika menggunakan user lain
$password = ""; // Ubah jika menggunakan password
$database = "dns2";

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}