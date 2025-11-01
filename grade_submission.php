<?php
session_start();
require_once 'config.php';

$id_oddaja = (int)$_POST['id_oddaja'];
$ocena = (int)$_POST['ocena'];
$komentar = isset($_POST['komentar']) ? $_POST['komentar'] : '';

// Update grade and comment
$sql = "UPDATE oddaje SET ocena = $ocena, komentar_ucitelja = '$komentar' WHERE id = $id_oddaja";
if ($conn->query($sql)) {
    echo json_encode(['uspeh' => true]);
} else {
    echo json_encode(['uspeh' => false, 'napaka' => $conn->error]);
}
?>