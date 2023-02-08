<?php

session_start();

if (empty($_SESSION["username"])) {
    header(("Location: login.php"));
}
require_once('DbHelper.php');
$conn = DbHelper::GetConnection();

$params = isset($_SESSION["searchQuery"]) ? $_SESSION["searchQuery"] : "";
if (isset($_GET['id']) && isset($_POST["btnYes"])) {
    $id = $_GET['id'];
    $stm2 = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stm2->execute(array($id));

    header("Location: index.php".$params);
}
if (isset($_POST["btnNo"])) {
    header("Location: index.php".$params);
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
    <form method="post" style="padding:10px;display: flex; flex-direction: column; justify-content:space-around; align-items:center;width:80vw;margin-bottom:30px;">
        <div class="form-group">Избраният продукт ще бъде изтрит. Желаете ли да продължите?</div>
        <div class="form-group">
            <input type="submit" name="btnYes" value="да" class="btn btn-success" />
            <input type="submit" name="btnNo" value="не" class="btn btn-danger" />
        </div>
    </form>

</body>

</html>