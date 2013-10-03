<?php

namespace Octopod\Octophp;


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
        static::$touch = $request->data('touch');
        static::$language = $request->data('language');
        static::$applicationId = $request->data('applicationId');
        static::$platform = $request->data('platform');
        static::$appString = $request->data('appString');
        static::$version = $request->data('version');
        static::$installationId = $request->data('installationId');
        static::$client = $request->data('client');
        static::$height = $request->data('height');
        static::$width = $request->data('width');

        static::$screenId = static::detectScreenId();

        $generatedPath = Facades\App::path('generated').'/';

        if (file_exists($generatedPath."data/fonts.php")) {
            static::$fonts = include($generatedPath."data/fonts.php");
        }
        if (file_exists($generatedPath."data/images.php")) {
            static::$images = include($generatedPath."data/images.php");
        }

        static::$imgPath = ((Facades\Config::get('imagesUrl') === 'auto') ? static::cutPathForImages() : Facades\Config::get('imagesUrl')).static::$screenId."/";
    }

    static function detectScreenId()
    {
        foreach (Facades\Config::get('screen') as $key => $scr) {
            if (static::$width >= $scr['minWidth'] && static::$width <= $scr['maxWidth'] && static::$height >= $scr['minHeight'] && static::$height <= $scr['maxHeight']) {
                return $key;
            }
        }

        return Facades\Config::get('default.screen');
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
        $screen = Facades\Config::get('screen');

        return round($value / ($screen[Facades\Config::get('scaleScreen')]['optimalWidth'] / $screen[static::$screenId]['optimalWidth'])).'px';
    }

    static function octoHPX($value)
    {
        $screen = Facades\Config::get('screen');

        return round($value / ($screen[Facades\Config::get('scaleScreen')]['optimalHeight'] / $screen[static::$screenId]['optimalHeight'])).'px';
    }

    private static function cutPathForImages()
    {
        // @todo: make it use config generated path
        $url = Facades\App::make('uri')->create('app/generated/images/');

        return $url;
    }

    static function style($style)
    {
        $styleString = '';
        $styles = Facades\Config::get("style.$style");

        if (is_array($styles) and count($styles)) {
            foreach ($styles as $key => $value) {
                $styleString .= " $key=\"$value\" ";
            }
        }

        return $styleString;
    }


}