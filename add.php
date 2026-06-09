<?php
require_once 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');

    if ($title !== '') {
        $db   = getDB();
        $stmt = $db->prepare('INSERT INTO tasks (title) VALUES (:title)');
        $stmt->execute([':title' => $title]);
        // :title is a named placeholder — PDO escapes it, SQL injection is impossible
    }
}

// Redirect back — this prevents form resubmission on browser refresh (PRG pattern)
header('Location: index.php');
exit;