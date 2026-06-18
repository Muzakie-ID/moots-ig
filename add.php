<?php
require_once 'config/database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    
    // Validasi
    if (empty($username)) {
        echo json_encode(['success' => false, 'message' => 'Username tidak boleh kosong']);
        exit;
    }
    
    // Bersihkan username (hapus @ kalau ada)
    $username = str_replace('@', '', $username);
    $username = preg_replace('/\s+/', '', $username); // hapus spasi
    
    if (empty($username)) {
        echo json_encode(['success' => false, 'message' => 'Username tidak valid']);
        exit;
    }
    
    // Cek apakah username sudah ada
    $stmt = $conn->prepare("SELECT id FROM members WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Username sudah terdaftar']);
        exit;
    }
    
    // Insert data baru
    $stmt = $conn->prepare("INSERT INTO members (username) VALUES (?)");
    $stmt->bind_param("s", $username);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Username berhasil ditambahkan']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal menyimpan data']);
    }
    
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
}

$conn->close();
