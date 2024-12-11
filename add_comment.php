<?php
$connection = pg_connect("host=localhost dbname=kanban user=postgres password=zaq1@WSX");

$task_id = (int)$_POST['task_id'];
$comment = $_POST['comment'];

$query = pg_query_params($connection, "INSERT INTO comments (task_id, content) VALUES ($1, $2)", [$task_id, $comment]);

if ($query) {
    echo "Comment added successfully.";
} else {
    echo "Failed to add comment.";
}
