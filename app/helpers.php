<?php

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

if (!function_exists("get_svg_icon")) {
    function get_svg_icon($path, $class = null, $svgClass = null)
    {
        if (!str_contains($path, "media")) {
            $path = theme()->getMediaUrlPath().$path;
        }

        $file_path = public_path($path);

        if (!file_exists($file_path)) {
            return "";
        }

        $svg_content = file_get_contents($file_path);

        if (empty($svg_content)) {
            return "";
        }

        $dom = new DOMDocument();
        $dom->loadXML($svg_content);

        // remove unwanted comments
        $xpath = new DOMXPath($dom);
        foreach ($xpath->query("//comment()") as $comment) {
            $comment->parentNode->removeChild($comment);
        }

        // add class to svg
        if (!empty($svgClass)) {
            foreach ($dom->getElementsByTagName("svg") as $element) {
                $element->setAttribute("class", $svgClass);
            }
        }

        // remove unwanted tags
        $title = $dom->getElementsByTagName("title");
        if ($title["length"]) {
            $dom->documentElement->removeChild($title[0]);
        }
        $desc = $dom->getElementsByTagName("desc");
        if ($desc["length"]) {
            $dom->documentElement->removeChild($desc[0]);
        }
        $defs = $dom->getElementsByTagName("defs");
        if ($defs["length"]) {
            $dom->documentElement->removeChild($defs[0]);
        }

        // remove unwanted id attribute in g tag
        $g = $dom->getElementsByTagName("g");
        foreach ($g as $el) {
            $el->removeAttribute("id");
        }
        $mask = $dom->getElementsByTagName("mask");
        foreach ($mask as $el) {
            $el->removeAttribute("id");
        }
        $rect = $dom->getElementsByTagName("rect");
        foreach ($rect as $el) {
            $el->removeAttribute("id");
        }
        $xpath = $dom->getElementsByTagName("path");
        foreach ($xpath as $el) {
            $el->removeAttribute("id");
        }
        $circle = $dom->getElementsByTagName("circle");
        foreach ($circle as $el) {
            $el->removeAttribute("id");
        }
        $use = $dom->getElementsByTagName("use");
        foreach ($use as $el) {
            $el->removeAttribute("id");
        }
        $polygon = $dom->getElementsByTagName("polygon");
        foreach ($polygon as $el) {
            $el->removeAttribute("id");
        }
        $ellipse = $dom->getElementsByTagName("ellipse");
        foreach ($ellipse as $el) {
            $el->removeAttribute("id");
        }

        $string = $dom->saveXML($dom->documentElement);

        // remove empty lines
        $string = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $string);

        $cls = array("svg-icon");

        if (!empty($class)) {
            $cls = array_merge($cls, explode(" ", $class));
        }

        $asd = explode("/media/", $path);
        if (isset($asd[1])) {
            $path = "assets/media/".$asd[1];
        }

        $output = "<!--begin::Svg Icon | path: $path-->\n";
        $output .= "<span class='".implode(" ", $cls)."'>".$string."</span>";
        $output .= "\n<!--end::Svg Icon-->";

        return $output;
    }
}

if (!function_exists("theme")) {
    /**
     * Get the instance of Theme class core
     *
     * @return \App\Core\Adapters\Theme|\Illuminate\Contracts\Foundation\Application|mixed
     */
    function theme()
    {
        return app(\App\Core\Adapters\Theme::class);
    }
}

if (!function_exists("util")) {
    /**
     * Get the instance of Util class core
     *
     * @return \App\Core\Adapters\Util|\Illuminate\Contracts\Foundation\Application|mixed
     */
    function util()
    {
        return app(\App\Core\Adapters\Util::class);
    }
}

if (!function_exists("bootstrap")) {
    /**
     * Get the instance of Util class core
     *
     * @return \App\Core\Adapters\Util|\Illuminate\Contracts\Foundation\Application|mixed
     * @throws Throwable
     */
    function bootstrap()
    {
        $demo      = ucwords(theme()->getDemo());
        $bootstrap = "\App\Core\Bootstraps\Bootstrap$demo";

        if (!class_exists($bootstrap)) {
            abort(404, "Demo has not been set or $bootstrap file is not found.");
        }

        return app($bootstrap);
    }
}

if (!function_exists("assetCustom")) {
    /**
     * Get the asset path of RTL if this is an RTL request
     *
     * @param $path
     * @param  null  $secure
     *
     * @return string
     */
    function assetCustom($path)
    {
        // Include rtl css file
        if (isRTL()) {
            return asset(theme()->getDemo()."/".dirname($path)."/".basename($path, ".css").".rtl.css");
        }

        // Include dark style css file
        if (theme()->isDarkModeEnabled() && theme()->getCurrentMode() !== "light") {
            $darkPath = str_replace(".bundle", ".".theme()->getCurrentMode().".bundle", $path);
            if (file_exists(public_path(theme()->getDemo()."/".$darkPath))) {
                return asset(theme()->getDemo()."/".$darkPath);
            }
        }

        // Include default css file
        return asset(theme()->getDemo()."/".$path);
    }
}

if (!function_exists("isRTL")) {
    /**
     * Check if the request has RTL param
     *
     * @return bool
     */
    function isRTL()
    {
        return (bool) request()->input("rtl");
    }
}

