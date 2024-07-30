<?php

declare(strict_types=1);

namespace ClickedTran\GiftCode;

use JsonException;
use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

use CortexPE\Commando\exception\HookAlreadyRegistered;
use CortexPE\Commando\PacketHooker;

use ClickedTran\GiftCode\command\GiftCodeCommand;
use ClickedTran\GiftCode\task\TaskManager;
use ClickedTran\GiftCode\utils\ConfigUtil;
use pocketmine\utils\SingletonTrait;
use venndev\vformoopapi\VFormLoader;

final class GiftCode extends PluginBase implements Listener
{
    use SingletonTrait;

    private Config $code;
    private string $cmdName = "giftcode";

    protected function onLoad(): void
    {
        self::setInstance($this);
        $this->saveDefaultConfig();
        $this->code = new Config(
            file: $this->getDataFolder() . "code.yml",
            type: Config::YAML
        );
    }

    /**
     * @throws HookAlreadyRegistered
     */
    protected function onEnable(): void
    {
        VFormLoader::init($this); // This is init the VAPM and VFormAPI
        $this->cmdName = ConfigUtil::getNested('command.name');
        if (!PacketHooker::isRegistered()) PacketHooker::register($this);
        $this->getServer()->getCommandMap()->register(
            fallbackPrefix: $this->cmdName,
            command: new GiftCodeCommand($this)
        );
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getScheduler()->scheduleRepeatingTask(new TaskManager($this), 20);
    }

    /**
     * @throws JsonException
     */
    public function onDisable(): void
    {
        $this->getCode()->save();
    }

    public function getCmdName(): string
    {
        return $this->cmdName;
    }

    public function getCode(): Config
    {
        return $this->code;
    }

    /**
     * @param string $name
     * @param float $expire
     * @param string $command - It will encode to base64
     * @throws JsonException
     *
     * Default method by ClickedTran, me don't want change it :)!
     */
    public function createCode(string $name, float $expire, string $command): void
    {
        $this->getCode()->set($name, [
            "expire" => $expire,
            "command" => $command,
            "used" => false
        ]);
        $this->getCode()->save();
    }

    public function getDataCode(string $name): ?array
    {
        return $this->getCode()->get($name) ?? null;
    }

    /**
     * @param string $name
     * @throws JsonException
     */
    public function removeCode(string $name): void
    {
        $all_code = $this->getCode()->getAll();
        unset($all_code[$name]);
        $this->getCode()->setAll($all_code);
        $this->getCode()->save();
    }

}
