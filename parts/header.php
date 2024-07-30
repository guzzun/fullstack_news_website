<header id="header">
    <!-- header -->

    <div class="container">
        <div class="header_inner">
            <div class="header_nav">
                <div class="menu_header">
                    <i id="openMenuMobile" class="fas fa-bars"></i>
                </div>

                <div class="name">
                    <a href="index.php"> gudnews </a>
                </div>
            </div>

            <div class="login">
                <?php
                if (isset($_SESSION['user_id'])) {
                    echo '
                    <div class="login_block1">
                        <a href="' . ($_SESSION['user_role'] == 2 || $_SESSION['user_role'] == 3 ? 'dash.php' : 'index.php') . '"> 
                        <i class="fa-solid fa-user-gear"></i>              
                        </a>
                        <a href="logout.php"> <i class="fas fa-sign-out-alt"></i> </a>
                    </div>
                    ';
                } else {
                    echo '
                    <div class="login_block2">
                        <a href="login.php"> logare </a>
                        <a href="register.php"> înregistrare </a>
                    </div>                        
                    ';
                }
                ?>
            </div>


        </div> <!-- ./header inner -->
    </div>

</header> <!-- header end -->