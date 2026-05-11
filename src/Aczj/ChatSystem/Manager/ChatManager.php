<?php

declare(strict_types=1);

namespace Aczj\ChatSystem\Manager;

use Aczj\ChatSystem\ChatSystem;
use Aczj\ChatSystem\Util\TextNormalizer;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

final class ChatManager{
    /** @var array<string, float> */
    private array $lastActionAt = [];

    public function __construct(
        private ChatSystem $plugin,
        private NoMentionManager $noMentions,
        private FilterWordsManager $filterWords,
        private PlayerRegistry $registry
    ){}

    public function handleChat(Player $player, string $message) : bool{
        $this->registry->record($player);
        $bypass = $this->hasBypass($player);

        if($this->isLocked() && !$bypass){
            $player->sendMessage($this->message("chat-locked"));
            return false;
        }
        $cooldownMessage = $this->checkCooldown($player, $bypass);
        if($cooldownMessage !== null){
            $player->sendMessage($cooldownMessage);
            return false;
        }
        if(!$this->isColoringEnabled() && !$bypass && str_contains($message, "§")){
            $player->sendMessage($this->message("color-blocked"));
            return false;
        }
        if(!$this->isCapitalLettersEnabled() && !$bypass && TextNormalizer::hasUppercaseLetter($message)){
            $player->sendMessage($this->message("capital-blocked"));
            return false;
        }
        if(!$bypass && mb_strlen($message) > $this->getCharacterCount()){
            $player->sendMessage($this->message("too-long", ["max" => (string)$this->getCharacterCount()]));
            return false;
        }
        $blockedMention = $this->findBlockedMention($message);
        if($blockedMention !== null && !$bypass){
            $player->sendMessage($this->message("mention-blocked", ["player" => $blockedMention]));
            return false;
        }
        if(!$bypass && $this->filterWords->containsBlockedWord($message)){
            $player->sendMessage($this->message("filter-blocked"));
            return false;
        }

        $this->playMentionSounds($player, $message);
        return true;
    }

    public function handleCommand(Player $player) : bool{
        $message = $this->checkCooldown($player, $this->hasBypass($player));
        if($message !== null){
            $player->sendMessage($message);
            return false;
        }
        return true;
    }

    public function checkCooldown(Player $player, bool $bypass) : ?string{
        if($bypass){ return null; }
        $seconds = $this->getMessageCooldown();
        if($seconds <= 0){ return null; }
        $key = strtolower($player->getName());
        $now = microtime(true);
        $last = $this->lastActionAt[$key] ?? 0.0;
        $remaining = $seconds - ($now - $last);
        if($remaining > 0){
            return $this->message("cooldown", ["seconds" => number_format($remaining, 2)]);
        }
        $this->lastActionAt[$key] = $now;
        return null;
    }

    public function setMessageCooldown(int $seconds) : void{ $this->setSetting("message-cooldown", max(1, min(60, $seconds))); }
    public function getMessageCooldown() : int{ return (int)$this->plugin->getConfig()->getNested("settings.message-cooldown", 1); }

    public function setColoringEnabled(bool $enabled) : void{ $this->setSetting("coloring-message", $enabled); }
    public function isColoringEnabled() : bool{ return (bool)$this->plugin->getConfig()->getNested("settings.coloring-message", false); }

    public function setCapitalLettersEnabled(bool $enabled) : void{ $this->setSetting("capital-letters", $enabled); }
    public function isCapitalLettersEnabled() : bool{ return (bool)$this->plugin->getConfig()->getNested("settings.capital-letters", true); }

    public function setCharacterCount(int $count) : void{ $this->setSetting("character-count", max(1, min(1000, $count))); }
    public function getCharacterCount() : int{ return (int)$this->plugin->getConfig()->getNested("settings.character-count", 100); }

    public function setLocked(bool $locked) : void{ $this->setSetting("locked", $locked); }
    public function isLocked() : bool{ return (bool)$this->plugin->getConfig()->getNested("settings.locked", false); }

    public function hasBypass(Player $player) : bool{
        return $player->hasPermission((string)$this->plugin->getConfig()->get("bypass-permission", "chatsystem.bypass"));
    }

    public function message(string $key, array $vars = []) : string{
        $vars["prefix"] = (string)$this->plugin->getConfig()->get("prefix", "§8[§bChatSystem§8]§r");
        $message = (string)$this->plugin->getConfig()->getNested("messages." . $key, $key);
        return $this->replace($message, $vars);
    }

    public function replace(string $text, array $vars) : string{
        foreach($vars as $key => $value){
            $text = str_replace("{" . $key . "}", (string)$value, $text);
        }
        return str_replace("\\n", "\n", $text);
    }

    private function setSetting(string $key, mixed $value) : void{
        $this->plugin->getConfig()->setNested("settings." . $key, $value);
        $this->plugin->getConfig()->save();
    }

    private function findBlockedMention(string $message) : ?string{
        foreach($this->noMentions->all() as $name){
            if(preg_match('/@' . preg_quote($name, '/') . '\b/i', $message) === 1){
                return $name;
            }
        }
        return null;
    }

    private function playMentionSounds(Player $sender, string $message) : void{
        foreach($this->plugin->getServer()->getOnlinePlayers() as $target){
            if(strtolower($target->getName()) === strtolower($sender->getName())){ continue; }
            if(preg_match('/@' . preg_quote($target->getName(), '/') . '\b/i', $message) !== 1){ continue; }
            $pos = $target->getPosition();
            $packet = PlaySoundPacket::create("random.click", $pos->getX(), $pos->getY(), $pos->getZ(), 1.0, 1.0);
            $target->getNetworkSession()->sendDataPacket($packet);
        }
    }
}
