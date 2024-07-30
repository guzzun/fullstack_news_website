<?php
$fileName = "error.txt";
$status = isset($_SERVER['REDIRECT_STATUS']) ? $_SERVER['REDIRECT_STATUS'] : 500;
$codes = array(
    400 => array('400 Bad Request', 'The request cannot be fulfilled due to bad syntax.'),
    403 => array('403 Forbidden', 'The server has refused to fulfill your request.'),
    404 => array('404 Not Found', ' Page not found on the server.'),
    405 => array('405 Method Not Allowed', 'The method specified in the request is not allowed for the specified resource.'),
    408 => array('408 Request Timeout', 'Your browser failed to send a request in the time allowed by the server.'),
    500 => array('500 Internal Server Error', 'The request was unsuccessful due to an unexpected condition encountered by the server.'),
    502 => array('502 Bad Gateway', 'The server received an invalid response while trying to carry out the request.'),
    504 => array('504 Gateway Timeout', 'The upstream server failed to send a request in the time allowed by the server.'),
);
$title = isset($codes[$status]) ? $codes[$status][0] : 'Unknown';
$message = isset($codes[$status]) ? $codes[$status][1] : 'Unknown';

if ($title == 'Unknown' || strlen($status) != 3) {
    $message = 'Please supply a valid HTTP status code.';
}
$fieldsSeparator = " | ";
$maxRecords = 10;
if (!file_exists($fileName)) {
    $fileO = fopen($fileName, "w") or die("Error opening file!");
    $logLine = "Date time" . $fieldsSeparator . $fieldsSeparator . "Page" . $fieldsSeparator . $fieldsSeparator . "Message";
    fwrite($fileO, $logLine . "\r\n");
    fclose($fileO);
}
// Verificăm dacă fișierul de jurnal există și conține deja înregistrări
if (file_exists($fileName)) {
    // Numărăm numărul actual de înregistrări în fișier
    $numRecords = count(file($fileName, FILE_IGNORE_NEW_LINES));

    // Dacă numărul actual de înregistrări depășește limita maximă, ștergem 
    if ($numRecords >= $maxRecords) {
        file_put_contents($fileName, '');
    }
}
$logLine = date("d/m/y H:i:s") . $fieldsSeparator . $_SERVER['REQUEST_URI'] . $fieldsSeparator . "Detected: " . $title . " - " . $message;
$fileO = fopen($fileName, "a") or die("Error opening file!");
fwrite($fileO, $logLine . "\r\n");
fclose($fileO);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Error Page </title>
    <script src="https://kit.fontawesome.com/da9ff5ae16.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="icon" href="img/logo.svg">
</head>

<body>

    <!-- sidemenu -->
    <?php include "parts/sidebar.php" ?>
    <?php include "parts/sidemenu.php" ?>

    <!-- page -->
    <div class="page">

        <!-- header -->
        <?php include "parts/header.php" ?>

        <!-- content -->
        <div id="content" class="content">
            <div class="container">
                <div class="content_inner">

                    <div class="error">
                        <div class="error_inner">
                            <img class="error_img" src="img/error.png" alt="">
                            <h1 class="error_title"> Ne pare rău, pagina pe care ați solicitat-o nu poate fi găsită.
                            </h1>
                            <h3 class="error_subtitle"> Este posibil ca adresa URL să fie scrisă greșit sau pagina pe
                                care o
                                căutați nu mai este disponibilă. </h3>
                        </div>
                    </div>

                </div> <!-- ./content inner -->
            </div> <!-- ./container -->
        </div> <!-- ./content end -->

        <!-- footer -->
        <?php include "parts/footer.php" ?>

    </div> <!-- ./page-->


</body>
<script src="js/script.js"></script>

</html>