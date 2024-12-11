<?php
$connection = pg_connect("host=localhost dbname=kanban user=postgres password=zaq1@WSX");

$task_id = (int)$_POST['task_id'];
$description = $_POST['description'];

$query = pg_query_params($connection, "UPDATE tasks SET description = $1 WHERE id = $2", [$description, $task_id]);

if ($query) {
    echo "Task updated successfully.";
} else {
    echo "Failed to update task.";
}
