<?php
    session_start();
    if(isset($_POST['login']) && isset($_POST['pass']) ){
        $login = $_POST['login'];
        $pass = $_POST['pass'];
        
        $polaczenie = new mysqli('localhost', username: 'root', '', 'users');

        $query = "SELECT login FROM uzytkownicy WHERE pass LIKE '$pass' AND login LIKE '$login'";
        
        
        $wynik = $polaczenie->query($query);
        if($wynik->num_rows==1){
            $_SESSION['logowanie']=$login;

        }else{
            echo "Błędny login lub hasło";
        }
    }

    

    if(isset($_SESSION['login'])){
        echo "jesteś zalogowany";
        
    }else {
        ?>
        <form action="logowaniebaza.php" method="post">
    <input type="text" name= "login">
    <input type="password" name= "pass">
    <input type="submit" name= "zaloguj">
    </form>
    <?php
    }

?>


