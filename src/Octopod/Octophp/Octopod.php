<?php

namespace Octopod\Octophp;

use Octopod\Octophp\Facades\App;
use Octopod\Octophp\Facades\Config;

class Octopod {
    public static $fonts;
    public static $images;
    public static $screenId;
    public static $imgPath;

    public static $touch;
    public static $language;
    public static $applicationId;
    public static $platform;
    public static $appString;
    public static $version;
    public static $installationId;
    public static $client;
    public static $height;
    public static $width;

    /**
     * @return string - current environment
     */
    public static function env()
    {
        // dev live web
        return 'dev';
    }

    static function init(Request $request)
    {
        static::$touch = $request->info('touch');
        static::$language = $request->info('language');
        static::$applicationId = $request->info('applicationId');
        static::$platform = $request->info('platform');
        static::$appString = $request->info('appString');
        static::$version = $request->info('version');
        static::$installationId = $request->info('installationId');
        static::$client = $request->info('client');
        static::$height = $request->info('height');
        static::$width = $request->info('width');

        static::$screenId = static::detectScreenId();

        $generatedPath = App::path('app').Config::get('paths.generated').'/';

        if (file_exists($generatedPath."data/fonts.php")) {
            static::$fonts = include($generatedPath."data/fonts.php");
        }
        if (file_exists($generatedPath."data/images.php")) {
            static::$images = include($generatedPath."data/images.php");
        }

        static::$imgPath = ((Config::get('imagesUrl') === 'auto') ? static::cutPathForImages() : Config::get('imagesUrl')).static::$screenId."/";
    }

    static function detectScreenId()
    {
        foreach (Config::get('screen') as $key => $scr) {
            if (static::$width >= $scr['minWidth'] && static::$width <= $scr['maxWidth'] && static::$height >= $scr['minHeight'] && static::$height <= $scr['maxHeight']) {
                return $key;
            }
        }

        return Config::get('default.screen');
    }

    static function iPath($imageKey)
    {
        return static::$imgPath.$imageKey;
    }

    static function iWidth($imageKey)
    {
        return static::$images[$imageKey][static::$screenId]['width'];
    }

    static function iHeight($imageKey)
    {
        return static::$images[$imageKey][static::$screenId]['height'];
    }

    static function font($fontKey)
    {
        return static::$fonts[static::$screenId][$fontKey];
    }

    static function octoWPX($value)
    {
        $screen = Config::get('screen');

        return round($value / ($screen[Config::get('scaleScreen')]['optimalWidth'] / $screen[static::$screenId]['optimalWidth'])).'px';
    }

    static function octoHPX($value)
    {
        $screen = Config::get('screen');

        return round($value / ($screen[Config::get('scaleScreen')]['optimalHeight'] / $screen[static::$screenId]['optimalHeight'])).'px';
    }

    private static function cutPathForImages()
    {
        $url = App::make('uri')->create('generated/images/');

        return $url;
    }

    static function style($style)
    {
        $styleString = '';
        $styles = Config::get("style.$style");

        if (is_array($styles) and count($styles)) {
            foreach ($styles as $key => $value) {
                $styleString .= " $key=\"$value\" ";
            }
        }

        return $styleString;
    }


}