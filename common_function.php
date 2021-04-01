<?php

// html入力時のエスケープ関数
function h($s)
{
    return htmlspecialchars($s, ENT_QUOTES, "UTF-8");
}

// 数値の0なら空文字を返す
function h_digit($d)
{
    if ($d === 0) {
        return "";
    }
    return h((string) $d);
}

// CSRF共通関数
// ------------------------------
// tokenの作成とセッションへの設定
function create_csrf_token($type)
{
    // CSRF用のtokenの作成と設定
    $csrf_token = "";
    try {
        // random_bytesはPHP7以降の関数だがPHP５.2以降で使えるユーザランド実装
        if (function_exists("random_bytes")) {
            $csrf_token = hash("sha512", random_bytes(128));
        } elseif (is_readable("/dev/urandom")) {
            $csrf_token = hash(
                "sha512",
                file_get_contents("/dev/urandom", false, null, 0, 128),
                false
            );
        } elseif (function_exists("openssl_random_pseudo_bytes")) {
            $csrf_token = hash("sha512", openssl_random_pseudo_bytes(128));
        }
    } catch (Exception $e) {
        // 後でまとめてエラーチェックするので、ここでは未処理
    }
    if ($csrf_token === "") {
        echo "CSRFトークンが作成できないので終了します";
        exit();
    }

    // CSRFトークンは5個まで
    if (isset($_SESSION[$type]["csrf_token"])) {
        while (count(@$_SESSION[$type]["csrf_token"]) >= 5) {
            array_shift($_SESSION[$type]["csrf_token"]);
        }
    }

    // セッションに格納
    $_SESSION[$type]["csrf_token"][$csrf_token] = time();

    return $csrf_token;
}

// tokenのチェック
function is_csrf_token($type)
{
    $error_detail = []; // エラー情報詳細を入れる配列

    // CSRFトークンを把握
    $post_csrf_token = (string) @$_POST["csrf_token"];

    // セッションの中に送られてきたトークンが存在しなければfalse
    if (isset($_SESSION[$type]["csrf_token"][$post_csrf_token]) === false) {
        $error_detail["error_csrf"] = true;
        return $error_detail;
    }

    // 寿命を把握
    $ttl = $_SESSION[$type]["csrf_token"][$post_csrf_token];
    // 先にトークンは削除
    unset($_SESSION[$type]["csrf_token"][$post_csrf_token]);
    // 寿命チェック（5分以内）
    if ($ttl + 300 <= time()) {
        $error_detail["error_csrf"] = true;
        return $error_detail;
    }

    // 全てのチェックOKだったのでtrueを返す
    return $error_detail;
}

// DB用関数
// ------------------------------

function get_dbh()
{
    $user = "root";
    $pass = "root";
    $dsn = "mysql:dbname=udemy_php_intermediate;host=localhost;charset=utf8mb4";

    // 接続オプションの設定
    $opt = [
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    // 「複文禁止」が可能なら付け足しておく
    if (defined("PDO::MYSQL_ATTR_MULTI_STATEMENTS")) {
        $opt[PDO::MYSQL_ATTR_MULTI_STATEMENTS] = false;
    }

    // 接続
    try {
        $dbh = new PDO($dsn, $user, $pass, $opt);
    } catch (PDOException $e) {
        echo "システムでエラーが起きました";
        exit();
    }
    return $dbh;
}

// postデータ取得関数
// ------------------------------

function get_postData()
{
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
    return $user_input_data;
}

// validation関数
// ------------------------------
// 必須チェック

function is_required($data)
{
    $error_detail = []; // エラー情報詳細を入れる配列
    $validate_params = [
        "name",
        "post",
        "address",
        "birthday_yy",
        "birthday_mm",
        "birthday_dd",
    ];
    foreach ($validate_params as $p) {
        if ($data[$p] === "") {
            $error_detail["error_must_{$p}"] = true; // 名前未入力の場合のkey名はerror_must_nameとなる
        }
    }
    return $error_detail;
}

// post型チェック
function match_post($data)
{
    $error_detail = []; // エラー情報詳細を入れる配列
    if (preg_match("/\A[0-9]{3}[- ]?[0-9]{4}\z/", $data["post"]) !== 1) {
        $error_detail["error_format_post"] = true;
    }
    return $error_detail;
}

// birthday型チェック
function match_birthday($data)
{
    $error_detail = []; // エラー情報詳細を入れる配列
    // postされた誕生日を文字列から数値に変換(checkdateを使う為)
    $int_params = ["birthday_yy", "birthday_mm", "birthday_dd"];
    foreach ($int_params as $p) {
        $data[$p] = (int) $data[$p];
    }
    if (
        // chackdate()は月日年の順
        checkdate(
            $data["birthday_mm"],
            $data["birthday_dd"],
            $data["birthday_yy"]
        ) === false
    ) {
        $error_detail["error_format_birthday"] = true;
    }
    return $error_detail;
}

// その他関数
// ------------------------------
// updateのvalue

function v($post_data, $db_data, $key)
{
    if (isset($post_data[$key])) {
        return $post_data[$key];
    } else {
        return $db_data[$key];
    }
}
