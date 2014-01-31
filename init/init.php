<?php

use Octopod\Octophp\Facades\App;
use Octopod\Octophp\Facades\Config;


if (App::path('clientAppPath') != null) {
    $generatedPath = App::path('dir') . App::path('clientAppPath') . Config::get('paths.generated') . '/';
    $resourcesPath = App::path('dir') . App::path('clientAppPath') . Config::get('paths.resources') . '/';
} else {
    $generatedPath = App::path('app') . Config::get('paths.generated') . '/'; // todo: ololo
    $resourcesPath = App::path('app') . Config::get('paths.resources') . '/'; // todo: ololo
}

rrmdir($generatedPath);

createFolderIfNotExists($generatedPath);
createFolderIfNotExists($generatedPath . 'data');
createFolderIfNotExists($generatedPath . 'images');

$scaleScreen = Config::get("scaleScreen");
$screen = Config::get('screen');

$imageTodo = array();

$scaleImagesList = filesToArray($resourcesPath . 'images/' . $scaleScreen);

if (count($screen)) {
    foreach ($screen as $screenId => $dims) {

        $imagesList = filesToArray($resourcesPath . 'images/' . $screenId);

        if (count($imagesList))
            foreach ($imagesList as $imageInfo) {
                $imageTodo[] = array(
                    "command" => "copy",
                    "sourcePath" => $imageInfo['fullPath'],
                    "destPath" => $generatedPath . 'images/' . $screenId . $imageInfo['localPath'],
                    "screenId" => $screenId,
                    "imageKey" => $imageInfo['localPath']);
            }

        if ($screenId != $scaleScreen) {
            $divider = $screen[$scaleScreen]["optimalWidth"] / $dims["optimalWidth"];

            foreach ($scaleImagesList as $scaleImageInfo) {
                if (!file_exists($resourcesPath . 'images/' . $screenId . $scaleImageInfo['localPath'])) {
                    $imageTodo[] = array(
                        "command" => "convert",
                        "sourcePath" => $scaleImageInfo['fullPath'],
                        "destPath" => $generatedPath . 'images/' . $screenId . $scaleImageInfo['localPath'],
                        "screenId" => $screenId,
                        "imageKey" => $scaleImageInfo['localPath'],
                        "divider" => $divider
                    );
                }
            }
        }
    }
}

//file_put_contents('/home/www/ilich/octopod/new/php_apps/techrent/generated/data/fonts.php', '<?php return ' . var_export($fontSizes, true) . ';');
file_put_contents($generatedPath . 'data/imagesInitList.php', '<?php return ' . var_export($imageTodo, true) . ';');


function createFolderIfNotExists($path)
{
    if (!file_exists($path)) {
        mkdir($path, 0755, true);
    }
}

function filesToArray($dir, $dirWithoutRoot = "")
{
    $listFiles = array();
    if ($handler = @opendir($dir)) {
        while (($sub = readdir($handler)) !== FALSE) {
            if ($sub != "." && $sub != ".." && $sub != "Thumb.db") {
                if (is_file($dir . "/" . $sub)) {
                    $listFiles[] = array("fullPath" => $dir . "/" . $sub, "localPath" => $dirWithoutRoot . "/" . $sub);
                } elseif (is_dir($dir . "/" . $sub)) {
                    $listFiles = array_merge($listFiles, filesToArray($dir . "/" . $sub, $dirWithoutRoot . "/" . $sub));
                }
            }
        }
        closedir($handler);
    }
    return $listFiles;
}

function rrmdir($dir)
{
    if (file_exists($dir)) {
        foreach (glob($dir . '/*') as $file) {
            if (is_dir($file)) rrmdir($file); else unlink($file);
        }
        rmdir($dir);
    }
}