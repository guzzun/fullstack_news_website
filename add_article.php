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
write_logs("view-add_article");

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

$message = '';

if (isset($_POST['submit'])) {
	$nume_imagine = basename($_FILES["fileToUpload"]["name"]);
	$target_dir = "img/post/";
	$target_file = $target_dir . $nume_imagine;
	$uploadOk = 1;

	if (file_exists($target_file)) {
		$uploadOk = 0;
	}
	if ($uploadOk == 0) {
		// echo "<b class=\"edit_message\">Sorry, your file was not uploaded.</b>";
	} else {
		if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
			$message .= "Fișierul " . htmlspecialchars(basename($_FILES["fileToUpload"]["name"])) . " a fost încărcat /  ";
		} else {
			$message .= "Eroare la încarcarea fișierului";
		}
	}

	$category = $_POST['category'];
	$title = $_POST['title'];
	$description = $_POST['description'];
	$continutu = $_POST['continutu'];
	$continutu = str_replace("'", "\'", $continutu);
	$continutu = str_replace('"', '\"', $continutu);
	$date = $_POST['date'];
	$source = $_POST['source'];
	$nr_vizionari = 1;

	// Obținem ID-ul utilizatorului din sesiune
	$user_id = $_SESSION['user_id'];

	// Adăugăm articolul în baza de date, asigurându-ne că utilizatorul curent este asociat cu articolul
	$query = "INSERT INTO articles (nume_imagine, categorie, titlu, descrierea, continutu, data_plasarii, sursa, nr_vizionari)
              VALUES ('$target_file', '$category', '$title', '$description', '$continutu','$date', '$source', '$nr_vizionari');";

	if (mysqli_query($conn, $query)) {
		$message .= " Datele au fost introduse cu succes";
	} else {
		$message .= "Eroare la adăugarea datelor";
	}

	mysqli_close($conn);
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

					<form class="edit_form" name="form_user" method="post" action="" enctype="multipart/form-data">

						<div class="form_title">
							<div class="article_nav">
								<a href="dash_article.php">
									<i class="fa-solid fa-table"></i>
								</a>
								<p class="edit_message"><?php echo "$message"; ?></p>
							</div>
							<h3 class="users_display">Adăugare articol</h3>
						</div>

						<br>
						<label for="fileToUpload">Selectează imagine:</label>
						<input required type="file" name="fileToUpload" id="fileToUpload" accept=".jpg, .jpeg, .png">
						<br><br>
						<label for="category">Categorie</label>
						<input required type="text" name="category" id="category">
						<br><br>
						<label for="title">Titlu</label>
						<textarea name="title" id="title"></textarea>
						<br><br>
						<label for="description">Descriere</label></td>
						<textarea name="description" id="description"></textarea>
						<br><br>
						<label for="continutu">Conținut</label></td>
						<textarea name="continutu" id="continutu" class="textarea_height"></textarea>
						<br><br>
						<label for="source">Sursa</label>
						<input type="text" name="source" id="source">
						<br><br>
						<label for="date">Data adăugare</label></td>
						<input type="date" name="date" id="date">
						<br><br><br>
						<input type="submit" name="submit" value="Adaugă">
					</form>

				</div> <!-- ./content_inner -->
			</div> <!-- ./container -->
		</div> <!-- ./table -->

		<!-- footer -->
		<?php include "parts/footer.php" ?>

	</div> <!-- ./page-->

	<!-- scroll up -->
	<div class="scroll_up">
		<i onclick="topFunction()" id="myBtn" class="fa-solid fa-arrow-up"></i>
	</div>

	<script src="js/script.js"></script>
</body>

</html>