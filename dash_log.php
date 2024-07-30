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
write_logs("view-dash_logs");

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

					<!-- logs table -->
					<?php
					if ($_SESSION['user_role'] == 3) {
						$log_file = 'logs.txt';

						if (file_exists($log_file)) {
							$log_entries = file($log_file, FILE_IGNORE_NEW_LINES);

							echo '<div class="table_dash">';
							echo '<h3 id="log_table" class="users_display"> Loguri</h3>';
							echo '<table id="customers">';
							echo '<tr>';
							echo '<th>Timp</th>';
							echo '<th>User ID</th>';
							echo '<th>Role ID</th>';
							echo '<th>Session ID</th>';
							echo '<th>Pagina</th>';
							echo '<th>Acțiuni</th>';
							echo '</tr>';

							foreach ($log_entries as $entry) {
								$log_data = explode(" | ", $entry);
								echo '<tr>';
								foreach ($log_data as $item) {
									echo '<td>' . htmlentities($item) . '</td>';
								}
								echo '</tr>';
							}
							echo '</table>';
						} else {
							echo 'Fișierul de log nu există.';
						}
						echo '</div>';
					}
					?>

					<hr>

					<?php
					// errors table
					if ($_SESSION['user_role'] == 3) {

						$log_file = 'error.txt';

						if (file_exists($log_file)) {
							$log_entries = file($log_file, FILE_IGNORE_NEW_LINES);

							
							echo '<div id="error_table" class="table_dash">';
							echo '<h3 class="users_display">  Erori </h3>';
							echo '<table id="customers">';
							echo '<tr>';
							echo '<th>Timp</th>';
							echo '<th>Pagina</th>';
							echo '<th>Error</th>';
							echo '</tr>';

							foreach ($log_entries as $entry) {
								$log_data = explode(" | ", $entry);
								echo '<tr>';
								foreach ($log_data as $item) {
									echo '<td>' . htmlentities($item) . '</td>';
								}
								echo '</tr>';
							}
							echo '</table>';
						} else {
							echo 'Fișierul de log nu există.';
						}
						echo '</div>';
					}
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