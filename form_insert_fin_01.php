<?php
/*
 * ユーザーからのform情報取得とDBへのINSERT
 */

// HTTP responseヘッダを出力する可能性があるので、バッファリングしておく
ob_start();
// セッションの開始
session_start();

// common_function.phpの読み込み
require_once "common_function.php";

// postデータ取得
$user_input_data = get_postData();

// 確認
// var_dump($user_input_data);

// ユーザー入力のvalidate
// -------------------------------------
//

// 必須チェック
$error_detail = is_required($user_input_data);
// 型チェック
// 郵便番号
$error_detail += match_post($user_input_data);
// 誕生日
$error_detail += match_birthday($user_input_data);

// CSRFチェック
$error_detail += is_csrf_token("front");

// 確認
// var_dump($error_detail);

// エラーがある場合、入力ページに遷移する
if (!empty($error_detail)) {
    // エラー情報をセッションに入れる
    $_SESSION["output_buffer"] = $error_detail;
    // value保持の為に入力情報をセッションに入れる
    $_SESSION["output_buffer"] += $user_input_data;
    header("Location: ./form_insert_01.php");
    exit();
}

// DBハンドルの取得
$dbh = get_dbh();

// INSERT文の作成と発行
// ----------------------------
// プリペアドステートメントの用意
$sql =
    "INSERT INTO `test_form`(`name`, post, `address`, birthday, created, updated) VALUES(:name, :post, :address, :birthday, :created, :updated);";
$pre = $dbh->prepare($sql);

// 値のバインド
$pre->bindValue(":name", $user_input_data["name"], PDO::PARAM_STR);
$pre->bindValue(":post", $user_input_data["post"], PDO::PARAM_STR);
$pre->bindValue(":address", $user_input_data["address"], PDO::PARAM_STR);
// 誕生日はあらかじめ繋げておく
$birthday = "{$user_input_data["birthday_yy"]}-{$user_input_data["birthday_mm"]}-{$user_input_data["birthday_dd"]}";
$pre->bindValue(":birthday", $birthday, PDO::PARAM_STR);
$pre->bindValue(":created", date("Y-m-d h:i:s"), PDO::PARAM_STR);
$pre->bindValue(":updated", date("Y-m-d h:i:s"), PDO::PARAM_STR);

// SQLの実行。insertできない場合はfalseが返る
$r = $pre->execute();

if ($r === false) {
    echo "insertシステムでエラーが起きました";
    exit();
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DB中級講座</title>
</head>
<body>
    <h3>新規登録ありがとうございます！</h3>
    <a href="./admin_data_list.php"><button class="btn btn-default">戻る</button></a>
</body>
</html>