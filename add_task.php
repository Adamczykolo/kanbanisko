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

if (isset($_POST['status_id']) && isset($_POST['task_name'])) {
    $status_id = (int)$_POST['status_id'];
    $task_name = trim($_POST['task_name']);
    $project_id = (int)$_POST['project_id'];
    $assignee_id = $_SESSION['user_id']; // Assign current user

    if (empty($task_name)) {
        echo "Task name cannot be empty.";
        exit;
    }

    $query = pg_query_params(
        $polaczenie,
        "INSERT INTO tasks (title, status_id, project_id, assignee_id) VALUES ($1, $2, $3, $4)",
        [$task_name, $status_id, $project_id, $assignee_id]
    );

    if ($query) {
        echo "Task added successfully.";
    } else {
        echo "Error adding task: " . pg_last_error($polaczenie);
    }
} else {
    echo "Invalid input.";
}
