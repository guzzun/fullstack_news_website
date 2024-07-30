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

$user_id = $_SESSION['user_id'];

if (isset($_GET['email']) && $_SESSION['user_role'] == 3) {
    $emailToDelete = mysqli_real_escape_string($conn, $_GET['email']);
    $deleteQuery = "DELETE FROM emails WHERE email = '$emailToDelete'";
    if (mysqli_query($conn, $deleteQuery)) {
        write_logs("delete-email");
        header('Location: ../dash_email.php');
        exit();
    } else {
        echo "Eroare la ștergerea înregistrării.";
    }
} elseif (isset($_GET['message']) && $_SESSION['user_role'] == 3) {
    $messageIdToDelete = mysqli_real_escape_string($conn, $_GET['message']);
    $deleteQuery = "DELETE FROM messages WHERE id_mail = '$messageIdToDelete'";
    if (mysqli_query($conn, $deleteQuery)) {
        write_logs("delete-message");
        header('Location: ../dash_email.php');
        exit();
    } else {
        echo "Eroare la ștergerea înregistrării.";
    }
} elseif (isset($_GET['comment']) && $_SESSION['user_role'] == 3) {
    $commentToDelete = mysqli_real_escape_string($conn, $_GET['comment']);
    $deleteQuery = "DELETE FROM articles_comments WHERE id = '$commentToDelete'";
    if (mysqli_query($conn, $deleteQuery)) {
        write_logs("delete-comment");
        header('Location: ../dash_comment.php');
        exit();
    } else {
        echo "Eroare la ștergerea înregistrării.";
    }
} elseif (isset($_GET['news']) && $_SESSION['user_role'] == 3) {
    $newsToDelete = mysqli_real_escape_string($conn, $_GET['news']);
    $deleteQuery = "DELETE FROM send_news WHERE id = '$newsToDelete'";
    if (mysqli_query($conn, $deleteQuery)) {
        write_logs("delete-news");
        header('Location: ../dash_send.php');
        exit();
    } else {
        echo "Eroare la ștergerea înregistrării.";
    }
} elseif (isset($_GET['inbox']) && $_SESSION['user_role'] == 3) {
    $inboxToDelete = mysqli_real_escape_string($conn, $_GET['inbox']);
    $deleteQuery = "DELETE FROM inbox WHERE id = '$inboxToDelete'";
    if (mysqli_query($conn, $deleteQuery)) {
        write_logs("delete-inbox");
        header('Location: ../dash_inbox.php');
        exit();
    } else {
        echo "Eroare la ștergerea înregistrării.";
    }
} elseif (isset($_GET['email']) || isset($_GET['message'])) {
    write_logs("unauthorized-delete");
    echo "Nu aveti permisiunea de a sterge";
}

mysqli_close($conn);