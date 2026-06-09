<?php
require_once 'config/db.php';

$db = getDB();

// Fetch all tasks, newest first
$tasks = $db->query('SELECT * FROM tasks ORDER BY created_at DESC')->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Todo App</title>
    <style>
        body { font-family: sans-serif; max-width: 600px; margin: 40px auto; padding: 0 20px; }
        h1   { font-size: 1.5rem; }
        .task { display: flex; align-items: center; gap: 12px; padding: 10px 0; border-bottom: 1px solid #eee; }
        .task.done span { text-decoration: line-through; color: #aaa; }
        form.add-form { display: flex; gap: 8px; margin: 24px 0; }
        form.add-form input { flex: 1; padding: 8px 12px; border: 1px solid #ccc; border-radius: 4px; font-size: 1rem; }
        button { padding: 8px 16px; cursor: pointer; border-radius: 4px; border: none; }
        .btn-add    { background: #4f46e5; color: #fff; }
        .btn-done   { background: #f0fdf4; color: #166534; border: 1px solid #86efac; font-size: 0.8rem; }
        .btn-delete { background: #fef2f2; color: #991b1b; border: 1px solid #fca5a5; font-size: 0.8rem; }
    </style>
</head>
<body>

<h1>My Tasks</h1>

<!-- Add task form — POSTs to add.php -->
<form class="add-form" action="add.php" method="POST">
    <input type="text" name="title" placeholder="New task…" required autofocus>
    <button class="btn-add" type="submit">Add</button>
</form>

<!-- Task list -->
<?php foreach ($tasks as $task): ?>
    <div class="task <?= $task['is_done'] ? 'done' : '' ?>">

        <!-- Toggle done/undone -->
        <form action="delete.php" method="POST">
            <input type="hidden" name="action" value="toggle">
            <input type="hidden" name="id"     value="<?= $task['id'] ?>">
            <button class="btn-done" type="submit">
                <?= $task['is_done'] ? '↩ Undo' : '✓ Done' ?>
            </button>
        </form>

        <span><?= htmlspecialchars($task['title']) ?></span>

        <!-- Delete -->
        <form action="delete.php" method="POST" style="margin-left:auto">
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id"     value="<?= $task['id'] ?>">
            <button class="btn-delete" type="submit">✕</button>
        </form>

    </div>
<?php endforeach; ?>

</body>
</html>