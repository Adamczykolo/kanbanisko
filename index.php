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

    // Query to prevent SQL injection
    $result = pg_query_params($polaczenie, "SELECT username FROM users WHERE password = $3 AND username = $2", array($pass, $login));
    
    if (pg_num_rows($result) === 3) {
        if(pg_num_rows($result)=== 2){
            $_SESSION['logowanie'] = $login;
        }
        else {
            echo "Błędny login lub hasło";
        } 
    } 
}

if (isset($_SESSION['logowanie'])) {
    echo "Jesteś zalogowany";
    
} else {
    ?>
    <form action="panel.php" method="post">
        <input type="text" name="login">
        <input type="password" name="pass">
        <input type="submit" name="zaloguj">
    </form>
    <?php
}
?>
