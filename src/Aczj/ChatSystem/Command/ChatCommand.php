<?php

declare(strict_types=1);

namespace Aczj\ChatSystem\Command;

use AEDXDEV\ECMD\args\ChoiceArgument;
use AEDXDEV\ECMD\args\IntegerArgument;
use AEDXDEV\ECMD\args\RawStringArgument;
use AEDXDEV\ECMD\args\TextArgument;
use AEDXDEV\ECMD\BaseCommand;
use Aczj\ChatSystem\ChatSystem;
use pocketmine\command\CommandSender;

final class ChatCommand extends BaseCommand{
    public function __construct(private ChatSystem $plugin){
        parent::__construct($plugin, "chat", "Manage ChatSystem", ["chatsystem"]);
        $this->setPermission("chatsystem.admin");
        $this->setPermissionMessage($plugin->getChatManager()->message("no-permission"));
    }

    protected function prepare() : void{
        $this->registerSubCommand("MessageCooldown", [new IntegerArgument("seconds")], function(CommandSender $sender, array $args) : void{
            $seconds = (int)$args["seconds"];
            if(!$this->range($sender, $seconds, 1, 60)){ return; }
            $this->plugin->getChatManager()->setMessageCooldown($seconds);
            $sender->sendMessage($this->plugin->getChatManager()->message("cooldown-set", ["seconds" => (string)$seconds]));
        }, "chatsystem.admin", self::ALL_CONSTRAINT);

        $this->registerSubCommand("ColoringMessage", [new ChoiceArgument("state", ["on", "off"])], function(CommandSender $sender, array $args) : void{
            $enabled = $args["state"] === "on";
            $this->plugin->getChatManager()->setColoringEnabled($enabled);
            $sender->sendMessage($this->plugin->getChatManager()->message("coloring-set", ["state" => $enabled ? "ON" : "OFF"]));
        }, "chatsystem.admin", self::ALL_CONSTRAINT);

        $this->registerSubCommand("CapitalLetters", [new ChoiceArgument("state", ["on", "off"])], function(CommandSender $sender, array $args) : void{
            $enabled = $args["state"] === "on";
            $this->plugin->getChatManager()->setCapitalLettersEnabled($enabled);
            $sender->sendMessage($this->plugin->getChatManager()->message("capital-set", ["state" => $enabled ? "ON" : "OFF"]));
        }, "chatsystem.admin", self::ALL_CONSTRAINT);

        $this->registerSubCommand("CharacterCount", [new IntegerArgument("count")], function(CommandSender $sender, array $args) : void{
            $count = (int)$args["count"];
            if(!$this->range($sender, $count, 1, 1000)){ return; }
            $this->plugin->getChatManager()->setCharacterCount($count);
            $sender->sendMessage($this->plugin->getChatManager()->message("count-set", ["count" => (string)$count]));
        }, "chatsystem.admin", self::ALL_CONSTRAINT);

        $this->registerSubCommand("Lock", [], function(CommandSender $sender) : void{
            $this->plugin->getChatManager()->setLocked(true);
            $this->plugin->getServer()->broadcastMessage($this->plugin->getChatManager()->message("locked"));
        }, "chatsystem.admin", self::ALL_CONSTRAINT);

        $this->registerSubCommand("Unlock", [], function(CommandSender $sender) : void{
            $this->plugin->getChatManager()->setLocked(false);
            $this->plugin->getServer()->broadcastMessage($this->plugin->getChatManager()->message("unlocked"));
        }, "chatsystem.admin", self::ALL_CONSTRAINT);

        $this->registerSubCommand("noMention", [new ChoiceArgument("action", ["add", "remove"]), new RawStringArgument("player")], function(CommandSender $sender, array $args) : void{
            $player = $this->plugin->getPlayerRegistry()->resolve((string)$args["player"]);
            if($args["action"] === "add"){
                $this->plugin->getNoMentionManager()->add($player);
                $sender->sendMessage($this->plugin->getChatManager()->message("no-mention-added", ["player" => $player]));
                return;
            }
            $removed = $this->plugin->getNoMentionManager()->remove($player);
            $sender->sendMessage($this->plugin->getChatManager()->message($removed ? "no-mention-removed" : "no-mention-not-found", ["player" => $player]));
        }, "chatsystem.admin", self::ALL_CONSTRAINT);

        $this->registerSubCommand("ListNoMention", [], function(CommandSender $sender) : void{
            $players = $this->plugin->getNoMentionManager()->all();
            $sender->sendMessage($this->plugin->getChatManager()->message("no-mention-list", [
                "count" => (string)count($players),
                "players" => count($players) === 0 ? "None" : implode("§8, §7", $players)
            ]));
        }, "chatsystem.admin", self::ALL_CONSTRAINT);

        $this->registerSubCommand("FilterWords", [new ChoiceArgument("action", ["add", "remove"]), new TextArgument("word")], function(CommandSender $sender, array $args) : void{
            $word = trim((string)$args["word"]);
            if($word === ''){
                $sender->sendMessage($this->plugin->getChatManager()->message("filter-not-found"));
                return;
            }
            if($args["action"] === "add"){
                $this->plugin->getFilterWordsManager()->add($word);
                $sender->sendMessage($this->plugin->getChatManager()->message("filter-added", ["word" => $word]));
                return;
            }
            $removed = $this->plugin->getFilterWordsManager()->remove($word);
            $sender->sendMessage($this->plugin->getChatManager()->message($removed ? "filter-removed" : "filter-not-found", ["word" => $word]));
        }, "chatsystem.admin", self::ALL_CONSTRAINT);

        $this->registerSubCommand("ListFilterWords", [], function(CommandSender $sender) : void{
            $words = $this->plugin->getFilterWordsManager()->all();
            $sender->sendMessage($this->plugin->getChatManager()->message("filter-list", [
                "count" => (string)count($words),
                "words" => count($words) === 0 ? "None" : implode("§8, §7", $words)
            ]));
        }, "chatsystem.admin", self::ALL_CONSTRAINT);
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
        $sender->sendMessage($this->plugin->getChatManager()->message("usage"));
    }

    private function range(CommandSender $sender, int $value, int $min, int $max) : bool{
        if($value >= $min && $value <= $max){ return true; }
        $sender->sendMessage($this->plugin->getChatManager()->message("invalid-number", ["min" => (string)$min, "max" => (string)$max]));
        return false;
    }
}
