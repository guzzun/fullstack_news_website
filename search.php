<?php
session_start();

if (!file_exists('utils.php')) {
    error_log("utils.php nu poate fi accesat.", 3, "error.txt");
    header('Location: /teza/fail.php');
    exit();
}

include_once "utils.php";
include_files(['logs.php', 'connect.php']);
write_logs("view-index");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> GUDNEWS </title>
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

        <?php
        include "parts/header.php";
        //=== PAGINATION ==============
        $total_records_per_page = 3;

        if (isset($_GET['page_no']) && $_GET['page_no'] != "") {
            $page_no = $_GET['page_no'];
        } else {
            $page_no = 1;
        }

        $result_count = mysqli_query($conn, "SELECT COUNT(*) AS total_records FROM articles");
        $total_records = mysqli_fetch_array($result_count);
        $total_records = $total_records['total_records'];

        $offset = ($page_no - 1) * $total_records_per_page;
        $previous_page = $page_no - 1;
        $next_page = $page_no + 1;
        $adjacents = "2";

        $total_no_of_pages = ceil($total_records / $total_records_per_page);
        $second_last = $total_no_of_pages - 1;
        ?>

        <!-- search bar -->
        <div class="container">
            <div class="search_bar">
                <form action="search.php" method="POST">
                    <input type="text" placeholder="Cautare..." name="search">
                    <button type="submit" name="submit"><i class="fa fa-search"></i></button>
                </form>
            </div>
        </div> <!-- ./search bar -->

        <!-- content -->
        <div class="content">
            <div class="container">
                <div class="content_inner">

                    <div class="content_header">

                        <!-- Send your news -->
                        <div class="news_nav">
                            <a href="#"> Cele mai recente </a>
                        </div>

                        <div class="send_news">
                            <p id="open_modal"> Expediați-ne o știre </p>
                            <i class="fas fa-envelope"></i>
                        </div>

                    </div> <!-- ./Send your news -->

                    <!-- send news modal -->
                    <dialog id="send_dialog">
                        <div class="dialog_wrapper">
                            <div class="close_area">
                                <i class="fas fa-times" id="close_modal"></i>
                            </div>
                            <div class="modal_header">
                                <p class="modal_title">Expediați-ne o <span>știre</span></p>
                                <p class="modal_subtitle">
                                    Ați aflat ceva interesant? Împărtășiți știrea cu toată
                                    comunitatea!
                                </p>
                            </div>
                            <form method="post" enctype="multipart/form-data">
                                <input type="text" name="description" placeholder="Descriere" required>
                                <input type="text" name="contact" placeholder="Contactele dvs." required>
                                <input type="file" name="fileToUpload" id="fileToUpload" multiple
                                    accept=".jpg, .jpeg, .png">
                                <button type="submit" class="modal_button">Trimite</button>
                                <span id="modal_msg"></span>
                            </form>
                        </div>
                    </dialog>

                    <!-- top_news -->
                    <div class="top_news">
                        <?php
                        $sql = "SELECT id, nume_imagine, titlu, data_plasarii, nr_vizionari
                        FROM articles
                        ORDER BY data_plasarii DESC
                        LIMIT 2";
                        $results = mysqli_query($conn, $sql) or die(mysqli_error($conn));

                        while ($row = mysqli_fetch_assoc($results))
                            echo '
                            <a href="article.php?id=' . $row['id'] . '" class="top_item">
                                <div class="top_img">
                                     <img src="' . $row['nume_imagine'] . '" alt="pixel5"> 
                                </div>
                    
                                <div class="top_text">
                                    <h2 class="title"> ' . $row['titlu'] . ' </h2>
                                    <div class="meta">
                                        <i class="fas fa-clock"></i>
                                        <p> ' . $row['data_plasarii'] . ' </p>
                                    </div>
                                </div>
                            </a> <!-- ./top item -->
                            ';
                        ?>
                    </div> <!-- ./top_news -->

                    <div class="news_category">
                        <!-- news category -->
                        <a onclick="sort_post_blocks_display_all()"> toate </a> <span> / </span>
                        <a onclick="sort_post_blocks('recenzii')"> recenzii </a> <span> / </span>
                        <a onclick="sort_post_blocks('filme')"> filme </a> <span> / </span>
                        <a onclick="sort_post_blocks('jocuri')"> jocuri </a> <span> / </span>
                        <a onclick="sort_post_blocks('tech')"> tech </a> <span> / </span>
                        <a onclick="sort_post_blocks('web')"> web </a>
                    </div> <!-- ./news category -->

                    <div class="news">
                        <!-- news -->
                        <div class="post">
                            <!-- post -->
                            <!-- block title -->
                            <div class="block_1">
                                <h3> Rezultatele cautate </h3>
                            </div>
                            <div id="no_blocks_message">
                                Nu există știri pentru această categorie
                            </div>

                            <?php
                            // Obține cuvântul de căutare din formular și aplică funcția filter
                            $key_word = filter($_POST['search']);

                            // Pregătește interogarea parametrizată
                            $stmt = $conn->prepare('SELECT * FROM articles WHERE titlu LIKE ?');
                            $search_param = '%' . $key_word . '%';
                            $stmt->bind_param('s', $search_param);

                            // Execută interogarea
                            $stmt->execute();

                            // Obține rezultatele
                            $search_results = $stmt->get_result();

                            // Verifică și afișează rezultatele
                            if ($search_results->num_rows > 0) {
                                while ($row = $search_results->fetch_assoc()) {
                                    echo '
                                    <div blockType="' . filter($row['categorie']) . '" class="post_block"> <!-- post block -->
                                        <div class="post_header">
                                            <div class="post_img">
                                                <a href="article.php?id=' . filter($row['id']) . '">
                                                    <img src="' . filter($row['nume_imagine']) . '" alt="' . filter($row['nume_imagine']) . '">
                                                </a>
                                            </div>
                                        </div>

                                        <div class="post_content">
                                            <h2 class="post_title">
                                                <a href="article.php?id=' . filter($row['id']) . '"> ' . filter($row['titlu']) . '. </a>
                                            </h2>
                                            <p class=".post_text">
                                                ' . filter($row['descrierea']) . '
                                            </p>
                                        </div>

                                        <div class="post_footer">
                                            <ul class="post_data">
                                                <li class="post_data-item">
                                                    <time datetime="' . filter($row['data_plasarii']) . '"> ' . filter($row['data_plasarii']) . ' </time>
                                                </li>
                                                <li class="post_data-item">
                                                    <a href="#">' . filter($row['categorie']) . '</a>
                                                </li>
                                            </ul>
                                            <div class="post_click">
                                                <div class="post_views">
                                                    <i class="fas fa-eye"></i>
                                                    <a href="#"> ' . filter($row['nr_vizionari']) . ' </a>
                                                </div>
                                                <div class="post_comment">
                                                    <i class="fas fa-comments"></i>
                                                    <a href="article.php?id=' . filter($row['id']) . '#comment"> comentarii </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div> <!-- ./post block -->
                                    ';
                                }
                            } else {
                                echo "<p class='msg'> Nu există articole care să conțină cuvântul: " . $key_word . " </p>";
                            }

                            // Închide declarația
                            $stmt->close();
                            ?>

                        </div> <!-- ./post -->

                        <!-- right_block -->
                        <?php include "parts/right_block.php"; ?>

                    </div> <!-- ./news -->

                </div> <!-- ./content_inner -->

                <ul class="pagenav">

                    <?php
                    if ($page_no > 1) {
                        echo "<li class='pagenav_item'><a class='pagenav_link' href='?page_no=1'><i class='fa-solid fa-backward-fast'></i></a></li>";
                    }
                    ?>

                    <li class="pagenav_item <?php if ($page_no <= 1) {
                        echo "disabled";
                    } ?>">
                        <a class="pagenav_link" <?php if ($page_no > 1) {
                            echo "href='?page_no=$previous_page'";
                        } ?>>&lt</a>
                    </li>

                    <?php
                    if ($total_no_of_pages <= 10) {
                        for ($counter = 1; $counter <= $total_no_of_pages; $counter++) {
                            if ($counter == $page_no) {
                                echo "<li class='active pagenav_item'><a class='pagenav_link'>$counter</a></li>";
                            } else {
                                echo "<li class='pagenav_item'><a class='pagenav_link' href='?page_no=$counter'>$counter</a></li>";
                            }
                        }
                    } elseif ($total_no_of_pages > 10) {

                        if ($page_no <= 4) {
                            for ($counter = 1; $counter < 8; $counter++) {
                                if ($counter == $page_no) {
                                    echo "<li class='active pagenav_item'><a class='pagenav_link'>$counter</a></li>";
                                } else {
                                    echo "<li class='pagenav_item'><a class='pagenav_link' href='?page_no=$counter'>$counter</a></li>";
                                }
                            }
                            echo "<li class='pagenav_item'><a class='pagenav_link'>...</a></li>";
                            echo "<li class='pagenav_item'><a class='pagenav_link' href='?page_no=$second_last'>$second_last</a></li>";
                            echo "<li class='pagenav_item'><a class='pagenav_link' href='?page_no=$total_no_of_pages'>$total_no_of_pages</a></li>";
                        } elseif ($page_no > 4 && $page_no < $total_no_of_pages - 4) {
                            echo "<li class='pagenav_item'><a class='pagenav_link' href='?page_no=1'>1</a></li>";
                            echo "<li class='pagenav_item'><a class='pagenav_link' href='?page_no=2'>2</a></li>";
                            echo "<li class='pagenav_item'><a class='pagenav_link'>...</a></li>";
                            for ($counter = $page_no - $adjacents; $counter <= $page_no + $adjacents; $counter++) {
                                if ($counter == $page_no) {
                                    echo "<li class='active pagenav_item'><a class='pagenav_link'>$counter</a></li>";
                                } else {
                                    echo "<li class='pagenav_item'><a class='pagenav_link' href='?page_no=$counter'>$counter</a></li>";
                                }
                            }
                            echo "<li class='pagenav_item'><a class='pagenav_link'>...</a></li>";
                            echo "<li class='pagenav_item'><a class='pagenav_link' href='?page_no=$second_last'>$second_last</a></li>";
                            echo "<li class='pagenav_item'><a class='pagenav_link' href='?page_no=$total_no_of_pages'>$total_no_of_pages</a></li>";
                        } else {
                            echo "<li pagenav_item><a class='pagenav_link' href='?page_no=1'>1</a></li>";
                            echo "<li class='pagenav_item'><a class='pagenav_link' href='?page_no=2'>2</a></li>";
                            echo "<li class='pagenav_item'><a class='pagenav_link'>...</a></li>";

                            for ($counter = $total_no_of_pages - 6; $counter <= $total_no_of_pages; $counter++) {
                                if ($counter == $page_no) {
                                    echo "<li class='active'><a class='pagenav_link'>$counter</a></li>";
                                } else {
                                    echo "<li class='pagenav_item'><a class='pagenav_link' href='?page_no=$counter'>$counter</a></li>";
                                }
                            }
                        }
                    }
                    ?>

                    <li class="pagenav_item <?php if ($page_no >= $total_no_of_pages) {
                        echo "disabled";
                    } ?>">
                        <a class="pagenav_link" <?php if ($page_no < $total_no_of_pages) {
                            echo "href='?page_no=$next_page'";
                        } ?>>&gt</a>
                    </li>
                    <?php if ($page_no < $total_no_of_pages) {
                        echo "<li class='pagenav_item'><a class='pagenav_link' href='?page_no=$total_no_of_pages'><i class='fa-solid fa-forward-fast'></i></a></li>";
                    } ?>
                </ul><!-- ./pagination -->

            </div> <!-- ./container -->
        </div> <!-- ./content end -->

        <!-- footer -->
        <?php include "parts/footer.php"; ?>

    </div> <!-- ./page-->

    <script src="js/script.js"></script>
    <script src="js/sort_post_blocks.js"></script>
</body>

</html>