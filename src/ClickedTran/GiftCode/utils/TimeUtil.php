<?php

declare(strict_types=1);

namespace ClickedTran\GiftCode\utils;

final class TimeUtil
{

    public static function calculateExpireTime(int $day, int $hour, int $minute, int $second): int
    {
        return time() + ($day * 86400) + ($hour * 3600) + ($minute * 60) + $second;
    }

    public static function getRemainingTime(int $expireTime, bool $getString = false): object|string
    {
        $time = $expireTime - time();
        $day = floor($time / 86400);
        $hour = floor(($time % 86400) / 3600);
        $minute = floor(($time % 3600) / 60);
        $second = $time % 60;
        if ($getString) {
            return sprintf(
                "%d day(s) %d hour(s) %d minute(s) %d second(s)",
                $day, $hour, $minute, $second
            );
        } else {
            return (object) [
                "day" => $day,
                "hour" => $hour,
                "minute" => $minute,
                "second" => $second
            ];
        }
    }

    public static function isExpired(int $expireTime): bool
    {
        return time() >= $expireTime;
    }

}