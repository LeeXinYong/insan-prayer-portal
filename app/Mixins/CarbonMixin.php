<?php


namespace App\Mixins;


use Carbon\Carbon;
use Carbon\CarbonInterface;
use Closure;
use Illuminate\Support\Facades\App;

class CarbonMixin
{
    /**
     * datetime
     * @return Closure
     */
    public function localisedDateTime(): Closure
    {
        return function() {
            /** @var CarbonInterface $dateTime */
            $dateTime = $this;

            $locale = App::getLocale();
            switch ($locale) {
                case 'zh_TW':
                    $format = 'Y年Md日 ah時i分';
                    break;
                default:
                    $format = 'd M Y, h:i A';
            }

            return Carbon::parse($dateTime)->translatedFormat($format);
        };
    }

    /**
     * time only
     * @return Closure
     */
    public function localisedTime(): Closure
    {
        return function() {
            /** @var CarbonInterface $dateTime */
            $dateTime = $this;

            $locale = App::getLocale();
            switch ($locale) {
                case 'zh_TW':
                    $format = 'ah時i分';
                    break;
                default:
                    $format = 'h:i A';
            }

            return Carbon::parse($dateTime)->translatedFormat($format);
        };
    }

    /**
     * date only
     *
     * @return Closure
     */
    public function localisedDate(): Closure
    {
        return function($humanReadable) {
            /** @var CarbonInterface $dateTime */
            $dateTime = $this;

            $locale = App::getLocale();
            switch ($locale) {
                case 'zh_TW':
                    $format = 'Y年Md日';
                    break;
                default:
                    $format = 'd M Y';
            }

            $carbonDateTime = Carbon::parse($dateTime);

            if ($humanReadable) {
                $carbonDateTime = $carbonDateTime->startOfDay();
                return $carbonDateTime->diffForHumans();
            }

            return $carbonDateTime->translatedFormat($format);
        };
    }

    /**
     * day and month only
     * @return Closure
     */
    public function localisedDayMonth(): Closure
    {
        return function() {
            /** @var CarbonInterface $dateTime */
            $dateTime = $this;

            $locale = App::getLocale();
            switch ($locale) {
                case 'zh_TW':
                    $format = 'Md日';
                    break;
                default:
                    $format = 'd M';
            }

            return Carbon::parse($dateTime)->translatedFormat($format);
        };
    }

    /**
     * month and year only
     * @return Closure
     */
    public function localisedMonthYear(): Closure
    {
        return function() {
            /** @var CarbonInterface $dateTime */
            $dateTime = $this;

            $locale = App::getLocale();
            switch ($locale) {
                case 'zh_TW':
                    $format = 'Y年M';
                    break;
                default:
                    $format = 'M Y';
            }

            return Carbon::parse($dateTime)->translatedFormat($format);
        };
    }
}
