<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>DB講座中級</title>
</head>
<body>
  <form action="./form_insert_fin.php" method="POST">
  名前:<input type="text" name="name" value=""><br>
  郵便番号(例999-9999):<input type="text" name="post" value=""><br>
  住所:<input type="text" name="adress" value=""><br>
  誕生日: 西暦<input type="text" name="birthday_yy" value="">年<input type="text" name="birthday_mm" value="">月<input type="text" name="birthday_dd" value="">日<br>
  <br>
  <button type="submit">データ登録</button>
  </form>
</body>
</html>