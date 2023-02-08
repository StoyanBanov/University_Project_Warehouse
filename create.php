<?php
session_start();

if (empty($_SESSION["username"])) {
    header(("Location: index.php"));
}

require_once('DbHelper.php');
$conn = DbHelper::GetConnection();
$stm = $conn->prepare("SELECT * FROM categories");
$stm->execute();
$categories = $stm->fetchAll(PDO::FETCH_ASSOC);
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
    </header><br>

    <?php
    $errors = array();
    if (isset($_POST['btnSubmit'])) {
        $code = $_POST['code'];
        $stm = $conn->prepare("SELECT * FROM products WHERE code = '$code'");
        $stm->execute();
        $products = $stm->fetchAll(PDO::FETCH_ASSOC);
        if (count($products)) {
            $errors[] = "Кодът трябва да е уникален!";
        } 

        if (count($errors) == 0) {
            if (!empty($_FILES['image']['name'])) {
                $image = file_get_contents($_FILES['image']['tmp_name']);

                $stm = $conn->prepare('INSERT INTO products(name, description, priceBought, priceSell, count, categoryId, code, image) VALUES(?, ?, ?, ?, ?, ?, ?, ?)');
                $stm->execute(array(
                    $_POST['name'],
                    $_POST['description'],
                    $_POST['priceBought'],
                    $_POST['priceSell'],
                    $_POST['count'],
                    $_POST['category'],
                    $_POST['code'],
                    $image
                ));
            } else {
                $stm = $conn->prepare('INSERT INTO products(name, description, priceBought, priceSell, count, categoryId, code) VALUES(?, ?, ?, ?, ?, ?, ?)');
                $stm->execute(array(
                    $_POST['name'],
                    $_POST['description'],
                    $_POST['priceBought'],
                    $_POST['priceSell'],
                    $_POST['count'],
                    $_POST['category'],
                    $_POST['code']
                ));
            }

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
    <form method="post" enctype="multipart/form-data">
        <label for="name">Име: </label>
        <input type="text" name="name" maxlength="50" required /><br>

        <label for="description">Описание: </label>
        <textarea name="description" maxlength="2000"> </textarea><br>

        <label for="image">Файл: </label>
        <input type="file" name="image" accept="image/png, image/jpeg, image/png">/><br>

        <label for="priceBought">Цена на закупуване: </label>
        <input type="text" name="priceBought" required /><br>

        <label for="priceSell">Цена на продаване: </label>
        <input type="text" name="priceSell" required /><br>

        <label for="count">Брой: </label>
        <input type="number" name="count" min="0" required /><br>

        <label for="category">Категория: </label>
        <select name="category">
            <?php
            $i = 0;
            foreach ($categories as $c) {
            ?>
                <option value="<?php echo $c["id"] ?>"><?php echo $c["name"] ?></option>
            <?php
            }
            ?>
        </select><br>

        <label for="code">Код: </label>
        <input type="text" name="code" required />

        <br><input type="submit" name="btnSubmit" value="Създаване" />
    </form>
</body>

</html>