<?php

declare(strict_types=1);

namespace ClickedTran\GiftCode\forms;

use ClickedTran\GiftCode\forms\api\ProcessMessage;
use ClickedTran\GiftCode\GiftCode;
use pocketmine\console\ConsoleCommandSender;
use pocketmine\player\Player;
use pocketmine\Server;
use venndev\vformoopapi\attributes\custom\VInput;
use venndev\vformoopapi\attributes\VForm;
use venndev\vformoopapi\Form;
use venndev\vformoopapi\utils\TypeForm;
use venndev\vnpctradegui\utils\MessageUtil;
use JsonException;

#[VForm(
    title: "",
    type: TypeForm::CUSTOM_FORM,
    content: ""
)]
final class FormRedeemCode extends Form
{

    private array $replaces = [];

    public function __construct(
        Player $player,
        mixed $middleware = null
    )
    {
        parent::__construct($player, $middleware);
        $this->replaces["{player}"] = $player->getName();
        $this->replaces["{player_xuid}"] = $player->getXuid();
        $this->setTitle(MessageUtil::process(
            message: "forms.form-redeem-code.title",
            replaces: $this->replaces
        ));
        $this->setContent(MessageUtil::process(
            message: "forms.form-redeem-code.content",
            replaces: $this->replaces
        ));
    }

    /**
     * @throws JsonException
     */
    #[VInput(
        text: new ProcessMessage("forms.form-redeem-code.input-code.text"),
        placeholder: new ProcessMessage("forms.form-redeem-code.input-code.placeholder"),
    )]
    public function code(Player $player, string $code): void
    {
        if (GiftCode::getInstance()->getCode()->exists($code)) {
            $server = Server::getInstance();
            $player->sendMessage(MessageUtil::process(
                message: "messages.has-redeemed",
                replaces: [
                    "{code}" => $code
                ]
            ));
            $data =  GiftCode::getInstance()->getDataCode($code);
            $server->dispatchCommand(
                sender: new ConsoleCommandSender(
                    server: $server,
                    language: $server->getLanguage()
                ),
                commandLine: str_replace(array_keys($this->replaces), array_values($this->replaces), base64_decode($data["command"]))
            );
            GiftCode::getInstance()->getCode()->remove($code);
            GiftCode::getInstance()->getCode()->save();
        } else {
            $player->sendMessage(MessageUtil::process(
                message: "messages.code-not-exists",
                replaces: [
                    "{code}" => $code
                ]
            ));
        }
    }

}