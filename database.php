<?php
define ('DB_HOST', 'localhost');
define ('DB_USER', 'root');
define ('DB_PASS', '');
define ('DB_NAME', 'stsweb');
$dbconnect = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

function tampilan()
{
    global $dbconnect;
    $query = "SELECT transaksi.id_transaksi, transaksi.tanggal_pinjam, transaksi.kode_barang, transaksi.nis,
    barang.nama_brg, barang.kategori, barang.merk, barang.jumlah AS stok_barang,
    users.nama AS nama_peminjam, users.username
FROM transaksi
INNER JOIN barang ON transaksi.kode_barang = barang.kode_barang
INNER JOIN users ON transaksi.nis = users.nis;";
    
    $hasil = mysqli_query($dbconnect, $query);
    
    if (!$hasil) {
        die("Query failed: " . mysqli_error($dbconnect) . "<br>Query: " . $query);
    }
    
    $rows = [];
    
    while($row = mysqli_fetch_assoc($hasil))
    {
        $rows[] = $row;
    }
    
    return $rows;
}

function inputdata($inputdata)
{
    global $dbconnect;
    $sql = mysqli_query($dbconnect, $inputdata);
    if (!$sql) {
        echo "Error: " . mysqli_error($dbconnect);
    }
    return $sql;
}

function cek_login($username, $password)
{
    global $dbconnect;
    $uname = mysqli_real_escape_string($dbconnect, $username);
    $upass = md5(mysqli_real_escape_string($dbconnect, $password));

    $query = "SELECT id_login, nis, username, role FROM users WHERE username = ? AND password = ?";

    $stmt = mysqli_prepare($dbconnect, $query);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ss", $uname, $upass);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        mysqli_stmt_close($stmt);

        if ($row = mysqli_fetch_assoc($result)) {
            return $row;
        } else {
            return false;
        }        
    } else {
        return false;
    }
}




function get_user_data($username) {
    global $dbconnect;
    $result = mysqli_query($dbconnect, "SELECT * FROM users WHERE username = '$username'");
    
   
    $user_data = mysqli_fetch_assoc($result);

    return $user_data;
}


function tampilanBarang()
{
    global $dbconnect;
    $query = "SELECT * FROM barang";
    $result = mysqli_query($dbconnect, $query);

    if (!$result) {
        die("Query failed: " . mysqli_error($dbconnect) . "<br>Query: " . $query);
    }

    $barangList = [];

    while ($barang = mysqli_fetch_assoc($result)) {
        $barangList[] = $barang;
    }

    return $barangList;
}

function kurangiStok($kode_barang, $quantity)
{
    global $dbconnect;

    $kode_barang = mysqli_real_escape_string($dbconnect, $kode_barang);
    $quantity = mysqli_real_escape_string($dbconnect, $quantity);

    
    $barangInfo = tampilanBarangById($kode_barang);
    $currentStock = $barangInfo['jumlah'];


    if ($currentStock >= $quantity) {
        $newStock = $currentStock - $quantity;
        $updateStockQuery = "UPDATE barang SET jumlah = '$newStock' WHERE kode_barang = '$kode_barang'";

        if (mysqli_query($dbconnect, $updateStockQuery)) {
            return true; 
        } else {
            return "Error updating stock: " . mysqli_error($dbconnect);
        }
    } else {
        return "Not enough stock available.";
    }
}

function tampilanBarangById($kode_barang)
{
    global $dbconnect;

    $kode_barang = mysqli_real_escape_string($dbconnect, $kode_barang);

    $query = "SELECT * FROM barang WHERE kode_barang = '$kode_barang'";
    $result = mysqli_query($dbconnect, $query);

    if (!$result) {
        die("Query failed: " . mysqli_error($dbconnect) . "<br>Query: " . $query);
    }

    $barangInfo = mysqli_fetch_assoc($result);

    return $barangInfo;
}
?>