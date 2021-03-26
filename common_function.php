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
// tokenの作成とセッションへの設定
function create_csrf_token()
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
        echo "CSRFトーケンが作成できないので終了します";
        exit();
    }

    // CSRFトークンは5個まで
    while (count(@$_SESSION["front"]["csrf_token"]) >= 5) {
        array_shift($_SESSION["front"]["csrf_token"]);
    }

    // セッションに格納
    $_SESSION["front"]["csrf_token"][$csrf_token] = time();

    return $csrf_token;
}

// tokenのチェック
function is_csrf_token()
{
    // CSRFトークンを把握
    $post_csrf_token = (string) @$_POST["csrf_token"];

    // セッションの中に送られてきたトークンが存在しなければfalse
    if (isset($_SESSION["front"]["csrf_token"][$post_csrf_token]) === false) {
        return false;
    }

    // 寿命を把握
    $ttl = $_SESSION["front"]["csrf_token"][$post_csrf_token];
    // 先にトークンは削除
    unset($_SESSION["front"]["csrf_token"][$post_csrf_token]);
    // 寿命チェック（5分以内）
    if ($ttl + 300 <= time()) {
        return false;
    }

    // 全てのチェックOKだったのでtrueを返す
    return true;
}