<?php
// セッションの開始
ob_start(); // セッションと合わせてバッファリングを開始すると便利になることがある
session_start();

// 共通関数の読み込み
require_once "common_function.php";

// var_dump($_SESSION); //セッションの中身を確認

// セッションにエラー情報のフラグが入っていたら取り出す
$view_data = [];
if (isset($_SESSION["output_buffer"]) === true) {
    $view_data = $_SESSION["output_buffer"];
}

// 二重に出力しないようにセッション内の情報を削除する
unset($_SESSION["output_buffer"]);

// CSRFトークンの取得
$csrf_token = create_csrf_token("front");
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>DB講座中級</title>
  <style type="text/css">
    .error { color: red;}
  </style>
</head>

<body>
<?php if (
    isset($view_data["error_csrf"]) &&
    $view_data["error_csrf"] === true
): ?>
  <span class="error">CSRFトークンでエラーが起きました。正しい遷移を5分以内に操作して下さい。<br></span>
  <?php endif; ?>
  <form action="./form_insert_fin_01.php" method="POST">
    <input type="hidden" name="csrf_token" value="<?php echo h(
        $csrf_token
    ); ?>">
  <?php if (
      isset($view_data["error_must_name"]) &&
      $view_data["error_must_name"] === true
  ): ?>
    <span class="error">名前が未入力です<br></span>
    <?php endif; ?>
    <!-- @があることでエラーがあっても無視される -->
  名前:<input type="text" name="name" value="<?php echo h(
      @$view_data["name"]
  ); ?>"><br>
  <?php if (
      isset($view_data["error_must_post"]) &&
      $view_data["error_must_post"] === true
  ): ?>
    <span class="error">郵便番号が未入力です<br></span>
    <?php endif; ?>
    <?php if (
        isset($view_data["error_format_post"]) &&
        $view_data["error_format_post"] === true
    ): ?>
    <span class="error">郵便番号の形式に誤りがあります<br></span>
    <?php endif; ?>
  郵便番号(例999-9999):<input type="text" name="post" value="<?php echo h(
      @$view_data["post"]
  ); ?>"><br>
  <?php if (
      isset($view_data["error_must_address"]) &&
      $view_data["error_must_address"] === true
  ): ?>
    <span class="error">住所が未入力です<br></span>
    <?php endif; ?>
  住所:<input type="text" name="address" value="<?php echo h(
      @$view_data["address"]
  ); ?>"><br>
  <?php if (
      isset($view_data["error_must_birthday_yy"]) &&
      $view_data["error_must_birthday_yy"] === true
  ): ?>
    <span class="error">誕生日(年)が未入力です<br></span>
    <?php endif; ?>
    <?php if (
        isset($view_data["error_must_birthday_mm"]) &&
        $view_data["error_must_birthday_mm"] === true
    ): ?>
    <span class="error">誕生日(月)が未入力です<br></span>
    <?php endif; ?>
    <?php if (
        isset($view_data["error_must_birthday_dd"]) &&
        $view_data["error_must_birthday_dd"] === true
    ): ?>
    <span class="error">誕生日(日)が未入力です<br></span>
    <?php endif; ?>
    <?php if (
        isset($view_data["error_format_birthday"]) &&
        $view_data["error_format_birthday"] === true
    ): ?>
    <span class="error">誕生日の形式に誤りがあります<br></span>
    <?php endif; ?>
  誕生日: 西暦<input type="text" name="birthday_yy" value="<?php echo h_digit(
      @$view_data["birthday_yy"]
  ); ?>">年<input type="text" name="birthday_mm" value="<?php echo h_digit(
    @$view_data["birthday_mm"]
); ?>">月<input type="text" name="birthday_dd" value="<?php echo h_digit(
    @$view_data["birthday_dd"]
); ?>">日<br>
  <br>
  <button type="submit">データ登録</button>
  </form>
</body>
</html>