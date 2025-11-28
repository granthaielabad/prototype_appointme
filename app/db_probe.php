<?php
// Adjust this line if your Database class path/name differs
require __DIR__ . '/app/Core/Database.php';

try {
    // Use YOUR app's Database class so we test the same config/env the app uses
    $db = (new \App\Core\Database())->getConnection();

    // Which DB did we connect to?
    $dbName = $db->query('SELECT DATABASE()')->fetchColumn();

    // Can we see the expected users table and a known account?
    $row = $db->query("SELECT email, password, is_active FROM tbl_users WHERE email='test@sample.com' LIMIT 1")->fetch(PDO::FETCH_ASSOC);
    var_dump([
        'connected_database' => $dbName,
        'test_user_row' => $row,
    ]);
} catch (Throwable $e) {
    echo "DB ERROR: " . $e->getMessage();
}
