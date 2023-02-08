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
        if (!isset($_POST['username']) || strlen($_POST['username']) < 5 || strlen($_POST['username']) > 15) {
            $errors[] = "Потребителкото име трябва да е между 5 и 15 символа!";
        } else {
            foreach (str_split($_POST['username']) as $character) {
                if (!((ord($character) >= 65 && ord($character) <= 90) || (ord($character) >= 97 && ord($character) <= 122) || $character == '_')) {
                    $errors[] = "Потребителкото име може да съдържа само малки и големи латински букви и долна черта!";
                    break;
                }
            }
        }
        $username = $_POST['username'];
        $stm = $conn->prepare("SELECT * FROM users WHERE username = '$username'");
        $stm->execute();
        $users = $stm->fetchAll(PDO::FETCH_ASSOC);
        if (count($users)) {
            $errors[] = "Потребителското име е заето!";
        } 

        if (!isset($_POST['password']) || strlen($_POST['password']) < 6 || strlen($_POST['password']) > 20 || !isset($_POST['passwordAgain']) || $_POST['passwordAgain'] != $_POST['password']) {
            $errors[] = "Невалидна парола!";
        }else{
            if ($_POST['password'] == strtolower($_POST['password']))
            {
                $errors[] = "Полето 'парола' трябва да съдържа поне една главна латинска буква!";
            }
            if ($_POST['password'] == strtoupper($_POST['password']))
            {
                $errors[] = "Полето 'парола' трябва да съдържа поне една малка латинска буква!";
            }
            if (str_contains($_POST['password'], '@') && str_contains($_POST['password'], '-') && str_contains($_POST['password'], '_')
                && str_contains($_POST['password'], '~') && str_contains($_POST['password'], '|'))
            {
                $errors[] = "Полето 'парола' трябва да съдържа поне един специален символ[@, -, _, ~, |]!";
            }
        }

        if (!isset($_POST['email'])) {
            $errors[] = "Не сте въвели имейл!";
        } else {
            if (!str_contains($_POST['email'], '.')) {
                $errors[] = "Невалиден имейл!";
            }
        }

        $email = $_POST['email'];
        $stm = $conn->prepare("SELECT * FROM users WHERE username = '$email'");
        $stm->execute();
        $users = $stm->fetchAll(PDO::FETCH_ASSOC);
        if (count($users)) {
            $errors[] = "Имейлът е зает!";
        } 

        if (!empty($_POST['phone'])) {
            foreach (str_split($_POST['phone']) as $character) {
                if ((ord($character) < 48 || ord($character) > 57) && $character != ' ' && $character != '-') {
                    $errors[] = "Полето 'телефон' може да съдържа само цифри, тирета или разстояния!";
                    break;
                }
            }
        }

        if (count($errors) == 0) {
            $stm = $conn->prepare('INSERT INTO users(username, password, email, phone) VALUES(?, ?, ?, ?)');
            $stm->execute(array(
                $_POST['username'],
                password_hash($_POST['password'], PASSWORD_DEFAULT),
                $_POST['email'],
                $_POST['phone']
            ));
            $_SESSION["username"] = $_POST["username"];

            header("Location: index.php");
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
        <input type="text" name="username" required/><br>

        <label for="email">Имейл: </label>
        <input type="email" name="email" required/><br>

        <label for="phone">Телефон: </label>
        <input type="text" name="phone" /><br>

        <label for="password">Парола: </label>
        <input type="password" name="password" required/><br>

        <label for="passwordAgain">Повторете паролата: </label>
        <input type="password" name="passwordAgain" required/><br>

        <input type="submit" name="btnSubmit" value="Регистрация" /><br>
    </form>
</body>

</html>