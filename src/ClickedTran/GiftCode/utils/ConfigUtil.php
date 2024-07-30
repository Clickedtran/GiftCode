<?php

declare(strict_types=1);

namespace ClickedTran\GiftCode\utils;

use ClickedTran\GiftCode\GiftCode;
use InvalidArgumentException;
use pocketmine\utils\TextFormat;

final class ConfigUtil
{

    public static function getNested(string $message, array $replaces = []): string|array
    {
        $resultConfig = GiftCode::getInstance()->getConfig()->getNested($message);
        $processString = fn($string) => TextFormat::colorize(str_replace(array_keys($replaces), array_values($replaces), $string));
        if (is_array($resultConfig)) {
            foreach ($resultConfig as $key => $value) $resultConfig[$key] = $processString($value);
        } elseif (is_string($resultConfig)) {
            $resultConfig = $processString($resultConfig);
        } else {
            throw new InvalidArgumentException("Invalid type of config value");
        }
        return $resultConfig;
    }

}