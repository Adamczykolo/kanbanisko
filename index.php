<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script src="script.js"></script>
</head>

<body>
</body>

</html>

<?php

$db_pass = "zaq1@WSX";
$connection_string = "host=localhost dbname=kanban user=postgres password=$db_pass";
$polaczenie = pg_connect($connection_string);

if (isset($_POST['login']) && isset($_POST['pass'])) {
    $login = $_POST['login'];
    $pass = $_POST['pass'];

    // PostgreSQL connection
    if (!$polaczenie) {
        die("Connection failed");
    }

    $query = pg_query(
        $polaczenie,
        "SELECT username, id FROM users WHERE password = '$pass' AND username = '$login'"
    );

    if (pg_num_rows($query) === 1) {
        $user = pg_fetch_assoc($query); // Fetch the user's data
        $_SESSION['logowanie'] = $user['username']; // Store username in session
        $_SESSION['user_id'] = $user['id'];         // Store user ID in session
        header("Location: index.php");             // Redirect to prevent resubmission
        exit;
    } else {
        echo "Błędny login lub hasło";
    }
}

// TU PISZEMY WSZYSTKO PO ZALOGOWANIU!!!!!
if (isset($_SESSION['logowanie'])) {
    echo "Hello, " . $_SESSION['logowanie'] . "! Welcome to your dashboard.<br>";
    $user_id = $_SESSION['user_id'];
    $projects_query = pg_query($polaczenie, "SELECT name FROM projects WHERE owner_id = $user_id");

    echo "<table border='1'>";


    while ($row = pg_fetch_assoc($projects_query)) {
        echo "<tr>";
        echo "<td>" . $row['name'] . "</td>";
        echo "</tr>";
    }

    echo "</table>";
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