if (!function_exists("preloadCss")) {
    /**
     * Preload CSS file
     *
     * @return bool
     */
    function preloadCss($url)
    {
        return "<link rel='preload' href='$url' as='style' onload='this.onload=null;this.rel=\"stylesheet\"' type='text/css'><noscript><link rel='stylesheet' href='$url'></noscript>";
    }
}

if (!function_exists("cleanTitleToFilename")){
    function cleanTitleToFilename($string): string
    {
        //$string = preg_replace("/[^A-Za-z0-9\s]/", "", $string); // Removes special chars except for space
        //$string = str_replace(" ", "_", $string); // Replaces all spaces with hyphens.
        return Str::random();
    }
}

// Prevent XSS - Remove any JS
// Code explanation: Find any JS script blocks, and replace them to empty
if (!function_exists("strip_scripts")) {
    function strip_scripts($str): array|string|null
    {
        return preg_replace("#<script(.*?)>(.*?)</script>#is", "", $str);
    }
}

// Extend array to be longer, by duplicating the contents
if (!function_exists("extend_array")) {
    function extend_array($min, $max, $array)
    {
        for ($i = 0; $i < $max; $i++) {
            if (!array_key_exists($i, $array)) {
                $array[$i] = $array[$i - $min];
            }
        }

        return $array;
    }
}

/**
 * Convert ISO 8601 values like P2DT15M33S
 * to a total value of seconds.
 *
 * @param string $ISO8601
 * @return string
 * @throws Exception
 */
if (!function_exists("convertISO8601Duration")) {
    /**
     * @throws Exception
     */
    function convertISO8601Duration(string $ISO8601): string
    {
        $interval = new DateInterval($ISO8601);
        $hour = $interval->h;
        $min = $interval->i;
        $sec = $interval->s;

        $ms = str_pad($min, 2, "0", STR_PAD_LEFT) . ":" . str_pad($sec, 2, "0", STR_PAD_LEFT);

        return ($hour > 0) ? str_pad($hour, 2, "0", STR_PAD_LEFT) . ":" . $ms : $ms;
    }
}

if (!function_exists("catchException")) {
    function catchException(Exception $exception, string $channel = null): JsonResponse
    {
        DB::rollBack();
        Log::channel($channel)->error($exception->getCode() . " : " . $exception->getMessage() . " (Line " . $exception->getLine() . " at " . $exception->getFile() . ") " . $exception->getTraceAsString());
        return response()->json(["errors" => $exception->getCode() . " : " . $exception->getMessage() . " (Line " . $exception->getLine() . " at " . $exception->getFile() . ") " . $exception->getTraceAsString()]);
    }
}

/**
 * Convert file size bytes to other units
 * Option $round to round the result to 2 decimal place
 * Option $unit to include the unit
 */
if (!function_exists("formatSizeUnits")) {
    function formatSizeUnits($bytes, $round = false, $unit = true)
    {
        if ($bytes >= 1073741824) {
            $bytes = number_format($bytes / 1073741824, 2);
            $bytes = ($round) ? round($bytes, 2) : $bytes;
            $bytes = ($unit) ? $bytes . ' GB' : $bytes;
        } elseif ($bytes >= 1048576) {
            $bytes = number_format($bytes / 1048576, 2);
            $bytes = ($round) ? round($bytes, 2) : $bytes;
            $bytes =  ($unit) ? $bytes . ' MB' : $bytes;
        } elseif ($bytes >= 1024) {
            $bytes = number_format($bytes / 1024, 2);
            $bytes = ($round) ? round($bytes, 2) : $bytes;
            $bytes = ($unit) ? $bytes . ' KB' : $bytes;
        } elseif ($bytes > 1) {
            $bytes = ($round) ? round($bytes, 2) : $bytes;
            $bytes = ($unit) ? $bytes . ' bytes' : $bytes;
        } elseif ($bytes == 1) {
            $bytes = ($round) ? round($bytes, 2) : $bytes;
            $bytes = ($unit) ? $bytes . ' byte' : $bytes;
        } else {
            $bytes = ($unit) ? '0 bytes' : '0';
        }

        return $bytes;
    }
}

/**
 * Convert other file size unit to bytes
 * Input must include the suffix unit, ex: 200MB
 */
if (!function_exists("toByteSize")) {
    function toByteSize($p_sFormatted)
    {
        $aUnits = array('B' => 0, 'KB' => 1, 'MB' => 2, 'GB' => 3, 'TB' => 4, 'PB' => 5, 'EB' => 6, 'ZB' => 7, 'YB' => 8);
        $sUnit = strtoupper(trim(substr($p_sFormatted, -2)));
        if (intval($sUnit) !== 0) {
            $sUnit = 'B';
        }
        if (!in_array($sUnit, array_keys($aUnits))) {
            return false;
        }
        $iUnits = trim(substr($p_sFormatted, 0, strlen($p_sFormatted) - 2));
        if (!intval($iUnits) == $iUnits) {
            return false;
        }
        return $iUnits * pow(1024, $aUnits[$sUnit]);
    }
}

/**
 * Return the file size after formatted using formatSizeUnits() function
 */
if (!function_exists("getFileSizeUnit")) {
    function getFileSizeUnit($size)
    {
        $arr = explode(" ", $size);

        return (isset($arr[1])) ? $arr[1] : "";
    }
}
