<?php
declare(strict_types=1);

namespace ClickedTran\GiftCode\command\subcmd;

use ClickedTran\GiftCode\utils\TimeUtil;
use CortexPE\Commando\args\IntegerArgument;
use CortexPE\Commando\BaseSubCommand;

use CortexPE\Commando\exception\ArgumentOrderException;
use pocketmine\command\CommandSender;

use ClickedTran\GiftCode\GiftCode;
use vennv\vapm\FiberManager;
use vennv\vapm\Promise;
use pocketmine\utils\TextFormat;
use Throwable;

final class ListGiftCodeCommand extends BaseSubCommand
{

    /**
     * @throws ArgumentOrderException
     */
    protected function prepare(): void
    {
        $this->setPermission("giftcode.command.list");
        $this->registerArgument(0, new IntegerArgument("start", false));
        $this->registerArgument(1, new IntegerArgument("end", false));
    }

    /**
     * @throws Throwable
     */
    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        Promise::c(function($resolve, $reject) use ($sender, $args): void {
            try {
                isset($args["start"]) ? $start = $args["start"] : $start = 0;
                isset($args["end"]) ? $end = $args["end"] : $end = 10;
                $all_giftcode = GiftCode::getInstance()->getCode()->getAll();
                if ($all_giftcode == null) $sender->sendMessage(TextFormat::RED . "The list of giftcode is empty!");
                $i = 0;
                foreach ($all_giftcode as $code => $key) {
                    if ($i < $start || $i >= $end) break;
                    /** @var string $expire */
                    $expire = TimeUtil::getRemainingTime($key["expire"], true);
                    $sender->sendMessage("§6> §bGiftCode: §7" . $code . " §bExpire: §7" . $expire);
                    $i++;
                    FiberManager::wait();
                }
                $resolve();
            } catch (Throwable $e) {
                $reject($e);
            }
        })->catch(function(Throwable $e) use ($sender): void {
            $sender->sendMessage(TextFormat::RED . "An error occurred while executing the command: " . $e->getMessage());
        });
    }

}
