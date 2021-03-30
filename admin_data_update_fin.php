<?php

require_once "./common_function.php";

// postデータのvalidate(共通関数として切り分ける)
// 未入力チェック
// postとbirthdayの型チェック

// エラーフラグがfalseなら・・

$dbh = get_dbh();
$sql =
    "UPDATE `test_form` SET `name`=:name, `post`=:post, `address`=:address, `birthday`=:birthday, `updated`=:updated WHERE test_form_id = :id";

$pre = $dbh->prepare($sql);

//bind
$pre->bindValue(":name", $_POST["name"]);
$pre->bindValue(":post", $_POST["post"]);
$pre->bindValue(":address", $_POST["address"]);
$pre->bindValue(":birthday", $_POST["birthday"]);
$pre->bindValue(":updated", date("Y-m-d h:i:s"));
$pre->bindValue(":id", $_POST["id"]);

$r = $pre->execute();

if ($r === false) {
    echo "データ修正時にエラーが発生しました";
    exit();
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DB講座中級: 修正完了ページ</title>
</head>
<body>
    <h3>データ修正に成功しました！</h3>
    <a href="./admin_data_list.php"><button class="btn btn-default">リストへ戻る</button></a>
</body>
</html>
