<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <script>
        // JavaScript function to fetch and display statuses
        function fetchStatuses(projectId) {
            const xhr = new XMLHttpRequest();
            xhr.open("GET", "fetch_statuses.php?project_id=" + projectId, true);
            xhr.onload = function() {
                if (xhr.status === 200) {
                    document.getElementById("statuses").innerHTML = xhr.responseText;
                } else {
                    document.getElementById("statuses").innerHTML = "Error fetching statuses.";
                }
            };
            xhr.send();
        }

        // JavaScript function to add a new status
        function addStatus() {
            const projectSelect = document.getElementById("projectSelect");
            const statusName = document.getElementById("statusName").value;

            const projectId = projectSelect.value;

            if (!projectId || !statusName) {
                document.getElementById("addStatusMessage").innerText = "Please select a project and enter a status name.";
                return;
            }

            const xhr = new XMLHttpRequest();
            xhr.open("POST", "add_status.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

            xhr.onload = function() {
                if (xhr.status === 200) {
                    document.getElementById("addStatusMessage").innerText = xhr.responseText;

                    // Reload statuses for the selected project
                    fetchStatuses(projectId);

                    // Clear the input field
                    document.getElementById("statusName").value = "";
                } else {
                    document.getElementById("addStatusMessage").innerText = "Error adding status.";
                }
            };

            xhr.send(`project_id=${projectId}&status_name=${encodeURIComponent(statusName)}`);
        }
    </script>
</head>

<body>
    <?php
    $db_pass = "zaq1@WSX";
    $connection_string = "host=localhost dbname=kanban user=postgres password=$db_pass";
    $polaczenie = pg_connect($connection_string);

    if (isset($_POST['login']) && isset($_POST['pass'])) {
        $login = $_POST['login'];
        $pass = $_POST['pass'];

        if (!$polaczenie) {
            die("Connection failed");
        }

        $query = pg_query(
            $polaczenie,
            "SELECT username, id FROM users WHERE password = '$pass' AND username = '$login'"
        );

        if (pg_num_rows($query) === 1) {
            $user = pg_fetch_assoc($query);
            $_SESSION['logowanie'] = $user['username'];
            $_SESSION['user_id'] = $user['id'];
            header("Location: index.php");
            exit;
        } else {
            echo "Błędny login lub hasło";
        }
    }

    if (isset($_SESSION['logowanie'])) {
        echo "Hello, " . $_SESSION['logowanie'] . "! Welcome to your dashboard.<br>";

        $user_id = $_SESSION['user_id'];

        // Display projects
        $projects_query = pg_query($polaczenie, "SELECT id, name FROM projects WHERE owner_id = $user_id");

        echo "<h2>Your Projects</h2>";
        echo "<table border='1'>";
        while ($project = pg_fetch_assoc($projects_query)) {
            echo "<tr>";
            echo "<td><a href='#' onclick='fetchStatuses(" . $project['id'] . ")'>" . htmlspecialchars($project['name']) . "</a></td>";
            echo "</tr>";
        }
        echo "</table>";

        // Section to display statuses
        echo "<h2>Project Statuses</h2>";
        echo "<div id='statuses'><p>Select a project to view its statuses.</p></div>";

        // Form to add statuses
        echo "<h2>Add a Status to a Project</h2>";
        echo '<form id="addStatusForm">
            <label for="projectSelect">Select Project:</label>
            <select id="projectSelect" name="project_id">';
        $projects_query = pg_query($polaczenie, "SELECT id, name FROM projects WHERE owner_id = $user_id");
        while ($project = pg_fetch_assoc($projects_query)) {
            echo '<option value="' . $project['id'] . '">' . htmlspecialchars($project['name']) . '</option>';
        }
        echo '</select>
            <br>
            <label for="statusName">Status Name:</label>
            <input type="text" id="statusName" name="status_name" required>
            <br>
            <button type="button" onclick="addStatus()">Add Status</button>
        </form>';
        echo '<div id="addStatusMessage"></div>';
    } else {
    ?>
        <form action="index.php" method="post">
            <input type="text" name="login" placeholder="Login">
            <input type="password" name="pass" placeholder="Password">
            <input type="submit" name="zaloguj" value="Log In">
        </form>
    <?php
    }
    ?>
</body>

</html>