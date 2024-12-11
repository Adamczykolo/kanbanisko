<?php
session_start();

$db_pass = "zaq1@WSX";
$connection_string = "host=localhost dbname=kanban user=postgres password=$db_pass";
$polaczenie = pg_connect($connection_string);

if (!$polaczenie) {
    die("Database connection failed.");
}

if (!isset($_SESSION['user_id'])) {
    die("Unauthorized access.");
}

if (isset($_POST['project_name']) && isset($_POST['project_description'])) {
    $project_name = trim($_POST['project_name']);
    $project_description = trim($_POST['project_description']);
    $user_id = $_SESSION['user_id']; // Creator's user ID

    if (empty($project_name)) {
        echo "Project name cannot be empty.";
        exit;
    }

    // Insert project into the `projects` table
    $project_query = pg_query_params(
        $polaczenie,
        "INSERT INTO projects (name, description, owner_id) VALUES ($1, $2, $3) RETURNING id",
        [$project_name, $project_description, $user_id]
    );

    if ($project_query) {
        $project = pg_fetch_assoc($project_query);
        $project_id = $project['id'];

        // Add the creator to the `permissions` table as an admin
        $permissions_query = pg_query_params(
            $polaczenie,
            "INSERT INTO permissions (user_id, project_id, role) VALUES ($1, $2, 'admin')",
            [$user_id, $project_id]
        );

        if ($permissions_query) {
            echo "Project added successfully.";
        } else {
            echo "Error adding permissions: " . pg_last_error($polaczenie);
        }
    } else {
        echo "Error adding project: " . pg_last_error($polaczenie);
    }
} else {
    echo "Invalid input.";
}
