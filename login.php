<?php
session_start();

if (!empty($_SESSION["username"])) {
    header(("Location: index.php"));
}

require_once('DbHelper.php');
$conn = DbHelper::GetConnection();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <header>
        <a href="login.php">Вход</a>
        <a href="register.php">Регистрация</a>
        <a href="logout.php">Изход</a>
    </header>

    <?php
    $errors = array();
    if (isset($_POST['btnSubmit'])) {
        if (isset($_POST['username']) && isset($_POST['password'])) {
            $username = $_POST['username'];
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $stm = $conn->prepare("SELECT * FROM users WHERE username = '$username'");
            $stm->execute();
            $users = $stm->fetchAll(PDO::FETCH_ASSOC);
            if (count($users)) {
                if (password_verify($_POST['password'], $users[0]['password'])) {
                    $_SESSION["username"] = $users[0]["username"];
                    header("Location: index.php");
                }else {
                    $errors[] = "Грешна парола!";
                }
            } else {
                $errors[] = "Грешно потребителско име или парола!";
            }
        }
    }
    if (count($errors) > 0) {
    ?>
        <ul style="color: red;">
            <?php
            foreach ($errors as $e) {
                echo "<li>$e</li>";
            }
            ?>
        </ul>
    <?php
    }
    ?>

    <form method="post">
        <label for="username">Потребителско име: </label>
        <input type="text" name="username" required><br>

        <label for="password">Парола: </label>
        <input type="password" name="password" required><br>

        <input type="submit" name="btnSubmit" value="Вход" />
    </form>
</body>

</html>