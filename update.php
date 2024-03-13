<?php
session_start();
require_once("database.php");


if (!isset($_SESSION['status']) || $_SESSION['status'] != "login") {
    header("location: login.php?msg=belum_login");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['kode_barang']) && isset($_POST['amount'])) {
    $kode_barang = mysqli_real_escape_string($dbconnect, $_POST['kode_barang']);
    $amount = (int)$_POST['amount'];

    
    if (!function_exists('tampilanBarangById')) {
        die('Error: tampilanBarangById function not found.');
    }


    $barangInfo = tampilanBarangById($kode_barang);

    if ($barangInfo) {
        $currentStock = $barangInfo['jumlah'];

      
        if (($amount < 0 && abs($amount) <= $currentStock) || ($amount >= 0)) {

            $newStock = $currentStock + $amount;
            $updateStockQuery = "UPDATE barang SET jumlah = '$newStock' WHERE kode_barang = '$kode_barang'";

            if (mysqli_query($dbconnect, $updateStockQuery)) {
                echo "Successfully updated stock.";
            } else {
                echo "Error updating stock: " . mysqli_error($dbconnect);
            }
        } else {
            echo "Stock kurang.";
        }
    } else {
        echo "Error.";
    }
} else {
    echo "Invalid request.";
}
?>
