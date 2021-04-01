<?php

require_once "./common_function.php";

// dbに関する関数を共通化
// -------------------------------
// idから1件データを取得する

function get_db_data($id)
{
    $dbh = get_dbh();
    $sql = "SELECT * FROM test_form WHERE test_form_id = :test_form_id";

    $pre = $dbh->prepare($sql);

    // bind
    $pre->bindValue(":test_form_id", $id, PDO::PARAM_INT);

    $r = $pre->execute();
    if ($r === false) {
        echo "formデータの取得に失敗しました";
        exit();
    }

    $data = $pre->fetch(PDO::FETCH_ASSOC);

    if (empty($data)) {
        header("Location: ./admin_data_list.php");
        exit();
    }
    return $data;
}
