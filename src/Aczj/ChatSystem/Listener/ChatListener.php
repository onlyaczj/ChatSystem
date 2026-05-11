<?php

declare(strict_types=1);

namespace Aczj\ChatSystem\Listener;

use Aczj\ChatSystem\ChatSystem;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\server\CommandEvent;
use pocketmine\player\Player;

final class ChatListener implements Listener{
    public function __construct(private ChatSystem $plugin){}

    public function onJoin(PlayerJoinEvent $event) : void{
        $this->plugin->getPlayerRegistry()->record($event->getPlayer());
    }

    public function onChat(PlayerChatEvent $event) : void{
        if(!$this->plugin->getChatManager()->handleChat($event->getPlayer(), $event->getMessage())){
            $event->cancel();
        }
    }

    public function onCommand(CommandEvent $event) : void{
        $sender = $event->getSender();
        if(!$sender instanceof Player){ return; }
        if(!$this->plugin->getChatManager()->handleCommand($sender)){
            $event->cancel();
        }
    }
}
