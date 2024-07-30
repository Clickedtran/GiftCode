<?php

declare(strict_types=1);

namespace ClickedTran\GiftCode\utils;

use Throwable;
use vennv\vapm\FiberManager;
use vennv\vapm\Promise;

final class CheckConditions
{

    /**
     * @throws Throwable
     * @param callable ...$callbacks
     * @return Promise
     *
     * @example
     * CheckConditions::any(
     *    fn() => !isset($args["giftcode"]) ? TextFormat::RED . "Please input new giftcode" : false,
     *   fn() => GiftCode::getInstance()->getCode()->exists($args["giftcode"]) ? TextFormat::RED . "Giftcode already exist. Try again!" : false,
     * ...
     */
    public static function anyStringOrBoolean(callable ...$callbacks): Promise
    {
        return new Promise(function ($resolve, $reject) use ($callbacks) {
            foreach ($callbacks as $callback) {
                $call = $callback();
                if (is_string($call)) $reject($call);
                FiberManager::wait();
            }
            $resolve(true);
        });
    }

}