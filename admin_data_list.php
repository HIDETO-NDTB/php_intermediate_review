<?php

// 管理画面の一覧ページ
// ------------------------------

require_once "common_function.php";

$dbh = get_dbh();

$sql = "SELECT * from test_form";
$pre = $dbh->prepare($sql);

// プレースホルダがないので、bindはなし

$r = $pre->execute();
if ($r === false) {
    echo "formデータを一覧にする過程でエラーが起きました";
    exit();
}

$data = $pre->fetchAll(PDO::FETCH_ASSOC);

// 確認
// var_dump($data);
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>DB講座中級: 管理画面イメージ</title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
</head>

<body>
  <div class="container">
    <table class="table table-hover">
      <h4>フォーム内容一覧</h4>
      <thead>
        <tr>
          <th>ID</th>
          <th>NAME</th>
          <th>CREATED</th>
          <th>UPDATED</th>
        </tr>
      </thead>
    <tbody>
      <?php foreach ($data as $d): ?>
        <tr>
          <td><?php echo h($d["test_form_id"]); ?></td>
          <td><?php echo h($d["name"]); ?></td>
          <td><?php echo h($d["created"]); ?></td>
          <td><?php echo h($d["updated"]); ?></td>
          <!-- rawurldecodeはurl用のエスケープ関数 -->
          <td><a class="btn btn-default" href="./admin_data_detail.php?test_form_id=<?php echo rawurldecode(
              $d["test_form_id"]
          ); ?>">詳細</a></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
</body>
</html>
