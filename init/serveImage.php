<?php

use Octopod\Octophp\Facades\Config;
use Octopod\Octophp\Facades\App;

if (App::path('clientAppPath') != null) {
    $generatedPath = App::path('dir') . App::path('clientAppPath') . Config::get('paths.generated') . '/';
    $resourcesPath = App::path('dir') . App::path('clientAppPath') . Config::get('paths.resources') . '/';
} else {
    $generatedPath = App::path('app') . Config::get('paths.generated') . '/'; // todo: ololo
    $resourcesPath = App::path('app') . Config::get('paths.resources') . '/'; // todo: ololo
}

function serveImage($imageInit)
{
    if (App::path('clientAppPath') != null) {
        $generatedPath = App::path('dir') . App::path('clientAppPath') . Config::get('paths.generated') . '/';
    } else {
        $generatedPath = App::path('app') . Config::get('paths.generated') . '/'; // todo: ololo
    }

    $imageInfo = @getimagesize($imageInit['sourcePath']);
    if ($imageInfo) {
        if ($imageInit['command'] == 'copy') {
            copyImage($imageInit['sourcePath'], $imageInit['destPath']);
            list($resultWidth, $resultHeight) = array($imageInfo[0], $imageInfo[1]);
        } else {
            list($resultWidth, $resultHeight) = convertImage($imageInit['sourcePath'], $imageInit['destPath'], $imageInit['divider']);
        }

        $fp = fopen($generatedPath . 'data/images.list', 'a+');
        fwrite($fp, $imageInit['destPath'] . "||" . $resultWidth . "||" . $resultHeight . "||" . $imageInit['screenId'] . "||" . $imageInit['imageKey'] . "\n");
        fclose($fp);
    }
}

function copyImage($source, $dest)
{
    createFolderForFilePath($dest);
    copy($source, $dest);

    return true;
}

function convertImage($source, $dest, $divider)
{
    createFolderForFilePath($dest);

    $imageInfo = @getimagesize($source);
    if ($imageInfo) {
        $newWidth = ($imageInfo[0] == 1) ? 1 : ceil($imageInfo[0] / $divider);
        $newHeight = ($imageInfo[1] == 1) ? 1 : ceil($imageInfo[1] / $divider);

        $src = imagecreatefromstring(file_get_contents($source));

        $tmp = imagecreatetruecolor($newWidth, $newHeight);

        imagealphablending($tmp, false);
        imagesavealpha($tmp, true);

        imagecopyresampled($tmp, $src, 0, 0, 0, 0, $newWidth, $newHeight, $imageInfo[0], $imageInfo[1]);

        switch ($imageInfo[2]) {
            case 1:
                imagegif($tmp, $dest);
                break;
            case 2:
                imagejpeg($tmp, $dest, 100);
                break;
            case 3:
                imagepng($tmp, $dest, 9);
                break;
        }

        imagedestroy($src);
        imagedestroy($tmp);

        return array($newWidth, $newHeight);
    }

//1 = GIF, 2 = JPG, 3 = PNG

    return true;
}

function saveImageListToArray()
{
    if (App::path('clientAppPath') != null) {
        $generatedPath = App::path('dir') . App::path('clientAppPath') . Config::get('paths.generated') . '/';
    } else {
        $generatedPath = App::path('app') . Config::get('paths.generated') . '/'; // todo: ololo
    }


    $imagesList = explode("\n", file_get_contents($generatedPath . 'data/images.list', 'a+'));
    foreach ($imagesList as $imageString) {
        if ($imageString != "") {
            $tmp = explode("||", $imageString);
            $key = ltrim($tmp[4], '/');
            $images[$key][$tmp[3]]['width'] = $tmp[1];
            $images[$key][$tmp[3]]['height'] = $tmp[2];
        }
    }
    file_put_contents($generatedPath . 'data/images.php', '<?php return ' . var_export($images, true) . ';');
}

function createFolderForFilePath($path)
{
    $path = substr($path, 0, strrpos($path, '/'));
    if (is_dir($path) || file_exists($path)) return;
    mkdir($path, 0755, true);
}