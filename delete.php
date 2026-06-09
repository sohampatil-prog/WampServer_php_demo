<?php
require_once 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id     = (int) ($_POST['id'] ?? 0);   // cast to int — never trust user input
    $action = $_POST['action'] ?? '';
    $db     = getDB();

    if ($action === 'delete' && $id > 0) {
        $stmt = $db->prepare('DELETE FROM tasks WHERE id = :id');
        $stmt->execute([':id' => $id]);
    }

    if ($action === 'toggle' && $id > 0) {
        // Flip is_done: 0 → 1, 1 → 0
        $stmt = $db->prepare('UPDATE tasks SET is_done = 1 - is_done WHERE id = :id');
        $stmt->execute([':id' => $id]);
    }
}

header('Location: index.php');
exit;