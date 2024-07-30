<?php
session_start();

if (!file_exists('utils.php')) {
    error_log("utils.php nu poate fi accesat.", 3, "error.txt");
    header('Location: /teza/fail.php');
    exit();
}
include_once "utils.php";

$filesToRequire = ['config.php', 'connect.php'];
$filesToInclude = ['logs.php'];

include_files($filesToRequire, 'require');
include_files($filesToInclude, 'include');

write_logs("view-contacts");

$response = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['Name'];
    $email = $_POST['Email'];
    $message = $_POST['Message'];

    $valid = true;

    if (!preg_match("/^[A-Za-z0-9 ]{1,25}$/", $name)) {
        $response .= "Numele introdus nu este valid. Trebuie să conțină între 1 și 25 caractere alfanumerice. Fără diacritice<br>";
        $valid = false;
    }

    if (!preg_match("/^[a-z0-9._%+\-]+@[a-z0-9.\-]+\.[a-z]{2,}$/i", $email)) {
        $response .= "Adresa de email introdusă nu este validă.<br>";
        $valid = false;
    }

    if (!preg_match("/^[a-zA-Z0-9\?\.\:\-, ]{0,150}$/", $message)) {
        $response .= "Mesajul introdus nu este valid. Poate conține doar caractere alfanumerice și semne de punctuație (?.,:-).<br>";
        $valid = false;
    }

    if ($valid) {
        $name = filter($name);
        $email = filter($email);
        $message = filter($message);

        $insertQuery = "INSERT INTO inbox (name, email, message) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($insertQuery);
        $stmt->bind_param("sss", $name, $email, $message);

        if ($stmt->execute()) {
            $response = "Datele au fost expediate cu succes.";
        } else {
            $response = "Eroare la înregistrarea datelor: " . $conn->error;
        }
    } else {
        if (empty($response)) {
            $response = "Datele introduse nu sunt valide.";
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="img/logo.svg">
    <link rel="stylesheet" href="css/style.css">
    <title> Contact</title>
</head>

<body>
    <!-- sidebar -->
    <?php include "parts/sidebar.php";
    include "parts/sidemenu.php";
    ?>

    <!-- page -->
    <div class="page">

        <!-- header -->
        <?php include "parts/header.php" ?>

        <div class="content">
            <!-- content -->
            <div class="container">

                <!-- form -->
                <section class="contacts">
                    <div class="contacts__content">
                        <h1 class="contacts__title">Să <span>colaborăm!</span></h1>
                        <div class="contacts__text">
                            <p>
                                Dacă sunteți interesat să lucrăm împreună sau pur și simplu doriți să ne trimiteți un
                                mesaj, suntem întotdeauna disponibili pentru discuții.
                            </p>
                        </div>
                        <img src="" alt="">

                        <form name="contacts_form" class="contacts__form form" method="post"
                            onsubmit="return validateContactForm();">
                            <p class="message" id="message"><?php echo $response; ?></p>
                            <div class="form__line">
                                <input required id="name" type="text" class="form__input" name="Name"
                                    placeholder="Nume *" pattern="^[A-Za-z0-9 ]{1,25}$"
                                    title="Min 3 litere/cifre si max 25. Fără diacritice" maxlength="25">
                            </div>
                            <div class="form__line">
                                <input required id="email" type="email" class="form__input" name="Email"
                                    placeholder="Email *" title="characters@characters.domain"
                                    pattern="[a-z0-9._%+\-]+@[a-z0-9.\-]+\.[a-z]{2,}$" maxlength="30">
                            </div>
                            <div class="form__line">
                                <input required id="text" type="text" class="form__input" name="Message"
                                    placeholder="Mesaj *" pattern="^[a-zA-Z0-9\?\.\:\-, ]{0,150}$"
                                    title="*Mesajul poate conține doar caractere alfanumerice și semne de punctuație (?.,:-). Fără diacritice"
                                    maxlength="150">
                            </div>
                            <div class="form__line">
                                <input id="submit" type="submit" class="form__button" value="trimite"></input>
                            </div>

                            <span id="error" class="form__msg"></span>
                        </form>

                        <div class="contacts__info info-contacts">
                            <div class="info-contacts__item">
                                <div class="info-contacts__icon">
                                    <i class="fa-solid fa-phone"></i>
                                </div>
                                <div class="info-contacts__body">
                                    <div class="info-contacts__title">TELEFON</div>
                                    <a href="tel: 069348461" class="info-contacts__value"> 069348461 </a>
                                </div>
                            </div>

                            <div class="info-contacts__item">
                                <div class="info-contacts__icon">
                                    <i class="fa-solid fa-location-dot"></i>
                                </div>
                                <div class="info-contacts__body">
                                    <div class="info-contacts__title">Locație</div>
                                    <a href="#" class="info-contacts__value"> Moldova,
                                        Chisinau </a>
                                </div>
                            </div>

                            <div class="info-contacts__item">
                                <div class="info-contacts__icon">
                                    <i class="fa-solid fa-envelope"></i>
                                </div>
                                <div class="info-contacts__body">
                                    <div class="info-contacts__title">EMAIL</div>
                                    <a href="mailto: guzzun@gmail.com" class="info-contacts__value">
                                        gudnews@gmail.com
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="contacts__map">
                        <iframe
                            src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d2788797.90080842!2d25.7497815!3d46.9531338!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x40c97c3628b769a1%3A0x258119acdf53accb!2sMoldova!5e0!3m2!1sro!2s!4v1687993331694!5m2!1sro!2s"
                            width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>
                </section>

            </div> <!-- ./container -->
        </div> <!-- ./content end -->
        <!-- footer -->
        <?php include "parts/footer.php" ?>
    </div> <!-- ./page-->

    <script src="js/script.js"></script>
    <script src="https://kit.fontawesome.com/da9ff5ae16.js" crossorigin="anonymous"></script>

</body>

</html>