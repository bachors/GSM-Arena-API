<?php

require("../lib/gsm.php");

// Create objek
$gsm = new gsm();
// Return ARRAY
if (!empty($_GET['query'])) {
    $data = $gsm->search($_GET['query']);
} elseif (!empty($_GET['slug'])) {
    $data = $gsm->detail($_GET['slug']);
} elseif (!empty($_GET['brands'])) {
    $data = $gsm->getBrands();
} else {
    $data = array(
        "status" => "error"
    );
}
// JSON
header('Content-Type: application/json');

// Tanda * berarti memberi hak akses kesemua host/domain untuk mengkonsusmi data JSON ini via AJAX.
// Jika sobat hanya ingin domain sobat saja yang bisa mengkonsusmi data JSON ini via AJAX tinggal rubah seperti ini:
// header('Access-Control-Allow-Origin: http://domain-sobat.com');
header('Access-Control-Allow-Origin: *');

// Convert ARRAY to JSON
echo json_encode($data, JSON_PRETTY_PRINT);
