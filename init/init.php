<?php

use Octopod\Core\Config;

include "../core/classes/Config.php";


rrmdir("../../generated");

createFolderIfNotExists("../../generated");
createFolderIfNotExists("../../generated/data");
createFolderIfNotExists("../../generated/images");

$fontSizes = Config::$font;
$imageTodo = array();

$scaleImagesList = filesToArray('../../application/resources/images/' . Config::$scaleScreenId);

if (sizeof($fontSizes[Config::$scaleScreenId])) {
    foreach (Config::$screen as $screenId => $dims) {
        $imagesList = filesToArray('../../application/resources/images/' . $screenId);
        if (sizeof($imagesList))
            foreach ($imagesList as $imageInfo) {
                $imageTodo[] = array("command" => "copy", "sourcePath" => $imageInfo['fullPath'], "destPath" => "../../generated/images/" . $screenId . $imageInfo['localPath'], "screenId" => $screenId, "imageKey" => $imageInfo['localPath']);
            }

        if ($screenId != Config::$scaleScreenId) {
            $divider = Config::$screen[Config::$scaleScreenId]["optimalWidth"] / $dims["optimalWidth"];

            foreach (Config::$font[Config::$scaleScreenId] as $name => $value) {
                if ((!isset (Config::$font[$screenId][$name])) || Config::$font[$screenId][$name] == "") {
                    $fontSizes[$screenId][$name] = round($value / $divider);
                }
            }

            foreach ($scaleImagesList as $scaleImageInfo) {
                if (!file_exists('../../application/resources/images/' . $screenId . $scaleImageInfo['localPath'])) {
                    $imageTodo[] = array("command" => "convert", "sourcePath" => $scaleImageInfo['fullPath'], "destPath" => "../../generated/images/" . $screenId . $scaleImageInfo['localPath'], "screenId" => $screenId, "imageKey" => $scaleImageInfo['localPath'], "divider" => $divider);
                }
            }
        }
    }
}

file_put_contents('../../generated/data/fonts.php', '<?php return ' . var_export($fontSizes, true) . ';');
file_put_contents('../../generated/data/imagesInitList.php', '<?php return ' . var_export($imageTodo, true) . ';');


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
    if (file_exists($dir))
        foreach (glob($dir . '/*') as $file) {
            if (is_dir($file)) rrmdir($file); else unlink($file);
        }
    rmdir($dir);
}