<?php
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

write_logs("view-right_block");

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['email'])) {
    $email = $_POST['email'];

    $email = filter($email);

    if (!preg_match("/^[a-z0-9._%+\-]+@[a-z0-9.\-]+\.[a-z]{2,}$/i", $email)) {
        $message = "Adresa de email introdusă nu este validă.";
    } else {
        // Verificăm dacă adresa de email există deja
        $checkEmailQuery = "SELECT * FROM emails WHERE email = '$email'";
        $result = $conn->query($checkEmailQuery);

        if ($result->num_rows > 0) {
            $message = "Această adresă de email există deja în baza de date.";
        } else {
            $insertQuery = "INSERT INTO emails (email) VALUES ('$email')";

            if ($conn->query($insertQuery) === TRUE) {
                write_logs("user-subscribe");
                $message = "Adresa de email a fost înregistrată cu succes.";
            } else {
                $message = "Eroare la înregistrarea adresei de email: " . $conn->error;
            }
        }
    }
} else {
    $message = "Vă rugăm să introduceți o adresă de email.";
}
?>

<!-- right block-->
<div class="right_block">

    <!-- related-->
    <div class="related">
        <div class="block_2">
            <h3>Populare</h3>
        </div>
        <?php
        $sql_related = "SELECT id, nr_vizionari, nume_imagine, titlu, data_plasarii
        FROM articles
        ORDER BY nr_vizionari DESC
        LIMIT 6";

        $results_related = mysqli_query($conn, $sql_related) or die(mysqli_error($conn));

        while ($row = mysqli_fetch_assoc($results_related)) {
            echo '
                <div class="related_block"> <!-- related_block -->
                    <div class="related_img">
                        <a href="article.php?id=' . $row['id'] . '">
                            <img src="' . $row['nume_imagine'] . '" alt="1+">
                        </a>
                    </div>
                    
                    <div class="related_text">
                        <a href="article.php?id=' . $row['id'] . '"> ' . $row['titlu'] . ' </a>
                        <div class="related_date">
                            <i class="fas fa-clock"></i>
                            <p> ' . $row['data_plasarii'] . ' </p>
                        </div>
                    </div>
                </div> <!-- ./related_block -->
                ';
        }
        ?>
    </div> <!-- ./related -->

    <!-- subscribe -->
    <div class="subscribe">
        <div class="subscribe_info">
            <h2 class="subscribe_title"> Înscrie-te pentru notificări! </h2>
            <p class="message" id="message"><?php echo $message; ?></p>
        </div>

        <form class="subscribe_form" action="<?php echo htmlspecialchars($_SERVER['SCRIPT_NAME']); ?>" method="post"
            autocomplete="off" onsubmit="return validateEmail();">
            <input type="email" placeholder="Adaugă email" name="email" id="email" title="characters@characters.domain"
                maxlength="30" pattern="[a-z0-9._%+\-]+@[a-z0-9.\-]+\.[a-z]{2,}$" required>
            <input id="sub_btn" type="submit" name="submit" value="Abonează-te">
        </form>
        <span id="error" class="message"></span>
    </div> <!-- subscribe -->

</div> <!-- ./right block -->

<?php
// Close the database connection after all operations are done
$conn->close();
?>