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
write_logs("view-edit_article");

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
	<link rel="stylesheet" href="css/style.css">
	<script src="https://kit.fontawesome.com/da9ff5ae16.js" crossorigin="anonymous"></script>
	<title> GUDNEWS ADMIN </title>
</head>

<body>
	<form class="edit_form" name="form_user" action="" method="post" enctype="multipart/form-data">
		<?php
		include "connect.php";

		$id = $_GET['id'];
		$query = "SELECT * FROM articles WHERE id='$id'";
		$sql = mysqli_query($conn, $query) or die(mysqli_error($conn));
		$row = mysqli_fetch_assoc($sql);

		$nume_imagine_old = $row['nume_imagine'];
		$categorie = trim($row['categorie']);
		$titlu = trim($row['titlu']);
		$description = trim($row['descrierea']);
		$continutu = trim($row['continutu']);
		$data_plasarii = trim($row['data_plasarii']);
		$sursa = trim($row['sursa']);
		$nr_vizionari = $row['nr_vizionari'];

		?>

		<div class="form_title">
			<a href="dash_article.php">
				<i class="fa-solid fa-reply-all"></i>
			</a>
			<p>Editare articol -> id:<?php echo $row['id']; ?></p>
		</div>
		<br>
		<p>Selectează imagine:</p>
		<input type="file" name="fileToUpload" id="fileToUpload">
		<br><br>
		<label for="category">Categorie</label>
		<input type="text" name="category" id="category" value="<?php echo htmlspecialchars($categorie); ?>">
		<br><br>
		<label for="title">Titlu</label>
		<textarea name="title" id="title"> <?php echo htmlspecialchars($titlu); ?> </textarea>
		<br><br>
		<label for="description">Descriere</label></td>
		<textarea name="description" id="description"> <?php echo htmlspecialchars($description); ?> </textarea>
		<br><br>
		<label for="continutu">Conținut</label></td>
		<textarea style="height: 50vh;" class="content" name="continutu"
			id="continutu"> <?php echo htmlspecialchars($continutu); ?> </textarea>
		<br><br>
		<label for="source">Sursa</label>
		<input type="text" name="source" id="source" value="<?php echo htmlspecialchars($sursa); ?>">
		<br><br>
		<label for="date">Data adăugare</label></td>
		<input type="date" name="date" id="date" value="<?php echo htmlspecialchars($data_plasarii); ?>">
		<br><br><br>
		<input type="submit" name="submit" value="Modifică">
		<?php

		if (isset($_POST['submit'])) {
			$category = trim($_POST['category']);
			$source = trim($_POST['source']);
			$title = trim($_POST['title']);
			$description = trim($_POST['description']);
			$continutu = trim($_POST['continutu']);
			$continutu = str_replace("'", "\'", $continutu);
			$continutu = str_replace('"', '\"', $continutu);
			$date = trim($_POST['date']);

			$nume_imagine_upload = basename($_FILES["fileToUpload"]["name"]);

			if (empty($nume_imagine_upload)) {
				$update_query = "UPDATE articles SET
										categorie = '$category',
										titlu = '$title',
										descrierea = '$description',
										continutu = '$continutu',
										data_plasarii = '$date',
										sursa = '$source'
										WHERE id = '$id'";

				if (mysqli_query($conn, $update_query))
					echo '<script class="message"> window.location.href = "edit_article.php?id=' . $id . '"; </script>';
				else
					echo '<b class="message"> Eroare la actualizarea înregistrării: ' . mysqli_error($conn) . '</b>';
			} else {
				$target_dir = "img/post/";
				$target_file = $target_dir . $nume_imagine_upload;

				if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
					echo "<b class=\"message\">Fișierul " . htmlspecialchars(basename($_FILES["fileToUpload"]["name"])) . " a fost încărcat.</b>";
					$update_query = "UPDATE articles SET
												nume_imagine = '$nume_imagine_upload',
												categorie = '$category',
												titlu = '$title',
												descrierea = '$description',
												continutu = '$continutu',
												data_plasarii = '$date',
												sursa = '$source'
												WHERE id = '$id'";

					if (mysqli_query($conn, $update_query))
						echo '<b class="message"> Modificare executată cu succes. </b>';
					else
						echo '<b class="message"> Eroare la actualizarea înregistrării: ' . mysqli_error($conn) . '</b>';
				} else
					echo "<b class=\"message\">Eroare la încarcarea fișierului</b>";
			}

		}
		mysqli_close($conn);
		?>
	</form>
</body>

</html>