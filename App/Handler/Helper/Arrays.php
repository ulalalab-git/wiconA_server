<?php
namespace App\Handler\Helper;

/**
 *
 */
class Arrays
{
    /**
     * [convertUnixTime description]
     * @param  string|null $time   [description]
     * @param  string      $format [description]
     * @return [type]              [description]
     */
    public function convertUnixTime(string $time = null, string $format = 'Y-m-d H:i:s.u'): string
    {
        if (null === $time || $time === true) {
            $time = date($format);
        }

        return strtotime($time);
    }

    /**
     * [nowWeek description]
     * @param  string $unixTime [description]
     * @return [type]           [description]
     */
    public function convertWeek(string $unixTime = 'NOW'): date
    {
        return date('YW', $this->convertUnixTime($unixTime));
    }

    /**
     * [dayOfWeek description]
     * @param  string  $arg      [description]
     * @param  boolean $iso_8601 [description]
     * @return [type]            [description]
     */
    public function dayOfWeek(string $arg = '', bool $iso_8601 = true): int
    {
        $rtn = -1;

        $dt = $arg;
        if (empty($dt) || $dt = _EMPTY_DATETIME_) {
            $dt = $this->convertUnixTime();
        }

        if ($iso_8601 === true) {
            $rtn = date('N', $dt); // 1(Mon) ~ 7(Sun)
        } else {
            $rtn = date('w', $dt); // 0(Sun) ~ 6(Sat)
        }

        return intval($rtn);
    }

    /**
     * [find description]
     * @param  array  $data  [description]
     * @param  array  $finds [description]
     * @return [type]        [description]
     */
    public function find(array $data = [], array $finds = []): array
    {
        return array_intersect_key($data, array_flip($finds));
    }

    /**
     * [findOfWeekDate description]
     * @param  [type]  $date       [description]
     * @param  integer $targetDate [description]
     * @return [type]              [description]
     */
    public function findOfWeekDate($date = null, $targetDate = 1): date
    {
        // 0 = sunday 일 / 1 = monday 월 / 2 = tuesday 화 / 3 = wednesday 수 / 4 = thursday 목 / 5 = friday 금 / 6 = saturday 토
        $date = (empty($date) === true) ? $this->convertWeek() : $date;

        $weekDate = date('Ymd', $this->convertUnixTime(substr($date, 0, 4) . 'W' . str_pad(substr($date, 4, 2), 2, 0, STR_PAD_LEFT)));
        return date('Y-m-d H:i:s', $this->convertUnixTime($weekDate) - ((date('N', $this->convertUnixTime($weekDate)) - $targetDate) * 3600 * 24));
    }

    /**
     * [findWeeksToDatetime description]
     * @param  [type] $date [description]
     * @param  [type] $week [description]
     * @return [type]       [description]
     */
    public function findWeeksToDatetime($date, $week): date
    {
        $date = date_create($date)->add(date_interval_create_from_date_string('1 day'))->format("Y-m-d H:i:s");
        if (date('w', $this->convertUnixTime($date)) !== $week) {
            return $this->findWeeksToDatetime($date, $week);
        }

        return $date;
    }

    /**
     * [has description]
     * @param  array   $vals [description]
     * @param  array   $sets [description]
     * @return boolean       [description]
     */
    public function has(array $vals = [], array $sets = []): bool
    {
        return (count(array_intersect_key(array_flip($vals), $sets)) > 0);
    }

    /**
     * [map description]
     * @param  string|null $fn    [description]
     * @param  array       $array [description]
     * @return [type]             [description]
     */
    public function map(string $fn = null, array $array = []): array
    {
        if (is_array($array) === false) {
            return call_user_func($fn, $array);
        }

        foreach ($array as $key => $value) {
            if (is_array($value) === true) {
                $value = $this->map($fn, $value);
            } else {
                $value = call_user_func($fn, $value);
            }
            $array[$key] = $value;
        }

        return $array;
    }

    /**
     * [merge description]
     * @param  array  $arrs [description]
     * @param  array  $args [description]
     * @return [type]       [description]
     */
    public function merge(array $arrs = [], array $args = []): array
    {
        if ($this->has($arrs, $args) === false) {
            return $arrs;
        }

        return array_intersect_key(array_merge($arrs, $args), $arrs);
    }

    /**
     * [pivot description]
     * @param  array       $input  [description]
     * @param  string|null $target [description]
     * @return [type]              [description]
     */
    public function pivot(array $input = [], string $target = null): array
    {
        $response = [];
        if (empty($target) === true || is_array($input) === false) {
            return $response;
        }

        foreach ($input as $value) {
            $response[$value[$target]] = $value;
        }

        return $response;
    }
}
