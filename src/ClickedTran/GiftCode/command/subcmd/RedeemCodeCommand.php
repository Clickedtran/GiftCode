<?php

declare(strict_types=1);

namespace ClickedTran\GiftCode\command\subcmd;

use ClickedTran\GiftCode\forms\FormRedeemCode;
use CortexPE\Commando\BaseSubCommand;

use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use Throwable;

final class RedeemCodeCommand extends BaseSubCommand
{

    protected function prepare(): void
    {
        $this->setPermission("giftcode.command.redemption");
    }

    /**
     * @throws Throwable
     */
    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if ($sender instanceof Player) {
            FormRedeemCode::getInstance($sender)->sendForm();
        }
    }

}
