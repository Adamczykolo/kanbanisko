<?php
session_start();

$db_pass = "zaq1@WSX";
$connection_string = "host=localhost dbname=kanban user=postgres password=$db_pass";
$polaczenie = pg_connect($connection_string);

if (!$polaczenie) {
    die("Connection failed");
}

if (isset($_GET['task_id'])) {
    $task_id = (int)$_GET['task_id'];

    // Fetch task details, including the project ID
    $task_query = pg_query_params(
        $polaczenie,
        "SELECT 
            tasks.id, 
            tasks.title, 
            tasks.description, 
            tasks.project_id, 
            users.username AS assignee 
         FROM 
            tasks 
         LEFT JOIN 
            users 
         ON 
            tasks.assignee_id = users.id 
         WHERE 
            tasks.id = $1",
        [$task_id]
    );

    if ($task_query) {
        $task = pg_fetch_assoc($task_query);

        if ($task) {
            $project_id = $task['project_id'];

            // Fetch all users assigned to the project
            $users_query = pg_query_params(
                $polaczenie,
                "SELECT id, username 
                 FROM users 
                 WHERE id IN (
                     SELECT user_id 
                     FROM permissions 
                     WHERE project_id = $1
                 )",
                [$project_id]
            );

            if (!$users_query) {
                echo json_encode(["error" => "Error fetching users: " . pg_last_error($polaczenie)]);
                exit;
            }

            $users = [];
            while ($user = pg_fetch_assoc($users_query)) {
                $users[] = $user;
            }

            $task['users'] = $users;

            // Fetch task comments
            $comments_query = pg_query_params(
                $polaczenie,
                "SELECT content FROM comments WHERE task_id = $1",
                [$task_id]
            );

            $comments = [];
            while ($comment = pg_fetch_assoc($comments_query)) {
                $comments[] = $comment;
            }

            $task['comments'] = $comments;

            echo json_encode($task);
        } else {
            echo json_encode(["error" => "Task not found"]);
        }
    } else {
        echo json_encode(["error" => "Error fetching task: " . pg_last_error($polaczenie)]);
    }
} else {
    echo json_encode(["error" => "Invalid task ID"]);
}
