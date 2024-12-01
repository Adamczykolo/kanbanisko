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

    $statuses_query = pg_query($polaczenie, "SELECT name FROM statuses WHERE project_id = $project_id");

    if (!$statuses_query) {
        echo "Error fetching statuses: " . pg_last_error($polaczenie);
        exit;
    }

    if (pg_num_rows($statuses_query) > 0) {
        while ($status = pg_fetch_assoc($statuses_query)) {
            echo "<p>" . htmlspecialchars($status['name']) . "</p>";
        }
    } else {
        echo "<p>No statuses found for this project.</p>";
    }
} else {
    echo "<p>Invalid project ID.</p>";
}
