<?php

declare(strict_types=1);

namespace ClickedTran\GiftCode\forms;

use ClickedTran\GiftCode\forms\api\ProcessMessage;
use ClickedTran\GiftCode\GiftCode;
use pocketmine\player\Player;
use pocketmine\Server;
use venndev\vformoopapi\attributes\custom\VInput;
use venndev\vformoopapi\attributes\custom\VSlider;
use venndev\vformoopapi\attributes\normal\VButton;
use venndev\vformoopapi\attributes\VForm;
use venndev\vformoopapi\Form;
use venndev\vformoopapi\FormSample;
use venndev\vformoopapi\utils\TypeForm;
use ClickedTran\GiftCode\utils\ConfigUtil;
use Throwable;

#[VForm(
    title: "",
    type: TypeForm::NORMAL_FORM,
    content: ""
)]
final class FormManager extends Form
{

    private array $replaces = [];

    public function __construct(
        Player $player,
        mixed $middleware = null
    )
    {
        parent::__construct($player, $middleware);
        $this->replaces["{player}"] = $player->getName();
        $this->setTitle(ConfigUtil::getNested(
            message: "forms.form-manager.title",
            replaces: $this->replaces
        ));
        $this->setContent(ConfigUtil::getNested(
            message: "forms.form-manager.content",
            replaces: $this->replaces
        ));
    }

    /**
     * @throws Throwable
     */
    #[VButton(
        text: new ProcessMessage("forms.form-manager.button-create-code.text"),
        image: new ProcessMessage("forms.form-manager.button-create-code.image"),
    )]
    public function createCode(Player $player, mixed $data): void
    {
        $createCode = new FormSample($player);
        $createCode->setType(TypeForm::CUSTOM_FORM);
        $createCode->setTitle(ConfigUtil::getNested(
            message: "forms.form-create-code.title",
            replaces: $this->replaces
        ));
        $createCode->setContent(ConfigUtil::getNested(
            message: "forms.form-create-code.content",
            replaces: $this->replaces
        ));
        // Add content for form with type `custom form`
        $createCode->addContent(new VInput(
            text: ConfigUtil::getNested("forms.form-create-code.input-code.text"),
            placeholder: ConfigUtil::getNested("forms.form-create-code.input-code.placeholder"),
        ), function(Player $player, mixed $data) {
            if ($data === "" || $data === null) {
                $player->sendMessage(ConfigUtil::getNested(
                    message: "messages.must-input-code",
                    replaces: $this->replaces
                ));
            }
        });
        $createCode->addContent(new VInput(
            text: ConfigUtil::getNested("forms.form-create-code.input-command.text"),
            placeholder: ConfigUtil::getNested("forms.form-create-code.input-command.placeholder"),
        ), function(Player $player, mixed $data) {
            if ($data === "" || $data === null) {
                $player->sendMessage(ConfigUtil::getNested(
                    message: "messages.must-input-command",
                    replaces: $this->replaces
                ));
            }
        });
        $slidersNested = [
            "slider-days",
            "slider-hours",
            "slider-minutes",
            "slider-seconds",
        ];
        foreach ($slidersNested as $sliderNested) {
            $createCode->addContent(new VSlider(
                text: ConfigUtil::getNested("forms.form-create-code.$sliderNested.text"),
                min: (int)ConfigUtil::getNested("forms.form-create-code.$sliderNested.min"),
                max: (int)ConfigUtil::getNested("forms.form-create-code.$sliderNested.max"),
                step: (int)ConfigUtil::getNested("forms.form-create-code.$sliderNested.step"),
                default: (int)ConfigUtil::getNested("forms.form-create-code.$sliderNested.default"),
            ), fn() => null);
        }
        $cmdName = GiftCode::getInstance()->getCmdName();
        $createCode->setFormSubmit(function(Player $player, array $data) use ($cmdName) {
            $code = $data[0];
            $command = base64_encode($data[1]);
            $day = $data[2];
            $hour = $data[3];
            $minute = $data[4];
            $second = $data[5];
            Server::getInstance()->dispatchCommand(
                sender: $player,
                commandLine: $cmdName . " create $code $day $hour $minute $second $command"
            );
        });
        $createCode->setFormClose(function(Player $player) {
            FormManager::getInstance($player)->sendForm();
        });
        $createCode->sendForm();
    }

    /**
     * @throws Throwable
     */
    #[VButton(
        text: new ProcessMessage("forms.form-manager.button-list-code.text"),
        image: new ProcessMessage("forms.form-manager.button-list-code.image"),
    )]
    public function listCode(Player $player, mixed $data): void
    {
        $listCode = new FormSample($player);
        $listCode->setType(TypeForm::CUSTOM_FORM);
        $listCode->setTitle(ConfigUtil::getNested(
            message: "forms.form-list-code.title",
            replaces: $this->replaces
        ));
        $listCode->setContent(ConfigUtil::getNested(
            message: "forms.form-list-code.content",
            replaces: $this->replaces
        ));
        $listCode->addContent(new VInput(
            text: ConfigUtil::getNested("forms.form-list-code.input-start.text"),
            placeholder: ConfigUtil::getNested("forms.form-list-code.input-start.placeholder"),
        ), fn() => null);
        $listCode->addContent(new VInput(
            text: ConfigUtil::getNested("forms.form-list-code.input-end.text"),
            placeholder: ConfigUtil::getNested("forms.form-list-code.input-end.placeholder"),
        ), fn() => null);
        $cmdName = GiftCode::getInstance()->getCmdName();
        $listCode->setFormSubmit(function(Player $player, array $data) use ($cmdName) {
            $start = $data[0] ?? 0;
            $end = $data[1] ?? 10;
            Server::getInstance()->dispatchCommand(
                sender: $player,
                commandLine: $cmdName . " list $start $end"
            );
        });
        $listCode->setFormClose(function(Player $player) {
            FormManager::getInstance($player)->sendForm();
        });
        $listCode->sendForm();
    }

    /**
     * @throws Throwable
     */
    #[VButton(
        text: new ProcessMessage("forms.form-manager.button-delete-code.text"),
        image: new ProcessMessage("forms.form-manager.button-delete-code.image"),
    )]
    public function deleteCode(Player $player, mixed $data): void
    {
        $deleteCode = new FormSample($player);
        $deleteCode->setType(TypeForm::CUSTOM_FORM);
        $deleteCode->setTitle(ConfigUtil::getNested(
            message: "forms.form-delete-code.title",
            replaces: $this->replaces
        ));
        $deleteCode->setContent(ConfigUtil::getNested(
            message: "forms.form-delete-code.content",
            replaces: $this->replaces
        ));
        $deleteCode->addContent(new VInput(
            text: ConfigUtil::getNested("forms.form-delete-code.input-code.text"),
            placeholder: ConfigUtil::getNested("forms.form-delete-code.input-code.placeholder"),
        ), fn() => null);
        $cmdName = GiftCode::getInstance()->getCmdName();
        $deleteCode->setFormSubmit(function(Player $player, array $data) use ($cmdName) {
            $code = $data[0];
            Server::getInstance()->dispatchCommand(
                sender: $player,
                commandLine: $cmdName . " remove $code"
            );
        });
        $deleteCode->setFormClose(function(Player $player) {
            FormManager::getInstance($player)->sendForm();
        });
        $deleteCode->sendForm();
    }

}