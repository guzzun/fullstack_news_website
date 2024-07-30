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

write_logs("view-login");

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    // Curata datele introduse de utilizator
    $email = filter($_POST['email']);
    $login = filter($_POST["login"]);
    $parola = filter($_POST['password']);

    // Validează emailul
    if (preg_match("/^[a-z0-9._%+\-]+@[a-z0-9.\-]+\.[a-z]{2,}$/i", $email)) {
        // Verifica dacă loginul respecta formatul
        if (preg_match('/^[A-Za-z0-9]{4,14}$/', $login)) {
            if (preg_match('/^[A-Za-z0-9@#$]{6,15}$/', $parola)) {
                // Verifica daca loginul sau emailul sunt deja inregistrate
                $check_query = "SELECT * FROM users WHERE login = ? OR email = ?";
                $stmt = mysqli_prepare($conn, $check_query);
                mysqli_stmt_bind_param($stmt, "ss", $login, $email);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);

                if (mysqli_num_rows($result) > 0) {
                    $message = "Numele sau email existente deja, folosește alt nume sau email";
                } else {
                    // Hash parola folosind password_hash() si algoritmul BCRYPT
                    $hashed_password = password_hash($parola, PASSWORD_BCRYPT);
                    $insert_query = "INSERT INTO users (login, password, email) VALUES (?, ?, ?)";
                    $stmt = mysqli_prepare($conn, $insert_query);
                    mysqli_stmt_bind_param($stmt, "sss", $login, $hashed_password, $email);

                    if (mysqli_stmt_execute($stmt)) {
                        $message = "Perfect, acum acccesați Login și autentificați-vă cu noul cont";
                    } else {
                        echo "Eroare la înregistrare: " . mysqli_error($conn);
                    }
                }
            } else {
                $message = "Parola poate să conțină numai litere/cifre/@#$, 6-15 caractere.";
            }
        } else {
            $message = "Loginul trebuie să conțină între 4 și 14 caractere alfanumerice";
        }
    } else {
        $message = "Adresa de email nu este validă";
    }

    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://kit.fontawesome.com/da9ff5ae16.js" crossorigin="anonymous"></script>
    <title> GUDNEWS </title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" href="img/logo.svg">
</head>

<body>
    <?php
    include "parts/sidebar.php";
    include "parts/sidemenu.php";
    ?>

    <div class="page">
        <form class="auth" action="<?php $_SERVER['SCRIPT_NAME'] ?>" method="post" autocomplete="off"
            onsubmit="return validateLoginForm();">
            <div class="auth_header">
                <h1>Înregistrare</h1>
                <a href="index.php"> <i class="back_btn fas fa-times"></i> </a>
            </div>

            <div class="auth_inner">
                <input type="email" placeholder="Email" name="email" id="email" title="characters@characters.domain"
                    maxlength="30" pattern="[a-z0-9._%+\-]+@[a-z0-9.\-]+\.[a-z]{2,}$" required autofocus>
                <input type="text" placeholder="Nume" name="login" id="login" title="Min 4 litere/cifre si max 14"
                    maxlength="14" pattern="^[A-Za-z0-9]{4,14}$" required>
                <input type="password" placeholder="Parola" name="password" id="password"
                    title="Min 6 litere/cifre/@#$ si max 15" maxlength="15" pattern="^[A-Za-z0-9@#$]{6,15}$" required>

                <!-- fara pattern -->
                <!-- <input type="text" placeholder="Email" name="email" id="email" title="characters@characters.domain"
                    maxlength="30" autofocus> -->
                <!-- <input type="text" placeholder="Name" name="login" id="login" maxlength="14" required>
                <input type="password" placeholder="Password" name="password" id="password" title="Min 6 litere/cifre si max 15" maxlength="15" required> -->
                <p class="message" id="message"><?php echo $message; ?></p>
                <span id="error" class="message"></span>

                <input type="submit" name="submit" value="Înregistrare" class="register_btn">
            </div>

            <div class="auth_footer">
                <p> Deja aveți un cont? <a href="login.php">Conectați-vă!</a></p>
            </div>
        </form>
    </div>

    <script src="js/script.js"></script>
</body>

</html>