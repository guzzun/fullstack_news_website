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
write_logs("view-dash_article");

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
	<title> GUDNEWS ADMIN </title>
</head>

<body>
	<!-- sidebar -->
	<?php
	include "parts/sidebar.php";
	include "parts/sidemenu_adm.php";
	?>

	<!-- page -->
	<div class="page">
		<?php
		include "parts/header_adm.php";
		?>

		<!-- table -->
		<div id="content" class="content">
			<div class="container">
				<div class="content_inner">

					<?php
					// article table
					if ($_SESSION['user_role'] == 3) {
						
						$query = "SELECT * FROM articles ORDER BY data_plasarii DESC";
						$result = mysqli_query($conn, $query);
						
						
						if ($result) {
							echo '<div class="table_dash">';
							echo '<h3 class="users_display"> Articole </h3>';
							echo '<table id="customers">';
							echo '<a href="add_article.php" class="add_article_btn">Adaugă articol</a>';
							echo '<tr>';
							echo '<th>Acțiuni</th>';
							echo '<th>ID</th>';
							echo '<th>Img</th>';
							echo '<th>Categorie</th>';
							echo '<th>Titlu</th>';
							echo '<th>Descriere</th>';
							echo '<th>Conținut</th>';
							echo '<th>Sursa</th>';
							echo '<th>Data</th>';
							echo '<th>Vizualizări</th>';
							echo '</tr>';

							while ($row = mysqli_fetch_assoc($result)) {
								$dropdownId = 'dropdown_' . $row['id'];
								echo '<tr>';
								echo '
								<td class="table_actions">
									<div class="dropdown">
										<i onclick="toggleDropdown(\'' . $dropdownId . '\')" class="fa-solid fa-ellipsis dropbtn"></i>
										<div id="' . $dropdownId . '" class="dropdown-content">
											<a href="edit_article.php?id=' . $row['id'] . '" class="dropdown-item">
												<i class="fa-solid fa-pencil"></i>											
												<p>Editare</p>
											</a>
											<a href="atmin/delete_article.php?id=' . $row['id'] . '" class="dropdown-item">
												<i class="fa-regular fa-trash-can"></i>											
												<p>Șterge</p>
											</a>
										</div>
									</div>
								</td>';
								echo '<td>' . $row['id'] . '</td>';
								// echo '<td>' . $row['nume_imagine'] . '</td>';
								echo '<td>';
								if ($row['nume_imagine'] !== null) {
									echo '<img src="' . $row['nume_imagine'] . '" alt="fara_img" class="table_img" onclick="openDialog(\'' . $row['nume_imagine'] . '\')">';
								} else {
									echo 'fără imagine';
								}
								echo '</td>';
								echo '<td>' . $row['categorie'] . '</td>';
								echo '<td>' . substr($row['titlu'], 0, 50) . '...</td>';
								echo '<td>' . substr($row['descrierea'], 0, 50) . '...</td>';
								echo '<td>' . substr($row['continutu'], 0, 50) . '...</td>';
								echo '<td>' . $row['sursa'] . '</td>';
								echo '<td>' . $row['data_plasarii'] . '</td>';
								echo '<td>' . $row['nr_vizionari'] . '</td>';
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
		<?php include "parts/footer.php" ?>

	</div> <!-- ./page-->

	<script src="js/script.js"></script>
</body>

</html>