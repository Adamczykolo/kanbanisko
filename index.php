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
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
        }

        .navbar {
            background-color: #333;
            color: white;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            position: fixed;
            top: 0;
            z-index: 1;
            height: 60px; /* Zwiększona wysokość nawigacji */
            box-sizing: border-box; /* Uwzględnianie paddingu */
        }

        .navbar .greeting {
            font-size: 18px;
            margin: 0;
            overflow: hidden; /* Ukrycie nadmiaru tekstu */
        }

        .navbar form {
            margin: 0;
        }

        .navbar button {
            background-color: #555;
            color: white;
            border: none;
            padding: 10px 15px; /* Zwiększone wymiary przycisku */
            cursor: pointer;
            font-size: 16px; /* Większy tekst */
            border-radius: 5px; /* Zaokrąglone rogi */
        }

        .navbar button:hover {
            background-color: #777;
        }

        .sidebar {
            width: 250px;
            background-color: #f4f4f4;
            height: 100vh;
            padding: 20px;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
            position: fixed;
            top: 60px; /* Dopasowane do wysokości nawigacji */
        }

        .sidebar h2 {
            margin-top: 0;
        }

        .sidebar ul {
            list-style: none;
            padding: 0;
        }

        .sidebar ul li {
            margin-bottom: 10px;
        }

        .sidebar ul li a {
            text-decoration: none;
            color: #333;
            font-size: 16px;
            display: block;
            padding: 5px;
            border-radius: 5px;
        }

        .sidebar ul li a:hover {
            background-color: #ddd;
        }

        .content {
            margin-left: 270px;
            padding: 20px;
            width: calc(100% - 270px);
            padding-top: 80px; /* Większy odstęp dla widoczności */
        }

        table {
            margin-top: 20px;
            border-collapse: collapse;
            width: 100%;
        }

        table, th, td {
            border: 1px solid black;
        }

        th, td {
            padding: 10px;
            text-align: left;
        }
    </style>
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
        ?>
        <div class="navbar">
            <div class="greeting">Hello, <?php echo htmlspecialchars($_SESSION['logowanie']); ?>!</div>
            <form method="POST" action="index.php">
                <input type="hidden" name="logout" value="1">
                <button type="submit">Log Out</button>
            </form>
        </div>
        <?php
    }

    if (isset($_SESSION['logowanie'])) {
        $user_id = $_SESSION['user_id'];
        $projects_query = pg_query($polaczenie, "SELECT name FROM projects WHERE owner_id = $user_id");

        ?>
        <div class="sidebar">
            <h2>Your Projects</h2>
            <ul>
                <?php
                while ($row = pg_fetch_assoc($projects_query)) {
                    echo '<li><a href="#">' . htmlspecialchars($row['name']) . '</a></li>';
                }
                ?>
            </ul>
        </div>
        <div class="content">
            <h2>Welcome to Your Dashboard</h2>
            <p>Here is an overview of your projects:</p>
            <?php
            $projects_query = pg_query($polaczenie, "SELECT name FROM projects WHERE owner_id = $user_id");

            echo "<table>";
            echo "<tr><th>Project Name</th></tr>";
            while ($row = pg_fetch_assoc($projects_query)) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                echo "</tr>";
            }
            echo "</table>";
            ?>
        </div>
        <?php
    } else {
        ?>
        <div class="content">
            <form action="index.php" method="post">
                <input type="text" name="login" placeholder="Login">
                <input type="password" name="pass" placeholder="Password">
                <input type="submit" name="zaloguj" value="Log In">
            </form>
        </div>
        <?php
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
        session_unset();
        session_destroy();
        header("Location: index.php");
        exit;
    }
    ?>
</body>

</html>