<?php

ob_start();

require_once "./common_function.php";

$id = (string) @$_GET["test_form_id"];
if ($id === "") {
    header("Location: ./admin_data_list");
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
      <td><input type="text" class="form-control" name="name" placeholder="Please input new name" value="<?php echo h(
          $data["name"]
      ); ?>"></td>
    </tr>
    <tr>
      <th>POST</th>
      <td><input type="text" class="form-control" name="post" placeholder="Please input new post" value="<?php echo h(
          $data["post"]
      ); ?>"></td>
    </tr>
    <tr>
      <th>ADDRESS</th>
      <td><input type="text" class="form-control" name="address" placeholder="Please input new address" value="<?php echo h(
          $data["address"]
      ); ?>"></td>
    </tr>
    <tr>
      <th>BIRTHDAY</th>
      <td><input type="text" name="birthday" value="<?php echo h(
          $data["birthday"]
      ); ?>"></td>
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