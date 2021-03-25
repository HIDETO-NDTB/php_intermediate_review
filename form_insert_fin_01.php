<?php

/*
 * ユーザーからのform情報取得とDBへのINSERT
 */

// HTTP responseヘッダを出力する可能性があるので、バッファリングしておく
ob_start();
// セッションの開始
session_start();

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

// エラーがある場合、入力ページに遷移する
if ($error_flg === true) {
    // エラー情報をセッションに入れる
    $_SESSION["output_buffer"] = $error_detail;
    $_SESSION["output_buffer"] += $user_input_data;
    header("Location: ./form_insert_01.php");
    exit();
}

// エラーがない場合にOKを出す（後に消す）
echo "OK";
