<?php
// Отвечаем только на Ajax
if ($_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest') {return;}

// Можно передавать в скрипт разный action и в соответствии с ним выполнять разные действия.
//$action = "r"; //$_POST['action'];
//$url = "r"; //$_POST['url'];
//$offset = 0; //$_POST['offset'];

$action = $_POST['action'];
$url = $_POST['url'];
$offset = $_POST['offset'];

$step = 1;

include "serveImage.php";


if (empty($action)) return;
if (empty($url)) return;

if ($offset == 0) {
    include "init.php";
}

$imagesInitList = include "../../generated/data/imagesInitList.php";
$count = sizeof($imagesInitList);

serveImage ($imagesInitList[$offset]);

// Проверяем, все ли строки обработаны
$offset = $offset + $step;
if ($offset >= $count) {
    saveImageListToArray();
    $success = 1;
} else {
    $success = round($offset / $count, 4);
}

// И возвращаем клиенту данные (номер итерации и сообщение об окончании работы скрипта)
$output = Array('offset' => $offset, 'success' => $success);
echo json_encode($output);


