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
write_logs("view-dash_send");

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

					<!-- message table -->
					<?php
					if ($_SESSION['user_role'] == 3) {
						
						$query = "SELECT * FROM send_news ORDER BY created_at DESC";
						$result = mysqli_query($conn, $query);
						
						if ($result) {
							echo '<div class="table_dash">';
							echo '<h3 class="users_display">Știri primite</h3>';
							echo '<table id="customers">';
							echo '<tr>';
							echo '<th>ID</th>';
							echo '<th>Știre</th>';
							echo '<th>Contacte</th>';
							echo '<th>Imagine</th>';
							echo '<th>Data</th>';
							echo '<th>Șterge</th>';
							echo '</tr>';
							while ($row = mysqli_fetch_assoc($result)) {
								echo '<tr>';
								echo '<td>' . $row['id'] . '</td>';
								echo '<td>' . substr($row['description'], 0, 40) . '...</td>';
								echo '<td>' . $row['contact'] . '</td>';
								echo '<td>';
								if ($row['file_path'] !== null) {
									echo '<img src="' . $row['file_path'] . '" alt="fara_img" class="table_img" onclick="openDialog(\'' . $row['file_path'] . '\')">';
								} else {
									echo 'fără imagine';
								}
								echo '</td>';
								echo '<td>' . $row['created_at'] . '</td>';
								echo '<td class="action_styles">
										<a href="atmin/delete_row.php?news=' . $row['id'] . '" class="link">
										  <img alt="Delete" title="Delete" src="img/delete.svg" width="18px" hspace="10">
										</a>
									  </td>';
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
					
					<!-- zoom image -->
					<dialog id="imageDialog">
						<img id="dialogImage" src="" alt="fara_img">
					</dialog>

				</div> <!-- ./content_inner -->
			</div> <!-- ./container -->
		</div> <!-- ./table -->

		<!-- footer -->
		<?php include "parts/footer.php"; ?>
	</div> <!-- ./page-->

	<script src="js/script.js"></script>
</body>

</html>