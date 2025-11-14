<?php
require_once '../../config/database.php';

if(isset($_POST['update_buku'])){
    $buku_id = $_POST['buku_id'];
    $penulis_id = $_POST['penulis_id'];
    $kategori_id = $_POST['kategori_id'];
    $judul = $_POST['judul'];
    $penerbit = $_POST['penerbit'];
    $terbit_pada = $_POST['terbit_pada'];
    $deskripsi = $_POST['deskripsi'];
    
    // Handle file upload untuk cover
    $cover = null;
    $update_cover = false;
    
    if(isset($_FILES['cover']) && $_FILES['cover']['error'] === UPLOAD_ERR_OK){
        $cover = file_get_contents($_FILES['cover']['tmp_name']);
        $update_cover = true;
    }
    
    // Handle file upload untuk file_buku
    $file_buku = null;
    $update_file_buku = false;
    
    if(isset($_FILES['file_buku']) && $_FILES['file_buku']['error'] === UPLOAD_ERR_OK){
        $file_buku = file_get_contents($_FILES['file_buku']['tmp_name']);
        $update_file_buku = true;
    }

    try {
        // Cek apakah penulis_id dan kategori_id ada di database
        $check_stmt = $connection->prepare("SELECT penulis_id FROM penulis WHERE penulis_id = ?");
        $check_stmt->bind_param("i", $penulis_id);
        $check_stmt->execute();
        $check_stmt->store_result();
        
        if($check_stmt->num_rows === 0) {
            throw new Exception("Penulis tidak ditemukan");
        }
        $check_stmt->close();

        $check_stmt = $connection->prepare("SELECT kategori_id FROM kategori WHERE kategori_id = ?");
        $check_stmt->bind_param("i", $kategori_id);
        $check_stmt->execute();
        $check_stmt->store_result();
        
        if($check_stmt->num_rows === 0) {
            throw new Exception("Kategori tidak ditemukan");
        }
        $check_stmt->close();

        // Build query berdasarkan file yang diupdate
        if ($update_cover && $update_file_buku) {
            $stmt = $connection->prepare(
                "UPDATE buku SET penulis_id=?, kategori_id=?, judul=?, cover=?, deskripsi=?, file_buku=?, penerbit=?, terbit_pada=? WHERE buku_id=?"
            );
            $stmt->bind_param("iisbssssi", $penulis_id, $kategori_id, $judul, $cover, $deskripsi, $file_buku, $penerbit, $terbit_pada, $buku_id);
            $stmt->send_long_data(3, $cover);
            $stmt->send_long_data(5, $file_buku);
        } elseif ($update_cover) {
            $stmt = $connection->prepare(
                "UPDATE buku SET penulis_id=?, kategori_id=?, judul=?, cover=?, deskripsi=?, penerbit=?, terbit_pada=? WHERE buku_id=?"
            );
            $stmt->bind_param("iisbsssi", $penulis_id, $kategori_id, $judul, $cover, $deskripsi, $penerbit, $terbit_pada, $buku_id);
            $stmt->send_long_data(3, $cover);
        } elseif ($update_file_buku) {
            $stmt = $connection->prepare(
                "UPDATE buku SET penulis_id=?, kategori_id=?, judul=?, deskripsi=?, file_buku=?, penerbit=?, terbit_pada=? WHERE buku_id=?"
            );
            $stmt->bind_param("iissssi", $penulis_id, $kategori_id, $judul, $deskripsi, $file_buku, $penerbit, $terbit_pada, $buku_id);
            $stmt->send_long_data(4, $file_buku);
        } else {
            $stmt = $connection->prepare(
                "UPDATE buku SET penulis_id=?, kategori_id=?, judul=?, deskripsi=?, penerbit=?, terbit_pada=? WHERE buku_id=?"
            );
            $stmt->bind_param("iisssi", $penulis_id, $kategori_id, $judul, $deskripsi, $penerbit, $terbit_pada, $buku_id);
        }

        $stmt->execute();
        
        if ($stmt->affected_rows === 0) {
            throw new Exception("Tidak ada perubahan data atau buku tidak ditemukan");
        }
        
        $stmt->close();
        header("Location: ../index.php?success=1");
        exit();

    } catch (mysqli_sql_exception $e){
        echo "Database Error: " . $e->getMessage();
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    header("Location: ../index.php");
    exit();
}