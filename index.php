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
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="styles.css" />
    <title>Document</title>
</head>

<body>
    <header>
        <a href="login.php">Вход</a>
        <a href="register.php">Регистрация</a>
        <a href="logout.php">Изход</a>
    </header>

    <?php
    $searchName;
    $searchCategory;
    $searchCode = isset($_GET['searchCode']) ? $_GET['searchCode'] : "";
    if (!empty($searchCode)) {
        $searchName = "";
        $searchCategory = 0;
    } else {
        $searchName = isset($_GET['searchName']) ? $_GET['searchName'] : "";
        $searchCategory = isset($_GET['searchCategory']) ? $_GET['searchCategory'] : 0;
    }

    $page = isset($_GET['page']) ? $_GET['page'] : 1;
    $_SESSION["searchQuery"] = '?searchName='.$searchName.'&searchCategory='.$searchCategory.'&searchCode='.$searchCode.'&page='.$page;

    $stm = $conn->query("SELECT * FROM products");
    $rows = $stm->fetchAll(PDO::FETCH_ASSOC);
    ?>

    <br>
    <form method="get">
        <label for="searchName">Име: </label>
        <input placeholder="Име" name="searchName" value="<?= $searchName ?>" />

        <label for="searchCategory">Категория: </label>
        <select name="searchCategory">
            <option value="0" selected>Всички</option>
            <?php
            $i = 1;
            foreach ($categories as $c) {
                if ($searchCategory == $c['id']) {
            ?>
                    <option value="<?php echo $_GET['searchCategory'] ?>" selected><?php echo $c["name"] ?></option>
                <?php
                } else {
                ?>
                    <option value="<?php echo $c["id"] ?>"><?php echo $c["name"] ?></option>
            <?php
                }
            }
            ?>
        </select>

        <label for="searchCode">Код: </label>
        <input placeholder="Код" name="searchCode" value="<?= $searchCode ?>" />

        <br><input type="submit" name="btnSubmit" value="Търсене" />
        <br><a href="index.php">Изчистване</a>
    </form>

    <br><a href="create.php">Добавяне на продукт</a>

    <?php
    $tempArr = array();

    if (!empty($_GET["searchCode"])) {
        foreach ($rows as $r) {
            if (str_contains($r["code"], $_GET["searchCode"])) {
                $tempArr[] = $r;
            }
        }
        $rows = $tempArr;
    } else {
        foreach ($rows as $r) {
            if ((empty($searchName) || str_contains($r["name"], $searchName)) &&
                ($searchCategory == 0 || $r["categoryId"] == $searchCategory)
            ) {
                $tempArr[] = $r;
            }
        }
        $rows = $tempArr;
    }
    if(count($rows) > 0){
    ?>

    <table>
        <tr>
            <th>Наименование</th>
            <th>Описание</th>
            <th>Файл</th>
            <th>Цена на закупуване</th>
            <th>Цена на продаване</th>
            <th>Брой</th>
            <th>Категория</th>
            <th>Код</th>
            <th></th>
        </tr>

        <?php
        $rowsForCurrentPage = array_slice($rows, (int)$page - 1, 2);
        foreach ($rowsForCurrentPage as $r) {
        ?>
            <tr>
                <td><?= $r["name"] ?></td>
                <td><?= $r["description"] ?></td>
                <td><?php echo '<img src="data:image;base64,' . base64_encode($r['image']) . '" alt="Image" style="width: 90px; height: 115px">' ?></td>
                <td><?= $r["priceBought"] ?></td>
                <td><?= $r["priceSell"] ?></td>
                <td><?= $r["count"] ?></td>
                <td><?php echo ($categories[$r["categoryId"] - 1]["name"]) ?></td>
                <td><?= $r["code"] ?></td>
                <td>
                    <a href="edit.php?id=<?= $r["id"] ?>">Edit</a>
                    <a href="delete.php?id=<?= $r["id"] ?>">Delete</a>
                </td>
            </tr>
        <?php
        }
        ?>
    </table><br>
    
    <?php
    }else{
    ?>
        <h3>Няма намерени продукти!</h3>
    <?php
    }

    ?>
    <div>
    <?php
    $totalPages = ceil(count($rows) / 2);
    for ($i = 0; $i < $totalPages; $i++) {
    ?>
        <a href='index.php?searchName=<?= $searchName ?>&page=<?= $i + 1 ?>&searchCategory=<?= $searchCategory ?>&searchCode=<?= $searchCode ?>'><?= '|'.($i + 1) ?></a>
    <?php
    }
    ?>
    </div>
</body>
</html>