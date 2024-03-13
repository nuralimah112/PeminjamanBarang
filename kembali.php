<?php
session_start();
require_once("database.php");

if (!isset($_SESSION['status']) || $_SESSION['status'] != "login") {
    header("location: login.php?msg=belum_login");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id_transaksi'])) {
    $id_transaksi = mysqli_real_escape_string($dbconnect, $_POST['id_transaksi']);

    // Retrieve transaction information
    $query = "SELECT * FROM transaksi WHERE id_transaksi = '$id_transaksi'";
    $result = mysqli_query($dbconnect, $query);

    if ($result) {
        $transactionInfo = mysqli_fetch_assoc($result);

        if ($transactionInfo) {
            $kode_barang = $transactionInfo['kode_barang'];
            
            // Retrieve item information
            $barangInfo = tampilanBarangById($kode_barang);

            if ($barangInfo) {
                mysqli_autocommit($dbconnect, false);

                // Increase stock
                $newStock = $barangInfo['jumlah'] + 1;
                $updateStockQuery = "UPDATE barang SET jumlah = '$newStock' WHERE kode_barang = '$kode_barang'";

                if (mysqli_query($dbconnect, $updateStockQuery)) {
                    // Delete transaction
                    $deleteTransactionQuery = "DELETE FROM transaksi WHERE id_transaksi = '$id_transaksi'";

                    if (mysqli_query($dbconnect, $deleteTransactionQuery)) {
                        mysqli_commit($dbconnect);
                        echo "Barang berhasil dikembalikan.";
                    } else {
                        mysqli_rollback($dbconnect);
                        echo "Error deleting transaction: " . mysqli_error($dbconnect);
                    }
                } else {
                    mysqli_rollback($dbconnect);
                    echo "Error updating stock: " . mysqli_error($dbconnect);
                }

                mysqli_autocommit($dbconnect, true);
            } else {
                echo "Item not found or error retrieving item information.";
            }
        } else {
            echo "Transaction not found.";
        }
    } else {
        echo "Error retrieving transaction information: " . mysqli_error($dbconnect);
    }
} else {
    echo "Invalid request.";
}
?>
