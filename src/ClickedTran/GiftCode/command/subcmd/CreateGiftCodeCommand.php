<?php

declare(strict_types=1);

namespace ClickedTran\GiftCode\command\subcmd;

use ClickedTran\GiftCode\utils\CheckConditions;
use ClickedTran\GiftCode\utils\TimeUtil;
use pocketmine\command\CommandSender;

use CortexPE\Commando\BaseSubCommand;
use CortexPE\Commando\args\IntegerArgument;
use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\exception\ArgumentOrderException;

use ClickedTran\GiftCode\GiftCode;
use pocketmine\utils\TextFormat;
use vennv\vapm\Async;
use Throwable;

final class CreateGiftCodeCommand extends BaseSubCommand
{

    /**
     * @throws ArgumentOrderException
     */
    protected function prepare(): void
    {
        $this->setPermission("giftcode.command.create");
        $this->registerArgument(0, new RawStringArgument("giftcode", true));
        $this->registerArgument(1, new IntegerArgument("day", true));
        $this->registerArgument(2, new IntegerArgument("hour", true));
        $this->registerArgument(3, new IntegerArgument("minute", true));
        $this->registerArgument(4, new IntegerArgument("second", true));
        $this->registerArgument(5, new RawStringArgument("command", true));
    }

    /**
     * @throws Throwable
     */
    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        new Async(function () use ($sender, $args): void {
            $result = Async::await(CheckConditions::anyStringOrBoolean(
                fn() => !isset($args["giftcode"]) ? TextFormat::RED . "Please input new giftcode" : false,
                fn() => GiftCode::getInstance()->getCode()->exists($args["giftcode"]) ? TextFormat::RED . "Giftcode already exist. Try again!" : false,
                fn() => !isset($args["day"]) ? TextFormat::RED . "Please input day" : false,
                fn() => !isset($args["hour"]) ? TextFormat::RED . "Please input hour" : false,
                fn() => !isset($args["minute"]) ? TextFormat::RED . "Please input minute" : false,
                fn() => !isset($args["second"]) ? TextFormat::RED . "Please input second" : false,
                fn() => !isset($args["command"]) ? TextFormat::RED . "Please input command" : false
            ));
            if ($result !== true) {
                $sender->sendMessage($result);
            } else {
                $code = $args["giftcode"];
                if (GiftCode::getInstance()->getCode()->exists($code)) {
                    $sender->sendMessage(TextFormat::RED . "Giftcode already exist. Try again!");
                    return;
                }
                $expireTime = TimeUtil::calculateExpireTime(
                    day: (int)$args["day"],
                    hour: (int)$args["hour"],
                    minute: (int)$args["minute"],
                    second: (int)$args["second"]
                );
                $cmd = $args["command"];
                GiftCode::getInstance()->createCode(
                    name: $code,
                    expire: $expireTime,
                    command: $cmd
                );
                /** @var string $remainingTime */
                $remainingTime = TimeUtil::getRemainingTime($expireTime, true);
                $sender->sendMessage(
                    TextFormat::GREEN . "Giftcode has been created successfully with name " .
                    TextFormat::RED . $code .
                    TextFormat::GREEN . " and expired in " .
                    TextFormat::RED . $remainingTime . TextFormat::GREEN . "."
                );
            }
        });
    }

}
