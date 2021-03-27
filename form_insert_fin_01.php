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

// ユーザー入力情報を保持する配列を準備する
$user_input_data = [];

// パラメーターの一覧を把握
$params = [
    "name",
    "post",
    "address",
    "birthday_yy",
    "birthday_mm",
    "birthday_dd",
];
// データを取得する
foreach ($params as $p) {
    $user_input_data[$p] = (string) @$_POST[$p];
}
// 確認
// var_dump($user_input_data);

// ユーザー入力のvalidate
// -------------------------------------
//
$error_flg = false;
$error_detail = []; // エラー情報詳細を入れる配列

// 必須チェック
$validate_params = [
    "name",
    "post",
    "address",
    "birthday_yy",
    "birthday_mm",
    "birthday_dd",
];
foreach ($validate_params as $p) {
    if ($user_input_data[$p] === "") {
        $error_detail["error_must_{$p}"] = true; // 名前未入力の場合のkey名はerror_must_nameとなる
        $error_flg = true;
    }
}

// 型チェック
// 郵便番号
if (preg_match("/\A[0-9]{3}[- ]?[0-9]{4}\z/", $user_input_data["post"]) !== 1) {
    $error_detail["error_format_post"] = true;
    $error_flg = true;
}
// 誕生日
// postされた誕生日を文字列から数値に変換
$int_params = ["birthday_yy", "birthday_mm", "birthday_dd"];
foreach ($int_params as $p) {
    $user_input_data[$p] = (int) $user_input_data[$p];
}
if (
    checkdate(
        $user_input_data["birthday_mm"],
        $user_input_data["birthday_dd"],
        $user_input_data["birthday_yy"]
    ) === false
) {
    $error_detail["error_format_birthday"] = true;
    $error_flg = true;
}

// 確認
// var_dump($error_flg);

// CSRFチェック
if (is_csrf_token() === false) {
    // CSRFエラーであることを配列に格納
    $error_detail["error_csrf"] = true;
    // エラーフラグを立てる
    $error_flg = true;
}

// エラーがある場合、入力ページに遷移する
if ($error_flg === true) {
    // エラー情報をセッションに入れる
    $_SESSION["output_buffer"] = $error_detail;
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

// SQLの実行
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
    新規登録ありがとうございます！
</body>
</html>