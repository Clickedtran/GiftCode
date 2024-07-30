<?php
declare(strict_types=1);

namespace ClickedTran\GiftCode\command;

use ClickedTran\GiftCode\command\subcmd\RedeemCodeCommand;
use ClickedTran\GiftCode\forms\FormManager;
use ClickedTran\GiftCode\forms\FormRedeemCode;
use InvalidArgumentException;
use pocketmine\Server;
use pocketmine\player\Player;
use CortexPE\Commando\BaseCommand;
use pocketmine\command\CommandSender;

use ClickedTran\GiftCode\GiftCode;
use ClickedTran\GiftCode\command\subcmd\HelpGiftCodeCommand;
use ClickedTran\GiftCode\command\subcmd\CreateGiftCodeCommand;
use ClickedTran\GiftCode\command\subcmd\RemoveGiftCodeCommand;
use ClickedTran\GiftCode\command\subcmd\ListGiftCodeCommand;
use ClickedTran\GiftCode\utils\ConfigUtil;
use pocketmine\utils\TextFormat;
use Throwable;

final class GiftCodeCommand extends BaseCommand
{

    public function __construct(protected \pocketmine\plugin\Plugin $plugin)
    {
        if (!$plugin instanceof GiftCode) throw new InvalidArgumentException("Plugin must be instance of GiftCode");
        parent::__construct(
            plugin: $plugin,
            name: ConfigUtil::getNested('command.name'),
            description: ConfigUtil::getNested('command.description'),
            aliases: ConfigUtil::getNested('command.aliases'),
        );
    }

    public function prepare(): void
    {
        /** @var GiftCode $plugin */
        $plugin = $this->plugin;
        $this->setPermission('giftcode.command');
        $this->registerSubcommand(new HelpGiftCodeCommand(
            plugin: $plugin,
            name: "help",
            description: "Help GiftCode Command"
        ));
        $this->registerSubcommand(new CreateGiftCodeCommand(
            plugin: $plugin,
            name: "create",
            description: "Create GiftCode Command"));
        $this->registerSubcommand(new RemoveGiftCodeCommand(
            plugin: $plugin,
            name: "remove",
            description: "Remove GiftCode Command"
        ));
        $this->registerSubcommand(new ListGiftCodeCommand(
            plugin: $plugin,
            name: "list",
            description: "List All GiftCode"
        ));
        $this->registerSubcommand(new RedeemCodeCommand(
            plugin: $plugin,
            name: "redeem",
            description: "Redeem GiftCode Command"
        ));
    }

    /**
     * @throws Throwable
     */
    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if (!$sender instanceof Player) {
            $sender->sendMessage(TextFormat::RED . "Please use this command in-game");
        } elseif (count($args) == 0) {
            if (
                Server::getInstance()->isOp($sender->getName()) ||
                $sender->hasPermission("giftcode.command.menu")
            ) {
               FormManager::getInstance($sender)->sendForm();
            } else {
                FormRedeemCode::getInstance($sender)->sendForm();
            }
        }
    }

    public function getPlugin(): \pocketmine\plugin\Plugin
    {
        return $this->plugin;
    }

}
