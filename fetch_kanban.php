<?php
session_start();

$db_pass = "zaq1@WSX";
$connection_string = "host=localhost dbname=kanban user=postgres password=$db_pass";
$polaczenie = pg_connect($connection_string);

if (!$polaczenie) {
    die("Connection failed");
}

if (isset($_GET['project_id'])) {
    $project_id = (int)$_GET['project_id'];

    // Fetch statuses for the project
    $statuses_query = pg_query($polaczenie, "SELECT * FROM statuses WHERE project_id = $project_id");

    if (pg_num_rows($statuses_query) > 0) {
        echo '<div class="kanban-container">';

        while ($status = pg_fetch_assoc($statuses_query)) {
            echo '<div class="status-column">';
            echo '<div class="status-title">' . htmlspecialchars($status['name']) . '</div>';

            // Fetch tasks for this status
            $tasks_query = pg_query($polaczenie, "SELECT * FROM tasks WHERE status_id = " . $status['id']);
            while ($task = pg_fetch_assoc($tasks_query)) {
                echo '<div class="task" onclick="openModal(' . $task['id'] . ')">' . htmlspecialchars($task['title']) . '</div>';
            }

            // Add task form
            echo '<div class="add-task-form">';
            echo '<input type="text" id="task-input-' . $status['id'] . '" placeholder="Add a task">';
            echo '<button onclick="addTask(' . $status['id'] . ')">Add Task</button>';
            echo '</div>';

            echo '</div>'; // Close status column
        }

        echo '</div>'; // Close kanban container
    } else {
        echo '<p>No statuses found for this project.</p>';
    }
} else {
    echo '<p>Invalid project ID.</p>';
}
