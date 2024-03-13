<?php
session_start();
require_once("database.php");


if (!isset($_SESSION['status']) || $_SESSION['status'] != "login") {
    header("location: login.php?msg=belum_login");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['kode_barang'])) {
    $kode_barang = mysqli_real_escape_string($dbconnect, $_POST['kode_barang']);

   
    if (!function_exists('tampilanBarangById')) {
        die('Error: tampilanBarangById function not found.');
    }

    $barangInfo = tampilanBarangById($kode_barang);

    if ($barangInfo) {
        $currentStock = $barangInfo['jumlah'];

        if ($currentStock > 0) {

          

      
            if (isset($_SESSION['user_data'])) {
                $nis = $_SESSION['user_data']['nis'];
                $tanggal_pinjam = date("Y-m-d");

         
                mysqli_autocommit($dbconnect, false);

          
                $newStock = $currentStock - 1;
                $updateStockQuery = "UPDATE barang SET jumlah = '$newStock' WHERE kode_barang = '$kode_barang'";

                if (mysqli_query($dbconnect, $updateStockQuery)) {
                 
                    $insertTransaksiQuery = "INSERT INTO transaksi (kode_barang, nis, tanggal_pinjam) VALUES ('$kode_barang', '$nis', '$tanggal_pinjam')";

                    if (mysqli_query($dbconnect, $insertTransaksiQuery)) {
              
                        mysqli_commit($dbconnect);
                        echo "Successfully borrowed item.";
                    } else {
      
                        mysqli_rollback($dbconnect);
                        echo "Error inserting transaction: " . mysqli_error($dbconnect);
                    }
                } else {

                    mysqli_rollback($dbconnect);
                    echo "Error updating stock: " . mysqli_error($dbconnect);
                }

   
                mysqli_autocommit($dbconnect, true);
            } else {
                echo "User data not found in the session.";
            }
        } else {
            echo "Item is out of stock.";
        }
    } else {
        echo "Item not found or error retrieving item information.";
    }
} else {
    echo "Invalid request.";
}
?>
