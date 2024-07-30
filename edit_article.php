<?php
session_start();

$message = '';

if (!file_exists('utils.php')) {
	error_log("utils.php nu poate fi accesat.", 3, "error.txt");
	$message = "utils.php nu poate fi accesat.";
	header('Location: /teza/fail.php');
	exit();
}

include_once "utils.php";
include_files(['logs.php', 'connect.php']);
write_logs("view-edit_article");

if (!isset($_SESSION['user_id'])) {
	header("Location: login.php");
	exit();
}

if ($_SESSION['user_role'] != 3) {
	header("Location: index.php");
	exit();
}

$id = $_GET['id'];
$query = "SELECT * FROM articles WHERE id='" . mysqli_real_escape_string($conn, $id) . "'";
$sql = mysqli_query($conn, $query);

if (!$sql) {
	$message = "Eroare la executarea interogării: " . mysqli_error($conn);
	exit();
}

$row = mysqli_fetch_assoc($sql);

$nume_imagine_old = $row['nume_imagine'];
$categorie = trim($row['categorie']);
$titlu = trim($row['titlu']);
$description = trim($row['descrierea']);
$continutu = trim($row['continutu']);
$data_plasarii = trim($row['data_plasarii']);
$source = trim($row['sursa']);
$nr_vizionari = $row['nr_vizionari'];

if (isset($_POST['submit'])) {
	$category = trim(mysqli_real_escape_string($conn, $_POST['category']));
	$source = trim(mysqli_real_escape_string($conn, $_POST['source']));
	$title = trim(mysqli_real_escape_string($conn, $_POST['title']));
	$description = trim(mysqli_real_escape_string($conn, $_POST['description']));
	$continutu = trim(mysqli_real_escape_string($conn, $_POST['continutu']));
	$date = trim(mysqli_real_escape_string($conn, $_POST['date']));

	$nume_imagine_upload = basename($_FILES["fileToUpload"]["name"]);

	if (empty($nume_imagine_upload)) {
		$update_query = "UPDATE articles SET
                            categorie = '$category',
                            titlu = '$title',
                            descrierea = '$description',
                            continutu = '$continutu',
                            data_plasarii = '$date',
                            sursa = '$source'
                         WHERE id = '" . mysqli_real_escape_string($conn, $id) . "'";

		if (mysqli_query($conn, $update_query)) {
			$message = "Modificare executată cu succes.";
		} else {
			$message = "Eroare la actualizarea înregistrării: " . mysqli_error($conn);
		}
	} else {
		$target_dir = "img/post/";
		$target_file = $target_dir . $nume_imagine_upload;

		if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
			$message = "Fișierul " . htmlspecialchars($nume_imagine_upload) . " a fost încărcat.";
			$update_query = "UPDATE articles SET
                                nume_imagine = '$target_file',
                                categorie = '$category',
                                titlu = '$title',
                                descrierea = '$description',
                                continutu = '$continutu',
                                data_plasarii = '$date',
                                sursa = '$source'
                             WHERE id = '" . mysqli_real_escape_string($conn, $id) . "'";

			if (mysqli_query($conn, $update_query)) {
				$message = "Modificare executată cu succes.";
			} else {
				$message = "Eroare la actualizarea înregistrării: " . mysqli_error($conn);
			}
		} else {
			$message = "Eroare la încarcarea fișierului.";
		}
	}
}
mysqli_close($conn);
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

					<form class="edit_form" name="form_user" action="" method="post" enctype="multipart/form-data">

						<div class="form_title">
							<div class="article_nav">
								<a href="dash_article.php">
									<i class="fa-solid fa-table"></i>
								</a>
								<p class="edit_message"><?php echo "$message"; ?></p>
							</div>
							<h3 class="users_display edit_title">Editare articol - <span> <?php echo $row['id']; ?>
								</span>
							</h3>
						</div>

						<br>
						<label for="fileToUpload">Selectează imagine:</label>
						<input type="file" name="fileToUpload" id="fileToUpload" accept=".jpg, .jpeg, .png">
						<br><br>
						<label for="category">Categorie</label>
						<input type="text" name="category" id="category"
							value="<?php echo htmlspecialchars($categorie); ?>">
						<br><br>
						<label for="title">Titlu</label>
						<textarea name="title" id="title"> <?php echo htmlspecialchars($titlu); ?> </textarea>
						<br><br>
						<label for="description">Descriere</label></td>
						<textarea name="description"
							id="description"> <?php echo htmlspecialchars($description); ?> </textarea>
						<br><br>
						<label for="continutu">Conținut</label></td>
						<textarea class="content" name="continutu"
							id="continutu"> <?php echo htmlspecialchars($continutu); ?> </textarea>
						<br><br>
						<label for="source">Sursa</label>
						<input type="text" name="source" id="source" value="<?php echo htmlspecialchars($source); ?>">
						<br><br>
						<label for="date">Data adăugare</label></td>
						<input type="date" name="date" id="date"
							value="<?php echo htmlspecialchars($data_plasarii); ?>">
						<br><br><br>
						<input type="submit" name="submit" value="Modifică">

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