<?php

// html入力時のエスケープ関数
function h($s)
{
    return htmlspecialchars($s, ENT_QUOTES, "UTF-8");
}

function h_digit($d)
{
    if ($d === 0) {
        return "";
    }
    return h((string) $d);
}
