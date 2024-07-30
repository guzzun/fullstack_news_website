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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['submit'])) {
        $login = filter($_POST["login"]);
        $parola = filter($_POST['password']);

        if (!preg_match('/^[A-Za-z0-9]{4,14}$/', $login)) {
            $message .= "Loginul trebuie sa contină între 4 și 14 caractere alfanumerice<br>";
        }

        if (!preg_match('/^[A-Za-z0-9@#$]{6,15}$/', $parola)) {
            $message .= "Parola trebuie sa contină între 6 și 15 caractere alfanumerice și {@#$}<br>";
        }

        if (empty($message)) {
            $query = "SELECT * FROM users WHERE login = ?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, 's', $login);
            mysqli_stmt_execute($stmt);
            $res = mysqli_stmt_get_result($stmt);

            if (mysqli_num_rows($res) == 1) {
                $row = mysqli_fetch_assoc($res);
                $stored_hash = $row['password'];

                if (password_verify($parola, $stored_hash)) {
                    $_SESSION['user_id'] = $row['id_user'];
                    $_SESSION['user_name'] = $row['login'];
                    $_SESSION['user_role'] = $row['id_role'];

                    write_logs("logged");

                    // Check the user role and redirect 
                    if ($_SESSION['user_role'] == 2 || $_SESSION['user_role'] == 3) {
                        // Redirect to dash.php if role is 2 or 3
                        header('Location: http://' . $_SERVER['SERVER_NAME'] . $path . '/dash.php');
                    } else {
                        // Redirect to user.php for other roles
                        header('Location: http://' . $_SERVER['SERVER_NAME'] . $path . '/index.php');
                    }
                } else {
                    write_logs("password-error");
                    $message .= "Parola incorectă";
                }
            } else {
                write_logs("username-error");
                $message .= "Login incorect";
            }

            mysqli_stmt_close($stmt);
        }
    }
    mysqli_close($conn);
}

$login = $parola = "";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-spath=1.0">
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
                <h1>Logare</h1>
                <a href="index.php"> <i class="back_btn fas fa-times"></i> </a>
            </div>

            <div class="auth_inner">
                <input type="text" placeholder="Nume" name="login" id="login" title="Min 4 litere/cifre si max 14"
                    maxlength="14" pattern="^[A-Za-z0-9]{4,14}$" required autofocus>
                <input type="password" placeholder="Parola" name="password" id="password"
                    title="Min 6 litere/cifre si max 15" maxlength="15" pattern="^[A-Za-z0-9@#$]{6,15}$" required>

                <p class="message" id="message"><?php echo $message; ?></p>
                <span id="error" class="message"></span>

                <input type="submit" id="submit" name="submit" value="Logare" class="register_btn">
            </div>

            <div class="auth_footer">
                <p> Nu aveți un cont? <a href="register.php"> Înregistrați-vă! </a></p>
            </div>
        </form>
    </div>

    <script src="js/script.js"></script>
</body>

</html>