<?php
include ("connect.php"); // Includeți fișierul connect.php pentru a obține conexiunea la baza de date

$message = ''; // Inițializați mesajul cu o valoare goală

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verificați dacă formularul a fost trimis

    // Preiați adresa de email din formular
    $email = $_POST['email'];

    // Înscrieți adresa de email în baza de date (data va fi completată automat)
    $sql = "INSERT INTO emails (email) VALUES ('$email')";

    if ($conn->query($sql) === TRUE) {
        $message = "Adresa de email a fost înregistrată cu succes!";
    } else {
        $message = "Eroare la înregistrarea adresei de email: " . $conn->error;
    }
}

// Închideți conexiunea la baza de date
$conn->close();