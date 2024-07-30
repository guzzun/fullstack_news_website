<?php
session_start();

if (!file_exists('utils.php')) {
	error_log("utils.php nu poate fi accesat.", 3, "error.txt");
	header('Location: /teza/fail.php');
	exit();
}

include_once "utils.php";
// Apelăm funcția pentru a verifica și include fișierele necesare
include_files(['logs.php', 'connect.php']);
write_logs("view-dash_email");

// Verificăm dacă utilizatorul este autentificat
if (!isset($_SESSION['user_id'])) {
	header("Location: login.php");
	exit();
}

// Verifică dacă utilizatorul are rolul 3
if ($_SESSION['user_role'] != 3) {
	header("Location: index.php");
	exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<script src="https://kit.fontawesome.com/da9ff5ae16.js" crossorigin="anonymous"></script>
	<link rel="icon" href="img/logo.svg">
	<link rel="stylesheet" href="css/style.css">
	<title>GUDNEWS ADMIN</title>
</head>

<body>
	<!-- sidebar -->
	<?php include "parts/sidebar.php"; ?>
	<?php include "parts/sidemenu_adm.php"; ?>

	<!-- page -->
	<div class="page">
		<?php include "parts/header_adm.php"; ?>

		<!-- table -->
		<div id="content" class="content">
			<div class="container">
				<div class="content_inner">
					<?php
					if (isset($_POST['submit'])) {
						$message = '';
						if (isset($_POST['selected_emails']) && is_array($_POST['selected_emails']) && !empty($_POST['text'])) {
							$selectedEmails = $_POST['selected_emails'];
							$text = filter($_POST['text']);

							if (preg_match('/^[a-zA-Z0-9\?\.\(\)\:\-,! ]{0,150}$/', $text)) {
								$values = array_map(function ($email) use ($text, $conn) {
									return "('" . mysqli_real_escape_string($conn, $email) . "', '" . mysqli_real_escape_string($conn, $text) . "')";
								}, $selectedEmails);

								$insertQuery = "INSERT INTO messages (email, message) VALUES " . implode(", ", $values);
								if (mysqli_query($conn, $insertQuery)) {
									write_logs("email-sent");
									$message = "Mesajul a fost trimis cu succes";
								} else {
									write_logs("email-sent-error");
									$message = "Eroare trimitere: " . mysqli_error($conn);
								}
							} else {
								$message = "Textul conține caractere invalide. *Permis: doar alfanumerice și semne de punctuație, fără ghilimele.";
							}
						} else {
							$message = "Selectează cel puțin un email";
						}
						echo '<span id="message" class="adm_message">' . $message . '</span>';
					}

					// form emails
					if (in_array($_SESSION['user_role'], [2, 3])) {
						$result = mysqli_query($conn, "SELECT email, data FROM emails");
						if ($result) {
							echo '<form class="adm_form" method="post" autocomplete="off" onsubmit="return validateCheckBox() && validateMessageDash();">';
							echo '<div class="table_dash">';
							echo '<table id="customers">';
							echo '<tr><th><input type="checkbox" id="select_all"> TOATE </th><th>Email</th><th>Data</th>';
							if ($_SESSION['user_role'] == 3) {
								echo '<th>Acțiuni</th>';
							}
							echo '</tr>';

							while ($row = mysqli_fetch_assoc($result)) {
								echo '<tr>';
								echo '<td><input type="checkbox" class="select-single" name="selected_emails[]" value="' . $row['email'] . '"></td>';
								echo '<td>' . $row['email'] . '</td>';
								echo '<td>' . $row['data'] . '</td>';
								if ($_SESSION['user_role'] == 3) {
									echo '<td class="action_styles">
                                            <a href="atmin/delete_row.php?email=' . $row['email'] . '" class="link">
                                                <img alt="Delete" title="Delete" src="img/delete.svg" width="18px" hspace="10">
                                            </a>
                                        </td>';
								}
								echo '</tr>';
							}
							echo '</table>';
							echo '</div>';
							echo '<span id="error" class="adm_message"></span>';
							echo '<input required type="text" placeholder="Scrie mesajul pentru abonați" name="text" id="text" 
								  title="*Mesajul poate conține doar caractere alfanumerice și semne de punctuație, fără ghilimele." pattern="^[a-zA-Z0-9\?\.\(\)\:\-,! ]{0,150}$">';
							echo '<input type="submit" name="submit" value="Trimite mesaj">';
							echo '</form>';

							mysqli_free_result($result);
						} else {
							echo 'Eroare la efectuarea interogării: ' . mysqli_error($conn);
						}
					}
					?>

					<hr>

					<!-- message table -->
					<?php
					if ($_SESSION['user_role'] == 3) {
						
						$query = "SELECT * FROM messages ORDER BY data DESC";
						$result = mysqli_query($conn, $query);
						
						
						if ($result) {
							echo '<div class="table_dash">';
							echo '<h3 class="users_display"> Mesaje trimise </h3>';
							echo '<table id="customers">';
							echo '<tr>';
							echo '<th>ID</th>';
							echo '<th>Email</th>';
							echo '<th>Mesaj</th>';
							echo '<th>Data</th>';
							echo '<th>Șterge</th>';
							echo '</tr>';

							while ($row = mysqli_fetch_assoc($result)) {
								echo '<tr>';
								echo '<td>' . $row['id_mail'] . '</td>';
								echo '<td>' . $row['email'] . '</td>';
								echo '<td>' . substr($row['message'], 0, 50) . '...</td>';
								echo '<td>' . $row['data'] . '</td>';
								if ($_SESSION['user_role'] == 3) {
									echo '<td class="action_styles">
                                            <a href="atmin/delete_row.php?message=' . $row['id_mail'] . '" class="link">
                                                <img alt="Delete" title="Delete" src="img/delete.svg" width="18px" hspace="10">
                                            </a>
                                        </td>';
								}
								echo '</tr>';
							}
							echo '</table>';
							echo '</div>';
							mysqli_free_result($result);
						} else {
							echo 'Eroare la efectuarea interogării: ' . mysqli_error($conn);
						}
					}
					mysqli_close($conn);
					?>

				</div> <!-- ./content_inner -->
			</div> <!-- ./container -->
		</div> <!-- ./table -->

		<!-- footer -->
		<?php include "parts/footer.php"; ?>
	</div> <!-- ./page-->

	<script src="js/script.js"></script>
</body>

</html>