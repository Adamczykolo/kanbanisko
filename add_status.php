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

if (isset($_POST['project_id']) && isset($_POST['status_name'])) {
    $project_id = (int)$_POST['project_id'];
    $status_name = trim($_POST['status_name']);

    if (empty($status_name)) {
        echo "Status name cannot be empty.";
        exit;
    }

    $query = pg_query_params(
        $polaczenie,
        "INSERT INTO statuses (project_id, name) VALUES ($1, $2)",
        [$project_id, $status_name]
    );

    if ($query) {
        echo "Status added successfully.";
    } else {
        echo "Error adding status: " . pg_last_error($polaczenie);
    }
} else {
    echo "Invalid input.";
}
