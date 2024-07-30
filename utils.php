<?php
// filter
function filter($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

// comments
// get_username_by_id
function get_username_by_id($conn, $user_id)
{
    $query = "SELECT login FROM users WHERE id_user = $user_id";
    $result = mysqli_query($conn, $query);

    if ($result) {
        $row = mysqli_fetch_assoc($result);
        return $row['login'];
    } else {
        return null;
    }
}

// insert_comment
function insert_comment($conn, $id_article, $user_id, $comment_textarea)
{
    $username = get_username_by_id($conn, $user_id); // Obținem numele de utilizator

    if ($username) {
        $query = "INSERT INTO articles_comments(id_article, user_name, comment)
                  VALUES($id_article, '$username', '$comment_textarea')";
        return mysqli_query($conn, $query);
    } else {
        return false;
    }
}

// count_comments
function count_comments($conn, $id_article)
{
    $count_comments_query = "SELECT COUNT(*) as count FROM articles_comments WHERE id_article = $id_article";
    $count_comments_result = mysqli_query($conn, $count_comments_query);
    if ($count_comments_result) {
        $count_row = mysqli_fetch_assoc($count_comments_result);
        return $count_row['count'];
    }
    return 0;
}

//get_comments
function get_comments($conn, $id_article)
{
    $select_coments = "SELECT comment, user_name, data FROM articles_comments WHERE id_article = $id_article";
    return mysqli_query($conn, $select_coments);
}

// include_files
function include_files($files, $action = 'include')
{
    global $conn, $path;
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $fieldsSeparator = " | ";

    try {
        foreach ($files as $file) {
            if (!file_exists($file)) {
                throw new Exception("File $file not found.");
            }

            if ($action === 'include') {
                include_once $file;
            } elseif ($action === 'require') {
                require_once $file;
            } else {
                throw new Exception("Acțiune invalidă specificată.");
            }
        }
    } catch (Exception $e) {
        $errorLogMessage = date("d/m/y H:i:s") . $fieldsSeparator . $_SERVER['PHP_SELF'] . $fieldsSeparator . $e->getMessage() . "\r\n";
        error_log($errorLogMessage, 3, "error.txt");
        header('Location: /teza/fail.php');
        exit;
    }
}

// SendNews Modal logic
// handleModalForm
function handleModalForm($conn)
{
    global $modal_msg;

    $description = isset($_POST['description']) ? filter($_POST['description']) : null;
    $contact = isset($_POST['contact']) ? filter($_POST['contact']) : null;

    $descriptionPattern = "/^[a-zA-Z0-9\?\.\:\-, ]{0,150}$/";
    $contactPattern = "/^[A-Za-z0-9.@ ]{1,25}$/";

    if (!preg_match($descriptionPattern, $description)) {
        $modal_msg = "Descrierea nu este validă.";
    } elseif (!preg_match($contactPattern, $contact)) {
        $modal_msg = "Contactele dvs. nu sunt valide.";
    } else {
        $fileUpload = handleFileUpload();
        if ($fileUpload['success']) {
            insertModalData($conn, $description, $contact, $fileUpload['filePath']);
            $modal_msg = "Datele au fost introduse cu succes.";
        } else {
            $modal_msg = $fileUpload['message'];
        }
    }
}

// handleFileUpload
function handleFileUpload()
{
    if (!isset($_FILES['fileToUpload']) || $_FILES['fileToUpload']['error'] == UPLOAD_ERR_NO_FILE) {
        // Dacă nu s-a încărcat niciun fișier
        return ['success' => true, 'filePath' => null];
    }

    $targetDir = "users_uploads/img/";
    $fileName = basename($_FILES["fileToUpload"]["name"]);
    $fileType = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    // Limiteaza lungimea numelui fișierului
    if (strlen($fileName) > 50) {
        return ['success' => false, 'message' => 'Numele fișierului este prea lung'];
    }

    // Limiteaza caracterele permise în numele fișierului
    if (!preg_match("/^[a-zA-Z0-9_\-\. ]{0,50}$/", $fileName)) {
        return ['success' => false, 'message' => 'Numele fișierului conține caractere nepermise'];
    }

    // Limiteaza dimensiunea fișierului (ex: 5MB)
    $maxFileSize = 5 * 1024 * 1024; // 5 MB
    if ($_FILES["fileToUpload"]["size"] > $maxFileSize) {
        return ['success' => false, 'message' => 'Dimensiunea fișierului depășește limita permisă de 5MB'];
    }

    $allowedTypes = ['jpg', 'jpeg', 'png'];
    if (!in_array($fileType, $allowedTypes)) {
        return ['success' => false, 'message' => 'Tipul de fișier nu este permis'];
    }

    // Generează un nume unic pentru fișier
    $newFileName = uniqid('file_', true) . '.' . $fileType;
    $targetFilePath = $targetDir . $newFileName;

    // Simulează scanarea fișierului cu un antivirus
    if (!scanFileWithAntivirus($_FILES["fileToUpload"]["tmp_name"])) {
        return ['success' => false, 'message' => 'Fișierul conține date malițioase.'];
    }

    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $targetFilePath)) {
        return ['success' => true, 'filePath' => $targetFilePath];
    } else {
        return ['success' => false, 'message' => 'A apărut o eroare la încărcarea fișierului'];
    }
}

// scanFile
function scanFileWithAntivirus($filePath)
{
    // Simulează scanarea fișierului cu un antivirus
    // Într-un scenariu real, aici ar trebui să fie integrat un serviciu de scanare antivirus, cum ar fi ClamAV
    return true; // Presupunem că fișierul este sigur
}

// insertModalData
function insertModalData($conn, $description, $contact, $filePath)
{
    global $modal_msg;

    $query = "INSERT INTO send_news (description, contact, file_path) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "sss", $description, $contact, $filePath);

    if (mysqli_stmt_execute($stmt)) {
        $modal_msg = "Datele au fost introduse cu succes";
    } else {
        $modal_msg = "Eroare la introducerea datelor: " . mysqli_error($conn);
    }

    mysqli_stmt_close($stmt);
}
