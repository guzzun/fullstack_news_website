<?php
session_start();

if (!file_exists('../utils.php')) {
    error_log("utils.php nu poate fi accesat.", 3, "error.txt");
    header('Location: /teza/fail.php');
    exit();
}

include_once "../utils.php";

// Apelăm funcția pentru a verifica și include fișierele necesare
include_files(['../logs.php', '../config.php', '../connect.php']);

// Verificăm dacă utilizatorul este autentificat
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$id = $_GET['id'];

// Verificăm dacă utilizatorul este autentificat
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    // Verificam daca utilizatorul are rolul 3
    if ($_SESSION['user_role'] == 3) {
        $query2 = "SELECT nume_imagine FROM articles WHERE id = '$id'";
        $result = mysqli_query($conn, $query2);
        $filename = mysqli_fetch_row($result);
        $filename = $filename[0];

        if ($filename) {
            // Încearcă să ștergi fișierul și ignoră avertismentele
            @unlink("../img/post/" . $filename); // '@' suprimă eventualele avertismente

            // Șterge articolul din baza de date
            $query1 = "DELETE FROM articles WHERE id = '$id'";
            mysqli_query($conn, $query1);

            header("Location: ../dash_article.php");
            exit();
        } else {
            echo 'Nu ești autorizat pentru a șterge articole';
        }
    } else {
        echo 'Nu ești autorizat pentru a șterge articole';
    }
} else {
    header("Location: ../login.php");
    exit();
}

mysqli_close($conn);