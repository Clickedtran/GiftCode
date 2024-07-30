<?php

namespace ClickedTran\GiftCode\command\subcmd;

use JsonException;
use pocketmine\command\CommandSender;

use CortexPE\Commando\BaseSubCommand;
use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\exception\ArgumentOrderException;

use ClickedTran\GiftCode\GiftCode;
use pocketmine\utils\TextFormat;

final class RemoveGiftCodeCommand extends BaseSubCommand
{

    /**
     * @throws ArgumentOrderException
     */
    protected function prepare(): void
    {
        $this->setPermission("giftcode.command.remove");
        $this->registerArgument(0, new RawStringArgument("giftcode", true));
    }

    /**
     * @throws JsonException
     */
    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if (!isset($args["giftcode"])) {
            $sender->sendMessage(TextFormat::RED . "Please input giftcode to remove");
        } elseif (!GiftCode::getInstance()->getCode()->exists($args["giftcode"])) {
            $sender->sendMessage(TextFormat::RED . "Giftcode not found. Try again!");
        } else {
            GiftCode::getInstance()->removeCode($args["giftcode"]);
            $sender->sendMessage(TextFormat::GREEN . "Giftcode has been removed successfully!");
        }
    }

}
