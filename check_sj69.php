<?php
$db = new mysqli('localhost', 'root', '', 'ilena');

// Get SJ 69
$sj = $db->query("SELECT * FROM surat_jalan WHERE id = 69")->fetch_assoc();
echo "=== SJ #69 ===\n";
print_r($sj);

// Get order
$idPesanan = $sj['id_pesanan'] ?? 'unknown';
echo "\n=== ORDER: $idPesanan ===\n";
$order = $db->query("SELECT id_pesanan, nama, nohp, alamat_pengiriman FROM pemesanan_offline WHERE id_pesanan = '$idPesanan'")->fetch_assoc();
print_r($order);

$db->close();
