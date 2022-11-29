<?php


namespace App\Services;


use App\Mixins\CarbonMixin;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class DateTimeFormatterService
{
    /**
     * @see CarbonMixin::localisedDateTime()
     * @param $value
     * @param null $timezone set null to use user timezone
     * @param bool $unlocalised to return as Y-m-d H:i:S
     * @return array|Application|Translator|string|null
     */
    public static function formatModalDateTime($value, $timezone = null, bool $unlocalised = false): array|string|Translator|Application|null
    {
        if(is_null($value)) {
            return __("general.message.not_applicable");
        }

        if(is_null($timezone)) {
            // defaulted to user timezone
            $timezone = Auth::user()->timezone ?? Auth::user()->info->timezone ?? null;
        }

        $dateTime = Carbon::parse($value, "UTC")
            ->setTimezone($timezone);

        if ($unlocalised) {
            return $dateTime->format("Y-m-d H:i:s");
        } else {
            return $dateTime->localisedDateTime();
        }

    }

    public static function formatModalDateTimeEpoch($value): float|int|array|string|Translator|Application|null
    {
        if(is_null($value)) {
            return __("general.message.not_applicable");
        }

        return Carbon::parse($value)->timestamp;
    }

    public static function formatModalDateTimeMalaysia($value): array|string|Translator|Application|null
    {
        return self::formatModalDateTime($value, "Asia/Kuala_Lumpur");
    }

    public static function formatIntervals($value, $timezone = null, $other = null): array|string|Translator|Application|null
    {
        if(is_null($value)) {
            return __("general.message.not_applicable");
        }

        if(is_null($timezone)) {
            // defaulted to user timezone
            if(isset(Auth::user()->timezone)) {
                $timezone = Auth::user()->timezone;
            }
        }

        return Carbon::parse($value, "UTC")
            ->setTimezone($timezone)
            ->diffForHumans($other);
    }


    /**
     * @see CarbonMixin::localisedDate()
     * @param $value
     * @param bool $humanReadable
     * @return mixed
     *
     */
    public static function formatDate($value, bool $humanReadable = false): mixed
    {
        return Carbon::parse($value)->localisedDate($humanReadable);
    }

    /**
     * @see CarbonMixin::localisedDayMonth()
     * @param $value
     * @return mixed
     */
    public static function formatDayMonth($value): mixed
    {
        return Carbon::parse($value)->localisedDayMonth();
    }

    /**
     * @see CarbonMixin::localisedMonthYear()
     * @param $value
     * @return mixed
     */
    public static function formatMonthYear($value): mixed
    {
        return Carbon::parse($value)->localisedMonthYear();
    }

    /**
     * @see CarbonMixin::localisedTime()
     * @param $value
     * @param null $timezone
     * @return array|Application|Translator|string|null
     */
    public static function formatTime($value, $timezone = null): array|string|Translator|Application|null
    {
        if(is_null($value)) {
            return __("general.message.not_applicable");
        }

        if(is_null($timezone)) {
            // defaulted to user timezone
            if(isset(Auth::user()->timezone)) {
                $timezone = Auth::user()->timezone;
            }
        }

        return Carbon::parse($value, "UTC")->setTimezone($timezone)->localisedTime();
    }

    /**
     * to format date time in db, using raw query so be careful!
     *
     * @param $date_column
     * @param string $timezone_offset
     * @return string
     */
    public static function DBDateTimeFormatter($date_column, string $timezone_offset = "+00:00"): string
    {
        $locale = App::getLocale();
        switch ($locale) {
            case "zh_TW":
                $meridian = [
                    600 => "凌晨",
                    900 => "早上",
                    1130 => "上午",
                    1230 => "中午",
                    1800 => "下午",
                ];

                $timeLiteral = "(HOUR(CONVERT_TZ(".$date_column.", '+00:00', '".$timezone_offset."'))*100 + MINUTE(CONVERT_TZ(".$date_column.", '+00:00', '".$timezone_offset."')))";
                $caseStatement = "CASE ".
                    collect($meridian)->map(function($string, $time) use ($timeLiteral) {
                        return " WHEN $timeLiteral < $time THEN '$string' ";
                    })->join("")
                    ."ELSE '晚上' END";
                $format = "CONCAT('%Y年%m月%d日', ($caseStatement), '%h時%i分')";
                break;
            default:
                $format = "'%d %b %Y, %h:%i %p'";
        }
        return $format;
    }
}
