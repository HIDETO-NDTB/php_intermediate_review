<?php

ob_start();
session_start();

require_once "./common_function.php";

// セッションにエラー情報のフラグが入っていたら取り出す
$view_data = [];
if (isset($_SESSION["output_buffer"]) === true) {
    $view_data = $_SESSION["output_buffer"];
}

// 二重に出力しないようにセッション内の情報を削除する
unset($_SESSION["output_buffer"]);

$id = (string) @$_GET["test_form_id"];
if ($id === "") {
    header("Location: ./admin_data_list.php");
    exit();
}

$dbh = get_dbh();
$sql = "SELECT * from test_form WHERE test_form_id = :test_form_id";

$pre = $dbh->prepare($sql);

// bind
$pre->bindValue(":test_form_id", $id, PDO::PARAM_INT);

$r = $pre->execute();
if ($r === false) {
    echo "修正用のデータ取得にエラーがありました";
    exit();
}

$data = $pre->fetch(PDO::FETCH_ASSOC);
if (empty($data)) {
    header("Location: ./admin_data_list.php");
    exit();
}

// 誕生日を年月日に分ける
$birthday_yy = date("Y", strtotime($data["birthday"]));
$birthday_mm = date("m", strtotime($data["birthday"]));
$birthday_dd = date("d", strtotime($data["birthday"]));

// 確認
// var_dump($data);
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
  <title>DB講座中級: 管理者画面修正イメージ</title>
  <style type="text/css">
    .error { color: red;}
  </style>
</head>
<body>
<div class="container">
  <h4>フォーム修正画面</h4>
  <form action="./admin_data_update_fin.php" method="POST">
  <table class="table table-hover">
    <tr>
      <th>ID</th>
      <td><?php echo h($data["test_form_id"]); ?></td>
    </tr>
    <tr>
      <th>NAME</th>
      <td><input type="text" class="form-control" name="name" value="<?php echo v(
          $view_data,
          $data,
          "name"
      ); ?>"></td>
      <?php if (
          isset($view_data["error_must_name"]) &&
          $view_data["error_must_name"] === true
      ): ?>
      <td><span class="error">名前の入力が空です</span></td>
      <?php endif; ?>
    </tr>
    <tr>
      <th>POST</th>
      <td><input type="text" class="form-control" name="post" value="<?php echo v(
          $view_data,
          $data,
          "post"
      ); ?>"></td>
      <?php if (
          isset($view_data["error_must_post"]) &&
          $view_data["error_must_post"] === true
      ): ?>
      <td><span class="error">郵便番号の入力が空です</span></td>
      <?php endif; ?>
    </tr>
    <tr>
      <th>ADDRESS</th>
      <td><input type="text" class="form-control" name="address" value="<?php echo v(
          $view_data,
          $data,
          "address"
      ); ?>"></td>
      <?php if (
          isset($view_data["error_must_address"]) &&
          $view_data["error_must_address"] === true
      ): ?>
      <td><span class="error">住所の入力が空です</span></td>
      <?php endif; ?>
    </tr>
    <tr>
      <th>BIRTHDAY</th>
      <td><input type="text" name="birthday_yy" value="<?php echo isset(
          $view_data["birthday_yy"]
      )
          ? h($view_data["birthday_yy"])
          : h(
              $birthday_yy
          ); ?>"> 年 <input type="text" name="birthday_mm" value="<?php echo isset(
    $view_data["birthday_mm"]
)
    ? h($view_data["birthday_mm"])
    : h(
        $birthday_mm
    ); ?>"> 月 <input type="text" name="birthday_dd" value="<?php echo isset(
    $view_data["birthday_dd"]
)
    ? h($view_data["birthday_dd"])
    : h($birthday_dd); ?>"> 日 </td>
      <?php if (
          (isset($view_data["error_must_birthday_yy"]) &&
              $view_data["error_must_birthday_yy"] === true) ||
          (isset($view_data["error_must_birthday_mm"]) &&
              $view_data["error_must_birthday_mm"] === true) ||
          (isset($view_data["error_must_birthday_dd"]) &&
              $view_data["error_must_birthday_dd"] === true)
      ): ?>
      <td><span class="error">誕生日の入力が空です</span></td>
      <?php endif; ?>
    </tr>
    <tr>
      <th>CREATED</th>
      <td><?php echo h($data["created"]); ?></td>
    </tr>
    <tr>
      <th>UPDATED</th>
      <td><?php echo h($data["updated"]); ?></td>
    </tr>
  </table>
  <input type="hidden" name="id" value="<?php echo h(
      $_GET["test_form_id"]
  ); ?>">
  <button type="submit" class="btn btn-default">修正</button>
  </form>
  </div>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
</body>
</html>