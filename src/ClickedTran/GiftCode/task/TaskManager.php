<?php

declare(strict_types=1);

namespace ClickedTran\GiftCode\task;

use ClickedTran\GiftCode\utils\TimeUtil;
use pocketmine\scheduler\Task;

use ClickedTran\GiftCode\GiftCode;
use vennv\vapm\FiberManager;
use vennv\vapm\Promise;
use Throwable;
use JsonException;

final class TaskManager extends Task
{

    private ?Promise $promise = null;

    public function __construct(
        private readonly GiftCode $plugin
    )
    {
        // TODO: Implement __construct() method.
    }

    /**
     * @throws JsonException
     * @throws Throwable
     */
    public function onRun(): void
    {
        if ($this->promise === null) {
            $this->promise = Promise::c(function($resolve, $reject) {
                try {
                    $giftcode = $this->plugin->getCode();
                    foreach ($giftcode->getAll() as $code => $key) {
                        if ($giftcode->exists($code)) {
                            // This is the expiry time of the gift code it is in seconds
                            // Function calculates the time now and compares it with the expiry time
                            // Details: `/utils/TimeUtils.php`
                            if (TimeUtil::isExpired($key["expire"]) || $key["used"]) $giftcode->remove($code);
                        }
                        FiberManager::wait(); // call this function to wait for the next tick
                    }
                    $giftcode->save();
                    $resolve(true);
                } catch (Throwable $e) {
                    $reject($e);
                }
            })->catch(function(Throwable $e) {
                $this->plugin->getLogger()->error($e->getMessage());
            })->finally(function() {
                $this->promise = null;
            });
        }
    }
}
