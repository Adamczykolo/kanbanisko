<?php
session_start();

$db_pass = "zaq1@WSX";
$connection_string = "host=localhost dbname=kanban user=postgres password=$db_pass";
$polaczenie = pg_connect($connection_string);

if (!$polaczenie) {
    die("Connection failed");
}

if (!isset($_SESSION['user_id'])) {
    die("Unauthorized access.");
}

if (isset($_POST['task_id']) && isset($_POST['assignee_id'])) {
    $task_id = (int)$_POST['task_id'];
    $assignee_id = (int)$_POST['assignee_id'];

    $query = pg_query_params(
        $polaczenie,
        "UPDATE tasks SET assignee_id = $1 WHERE id = $2",
        [$assignee_id, $task_id]
    );

    if ($query) {
        echo "Task assigned successfully.";
    } else {
        echo "Error assigning task: " . pg_last_error($polaczenie);
    }
} else {
    echo "Invalid input.";
}
