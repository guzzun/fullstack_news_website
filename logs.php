<?php
function write_logs($action)
{
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : '[-]';
    $role_id = isset($_SESSION['user_role']) ? $_SESSION['user_role'] : '[-]';
    $request_uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '[-]';
    $session_id = session_id() ? session_id() : 'none';

    $log_file = __DIR__ . '/logs.txt';

    // Verifică numărul de linii din fișier
    $line_count = count(file($log_file));

    // Resetează fișierul dupa 15 linii
    if ($line_count >= 10) {
        file_put_contents($log_file, '');
    }

    // $log_entry = "Time: " . date('Y/m/d H:i:s') . " | User ID: $user_id | Role ID: $role_id | Session ID: $session_id | Page: $request_uri | Action: $action " . PHP_EOL;
    $log_entry = date('Y/m/d H:i:s') . " | $user_id | $role_id | $session_id | $request_uri | $action " . PHP_EOL;

    // Deschide sau creează fișierul de log în modul append (adaugare la sfârșitul fișierului)
    if ($file_handle = fopen($log_file, 'a')) {
        // Scrie în fișier și închide fișierul
        fwrite($file_handle, $log_entry);
        fclose($file_handle);
    } else {
        echo 'Eroare la deschiderea fișierului de log.';
    }
}
