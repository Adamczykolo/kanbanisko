<?php
session_start();

if (isset($_POST['login']) && isset($_POST['pass'])) {
    $login = $_POST['login'];
    $pass = $_POST['pass'];

    $db_pass = "zaq1@WSX";

    // PostgreSQL connection
    $connection_string = "host=localhost dbname=kanban user=postgres password=$db_pass";
    $polaczenie = pg_connect($connection_string);
    if (!$polaczenie) {
        die("Connection failed");
    }

    $query = pg_query($polaczenie, 
        "SELECT username FROM users WHERE password = '$pass' AND username = '$login'"
    );
    


    if (pg_num_rows($query) === 1) {
        $_SESSION['logowanie'] = $login; // Store username in session
        header("Location: index.php");   // Redirect to prevent resubmission
        exit;
    } else {
        echo "Błędny login lub hasło";
    }
}

if (isset($_SESSION['logowanie'])) {
    echo "Hello, " . $_SESSION['logowanie'] . "! Welcome to your dashboard.";
    echo '<br><a href="index.php">Logout</a>'; // Add a logout link
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