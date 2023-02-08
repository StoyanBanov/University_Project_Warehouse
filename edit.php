<?php

session_start();

if (empty($_SESSION["username"])) {
    header(("Location: login.php"));
}

require_once('DbHelper.php');
$conn = DbHelper::GetConnection();
$stm = $conn->prepare("SELECT * FROM categories");
$stm->execute();
$categories = $stm->fetchAll(PDO::FETCH_ASSOC);

$product = null;
$products = array();
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stm2 = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stm2->execute(array($id));
    $products = $stm2->fetchAll(PDO::FETCH_ASSOC);

    if (count($products)) {
        $product = $products[0];
    }
}
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
    <?php
    $errors = array();
    if (isset($_POST['btnSubmit'])) {
        $code = $_POST['code'];
        $stm = $conn->prepare("SELECT * FROM products WHERE code = '$code' AND id <> $id");
        $stm->execute();
        $productsToCheck = $stm->fetchAll(PDO::FETCH_ASSOC);
        if (count($productsToCheck)) {
            $errors[] = "Кодът трябва да е уникален!";
        }

        if (count($errors) == 0) {
            if (!empty($_FILES['image']['name'])) {
                $image = file_get_contents($_FILES['image']['tmp_name']);

                $stm = $conn->prepare('UPDATE products SET name = ?, description = ?, priceBought = ?, priceSell = ?, count = ?, categoryId = ?, code = ?, image = ? WHERE id = ?');
                $stm->execute(array(
                    $_POST['name'],
                    $_POST['description'],
                    $_POST['priceBought'],
                    $_POST['priceSell'],
                    $_POST['count'],
                    $_POST['category'],
                    $_POST['code'],
                    $image,
                    $id
                ));
            }else{
                $stm = $conn->prepare('UPDATE products SET name = ?, description = ?, priceBought = ?, priceSell = ?, count = ?, categoryId = ?, code = ? WHERE id = ?');
                $stm->execute(array(
                    $_POST['name'],
                    $_POST['description'],
                    $_POST['priceBought'],
                    $_POST['priceSell'],
                    $_POST['count'],
                    $_POST['category'],
                    $_POST['code'],
                    $id
                ));
            }

            $params = isset($_SESSION["searchQuery"]) ? $_SESSION["searchQuery"] : "";
            header("Location:index.php".$params);
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
        <input type="text" name="name" value="<?php echo $product['name']?>" maxlength="50"/><br>

        <label for="description">Описание: </label>
        <textarea name="description" value="<?php echo $product['description']?>" maxlength="2000"> </textarea><br>

        <label for="image">Файл: </label>
        <?php echo '<img src="data:image;base64,' . base64_encode($product['image']) . '" alt="Image" style="width: 324px; height: 476px">' ?>
        <input type="file" name="image" accept="image/png, image/jpeg, image/png"/><br>

        <label for="priceBought">Цена на закупуване: </label>
        <input type="text" name="priceBought" value="<?php echo $product['priceBought'] ?>" /><br>

        <label for="priceSell">Цена на продаване: </label>
        <input type="text" name="priceSell" value="<?php echo $product['priceSell'] ?>" /><br>

        <label for="count">Брой: </label>
        <input type="number" name="count" value="<?php echo $product['count'] ?>" /><br>

        <label for="category">Категория: </label>
        <select name="category">
            <?php
            foreach ($categories as $c) {
                if ($product['categoryId'] == $c['id']) {
            ?>
                    <option value="<?php echo $c["id"] ?>" selected><?php echo $c["name"] ?></option>
                <?php
                } else {
                ?>
                    <option value="<?php echo $c["id"] ?>"><?php echo $c["name"] ?></option>
            <?php
                }
            }
            ?>
        </select><br>

        <label for="code">Код: </label>
        <input type="text" name="code" value="<?php echo $product['code'] ?>" />

        <br><input type="submit" name="btnSubmit" value="Редактиране" />
    </form>
    <a href="index.php<?= isset($_SESSION["searchQuery"]) ? $_SESSION["searchQuery"] : "" ?>">Обратно</a>
</body>

</html>