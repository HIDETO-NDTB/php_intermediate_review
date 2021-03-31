<?php

ob_start();
session_start();

require_once "./common_function.php";

// ユーザー入力情報を保持する配列
$user_input_data = get_postData();

$error_detail = []; // エラー詳細を入れる配列
// 必須チェック
$error_detail = is_required($user_input_data);
// post,birthdayの型チェック
$error_detail += match_post($user_input_data);
$error_detail += match_birthday($user_input_data);
// error_detailが空でなければ・・
if (!empty($error_detail)) {
    $_SESSION["output_buffer"] = $error_detail;
    $_SESSION["output_buffer"] += $user_input_data;
    $url = "./admin_data_update.php?test_form_id=" . rawurldecode($_POST["id"]);
    header("Location:" . $url);
    exit();
}

$dbh = get_dbh();
$sql =
    "UPDATE `test_form` SET `name`=:name, `post`=:post, `address`=:address, `birthday`=:birthday, `updated`=:updated WHERE test_form_id = :id";

$pre = $dbh->prepare($sql);

//bind
$pre->bindValue(":name", $user_input_data["name"]);
$pre->bindValue(":post", $user_input_data["post"]);
$pre->bindValue(":address", $user_input_data["address"]);
$birthday = "{$user_input_data["birthday_yy"]}-{$user_input_data["birthday_mm"]}-{$user_input_data["birthday_dd"]}";
$pre->bindValue(":birthday", $birthday);
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
