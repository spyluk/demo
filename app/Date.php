<?php
/**
 *
 * User: sergei
 * Date: 29.12.18
 * Time: 15:39
 */

namespace App;

use Jenssegers\Date\Date as BaseDate;

class Date extends BaseDate
{
    /**
     *
     */
    const DEFAULT_TO_STRING_FORMAT = 'd-m-Y H:i';
    /**
     *
     */
    const DEFAULT_TO_STRING_FORMAT_START_DAY = 'd-m-Y 00:00';

    /**
     * @param $date
     * @param string $format
     * @return string
     */
    public static function toUTC($date, $timezone, $format = self::DEFAULT_TO_STRING_FORMAT)
    {
        return (new self($date, $timezone))->timezone('UTC')->format($format);
    }

    /**
     * @param $date
     * @param string $format
     * @return string
     */
    public static function fromUTC($date, $timezone, $format = self::DEFAULT_TO_STRING_FORMAT)
    {
        return (new self($date, 'UTC'))->timezone($timezone)->format($format);
    }

    /**
     * @param $date
     * @param $timezone
     * @return int
     */
    public static function toUTCTimestamp($date, $timezone) {
        return (new self($date, $timezone))->timezone('UTC')->getTimestamp();
    }

    /**
     * @param $timestamp
     * @param $timezone
     * @param string $format
     * @return string
     */
    public static function fromUTCTimestamp($timestamp, $timezone, $format = self::DEFAULT_TO_STRING_FORMAT) {
        return self::createFromTimestamp($timestamp, $timezone)->format($format);
    }

    /**
     * @param string $tz
     * @return int
     */
    public static function nowTimestamp($tz = 'UTC')
    {
        return self::now($tz)->getTimestamp();
    }
}