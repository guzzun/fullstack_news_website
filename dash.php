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
write_logs("view-dashboard");

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

// Verifică conexiunea
if ($conn->connect_error) {
	die("Conexiunea a eșuat: " . $conn->connect_error);
}

$articleCount = $conn->query("SELECT COUNT(*) as count FROM articles")->fetch_assoc()['count'];
$commentsCount = $conn->query("SELECT COUNT(*) as count FROM articles_comments")->fetch_assoc()['count'];
$sentMessagesCount = $conn->query("SELECT COUNT(*) as count FROM emails")->fetch_assoc()['count'];
$receivedNewsCount = $conn->query("SELECT COUNT(*) as count FROM send_news")->fetch_assoc()['count'];
$inboxCount = $conn->query("SELECT COUNT(*) as count FROM inbox")->fetch_assoc()['count'];
// $errorCount = $conn->query("SELECT COUNT(*) as count FROM erori")->fetch_assoc()['count'];
// $logCount = $conn->query("SELECT COUNT(*) as count FROM loguri")->fetch_assoc()['count'];

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<heap>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title> GUDNEWS </title>
	<script src="https://kit.fontawesome.com/da9ff5ae16.js" crossorigin="anonymous"></script>
	<link rel="stylesheet" href="css/style.css">
	<link rel="icon" href="img/logo.svg">
</heap>

<body>
	<!-- sidebar -->
	<?php include "parts/sidebar.php";
	include "parts/sidemenu_adm.php";
	?>

	<!-- page -->
	<div class="page">

		<!-- header -->
		<?php include "parts/header_adm.php" ?>

		<!-- content -->
		<div class="content">
			<div class="container">
				<div class="content_inner">

					<!-- dashboard -->
					<div class="dashboard">
						<!-- dashboard_inner -->
						<div class="dashboard_inner">
							<h2 class="dashboard_name">Articole <span>-></span></h2>
							<div class="dashboard_wrapper">
								<a href="dash_article.php" class="dashboard_item">
									<div class="dash_icon">
										<i class="fa-solid fa-pen-nib"></i>
									</div>
									<div class="dash_info">
										<h2 class="dash_title">Articole</h2>
										<div class="dash_count"><?php echo $articleCount; ?></div>
									</div>
								</a>
								<a href="dash_comment.php" class="dashboard_item">
									<div class="dash_icon">
										<i class="fa-regular fa-comments"></i>
									</div>
									<div class="dash_info">
										<h2 class="dash_title">Moderează comentarii</h2>
										<div class="dash_count"><?php echo $commentsCount; ?></div>
									</div>
								</a>
							</div>
							<h2 class="dashboard_name">Securitate <span>-></span></h2>
							<div class="dashboard_wrapper">
								<a href="dash_log.php" class="dashboard_item">
									<div class="dash_icon">
										<i class="fa-regular fa-keyboard"></i>
									</div>
									<div class="dash_info">
										<h2 class="dash_title">Loguri</h2>
										<div class="dash_count">10</div>
									</div>
								</a>
								<a href="dash_log.php" class="dashboard_item">
									<div class="dash_icon">
										<i class="fa-solid fa-bug"></i>
									</div>
									<div class="dash_info">
										<h2 class="dash_title">Erori</h2>
										<div class="dash_count">9</div>
									</div>
								</a>
							</div>
							<h2 class="dashboard_name">Social <span>-></span></h2>
							<div class="dashboard_wrapper">
								<a href="dash_email.php" class="dashboard_item">
									<div class="dash_icon">
										<i class="fa-regular fa-paper-plane"></i>
									</div>
									<div class="dash_info">
										<h2 class="dash_title">Trimite mesaj</h2>
										<div class="dash_count"><?php echo $sentMessagesCount; ?></div>
									</div>
								</a>
								<a href="dash_send.php" class="dashboard_item">
									<div class="dash_icon">
										<i class="fa-solid fa-envelope-open-text"></i>
									</div>
									<div class="dash_info">
										<h2 class="dash_title">Știri primite</h2>
										<div class="dash_count"><?php echo $receivedNewsCount; ?></div>
									</div>
								</a>
								<a href="dash_inbox.php" class="dashboard_item">
									<div class="dash_icon">
										<i class="fa-solid fa-inbox"></i>
									</div>
									<div class="dash_info">
										<h2 class="dash_title">Inbox</h2>
										<div class="dash_count"><?php echo $inboxCount; ?></div>
									</div>
								</a>
							</div>
						</div> <!-- ./dashboard_inner -->
					</div> <!-- ./dashboard -->

				</div> <!-- ./content_inner -->

			</div> <!-- ./container -->
		</div> <!-- ./content end -->

		<!-- footer -->
		<div>
			<?php include "parts/footer.php" ?>
		</div>

	</div> <!-- ./page-->

	<script src="js/script.js"></script>

</body>

</html>