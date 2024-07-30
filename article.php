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

write_logs("view-article");

// Verifică dacă parametrul 'id' este setat și este un număr valid
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Parametrul 'id' nu este valid.");
}

$id_article = intval($_GET['id']);

// Utilizarea de prepared statements pentru a preveni SQL Injection
$sql = "SELECT * FROM articles WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 'i', $id_article);
mysqli_stmt_execute($stmt);
$results = mysqli_stmt_get_result($stmt);
$results = mysqli_fetch_assoc($results);

// Incrementare număr de vizionări
$current_views = $results['nr_vizionari'] + 1;

$update_nr_views = "UPDATE articles SET nr_vizionari = ? WHERE id = ?";
$stmt_update = mysqli_prepare($conn, $update_nr_views);
mysqli_stmt_bind_param($stmt_update, 'ii', $current_views, $id_article);
mysqli_stmt_execute($stmt_update);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> </title>
    <script src="https://kit.fontawesome.com/da9ff5ae16.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" href="img/logo.svg">
</head>

<body>
    <!-- sidebar -->
    <?php include "parts/sidebar.php";
    include "parts/sidemenu.php";
    ?>

    <!-- page -->
    <div class="page">

        <!-- header -->
        <?php include "parts/header.php" ?>

        <!-- search bar -->
        <div class="container">
            <div class="search_bar">
                <form action="search.php" method="POST">
                    <input type="text" placeholder="Căutare..." name="search">
                    <button type="submit" name="submit"><i class="fa fa-search"></i></button>
                </form>
            </div>
        </div> <!-- ./search bar -->

        <!-- content -->
        <div class="content">
            <div class="container">
                <div class="content_inner">

                    <!-- news -->
                    <div class="news">
                        <div class="post">
                            <div class="block_1">
                                <h3> Știri </h3>
                            </div>

                            <!-- post -->
                            <div class="post_block">
                                <!-- post block -->
                                <div class="post_actions">
                                    <div class="post_back">
                                        <a href="index.php"> <i class="fa-solid fa-arrow-left-long"></i> </a>
                                    </div>

                                    <div class="post_share">
                                        <a href="#"> </a>
                                        <a href="#"> <i class="fas fa-share-alt"></i> </a>
                                    </div>
                                </div>

                                <!-- title -->
                                <h2 class="post_title post_title--open">
                                    <?php echo $results['titlu']; ?>
                                </h2>

                                <!-- post_footer -->
                                <div class="post_footer post_footer--open">
                                    <ul class="post_data">
                                        <li class="post_data-item">
                                            <time datetime="14-06-2021 12:12"> <?php echo $results['data_plasarii']; ?>
                                            </time>
                                        </li>
                                        <li class="post_data-item">
                                            <a href="#"> <?php echo $results['categorie']; ?> </a>
                                        </li>
                                    </ul>

                                    <div class="post_click">
                                        <div class="post_views">
                                            <i class="fas fa-eye"></i>
                                            <p> <?php echo $results['nr_vizionari']; ?> </p>
                                        </div>

                                        <div class="post_comment">
                                            <a href="#comment"><i class="fas fa-comments"></i></a>
                                        </div>
                                    </div>

                                </div> <!-- ./post_footer -->

                                <div class="post_img--open">
                                    <img src="<?php echo $results['nume_imagine']; ?>" alt="">
                                </div>

                                <div class="post_text">
                                    <?php echo filter($results['continutu']); ?>
                                </div>

                                <div class="post_source">
                                    <p>© Sursa: <a href="#">
                                            <?php echo $results['sursa']; ?>
                                        </a> </p>
                                </div>

                                <!-- comments -->
                                <div class="comment_header">
                                    <h4 id="comment" class="comment_title">
                                        <?php
                                        $comments_count = count_comments($conn, $id_article);
                                        echo "Comentarii <span>$comments_count</span>";
                                        ?>
                                    </h4>
                                    <?php
                                    $comment_error = '';

                                    // Verifică dacă formularul a fost trimis
                                    if (isset($_POST['submit'])) {
                                        $user_id = $_SESSION['user_id'];
                                        $comment_textarea = filter($_POST['comment_textarea']);

                                        if (empty($comment_textarea)) {
                                            $comment_error = 'Comentariul nu poate fi gol!';
                                        } elseif (!preg_match("/^[a-zA-Z0-9\?\.\:\-, ]{1,255}$/", $comment_textarea)) {
                                            $comment_error = 'Comentariul conține caractere nepermise sau depășește 255 de caractere. Permis: cifre/litere/(?.,:-)';
                                        } else {
                                            if (insert_comment($conn, $id_article, $user_id, $comment_textarea)) {
                                                echo '<script>window.location.href = "article.php?id=' . $id_article . '";</script>';
                                                write_logs("comment-added");
                                            } else {
                                                $comment_error = 'Comentariul nu a putut fi adăugat!';
                                            }
                                        }
                                    }
                                    ?>
                                    <p class="comment_error" id="comment_error">
                                        <?php echo $comment_error; ?>
                                    </p>
                                </div>

                                <?php
                                if (!isset($_SESSION['user_id'])) {
                                    echo '<p class="comment_restrict"><a href="login.php">Conectează-te,</a> pentru a comenta</p>';
                                }
                                ?>
                                <span id="error" class="message"></span>

                                <?php
                                $coments_results = get_comments($conn, $id_article);
                                if (isset($_SESSION['user_id'])) {
                                    echo '
                                    <form method="POST" class="comment_form" id="comment_form" onsubmit="return validateComment();">
                                        <textarea name="comment_textarea" class="comment_textarea" id="comment"
                                        placeholder="Lasă un comentariu" pattern="^[a-zA-Z0-9\?\.\:\-, ]{1,255}*$" maxlength="255"
                                        title="*Comentariul poate conține doar caractere(255) alfanumerice și semne de punctuație (?.,:-)."></textarea>                                       
                                        
                                        <span id="error" class="message"></span>
                                        
                                        <input class="comment_btn" type="submit" name="submit" id="submit_btn" value="Adaugă">                                        
                                    </form>
                                    ';
                                }
                                ?>

                                <div class="comment_block">
                                    <?php
                                    if (mysqli_num_rows($coments_results) > 0) {
                                        while ($row = mysqli_fetch_assoc($coments_results)) {
                                            echo '
                                            <div class="comment_block_inner">
                                                <h3>' . filter($row['user_name']) . '</h3>
                                                <p>' . filter($row['comment']) . '</p>
                                                <p class="comment_date">' . filter($row['data']) . '</p>
                                            </div>
                                            ';
                                        }
                                    } else {
                                        echo "<p class='no_comment_form'> Nu sunt comentarii </p>";
                                    }
                                    ?>
                                </div>

                            </div> <!-- ./post block -->

                        </div> <!-- ./post -->

                        <!-- right block -->
                        <?php include "parts/right_block.php" ?>

                    </div> <!-- ./news -->

                </div> <!-- ./content_inner -->

            </div> <!-- ./container -->
        </div> <!-- ./content end -->

        <!-- footer -->
        <?php include "parts/footer.php" ?>

    </div> <!-- ./page-->

    <!-- scroll up -->
    <div class="scroll_up_mobile">
        <i onclick="topFunction()" id="myBtn" class="fa-solid fa-arrow-up"></i>
    </div>

    <script src="js/script.js"></script>

</body>

</html>