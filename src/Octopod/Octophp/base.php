<?php

/**
 * View Functions
 */

namespace {

    use Octopod\Octophp\Octopod;

    if (!function_exists('iPath')) {
        function iPath($imageKey) {
            return Octopod::iPath($imageKey);
        }
    }

    if (!function_exists('iWidth')) {
        function iWidth($imageKey) {
            return Octopod::iPath($imageKey);
        }
    }

    if (!function_exists('iHeight')) {
        function iHeight($imageKey) {
            return Octopod::iPath($imageKey);
        }
    }

    if ( ! function_exists('octoWPX')) {
        function octoWPX($value){
            return Octopod::octoWPX($value);
        }
    }

    if ( ! function_exists('octoHPX')) {
        function octoHPX($value){
            return Octopod::octoWPX($value);
        }
    }

    if ( ! function_exists('font')) {
        function font($fontKey){
            return Octopod::font($fontKey);
        }
    }

    if ( ! function_exists('style')) {
        function style($style){
            return Octopod::style($style);
        }
    }

}