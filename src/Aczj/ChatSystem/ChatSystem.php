<?php

declare(strict_types=1);

namespace Aczj\ChatSystem;

use Aczj\ChatSystem\Command\ChatCommand;
use Aczj\ChatSystem\Listener\ChatListener;
use Aczj\ChatSystem\Manager\ChatManager;
use Aczj\ChatSystem\Manager\FilterWordsManager;
use Aczj\ChatSystem\Manager\NoMentionManager;
use Aczj\ChatSystem\Manager\PlayerRegistry;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

final class ChatSystem extends PluginBase{
    private ChatManager $chatManager;
    private NoMentionManager $noMentionManager;
    private FilterWordsManager $filterWordsManager;
    private PlayerRegistry $playerRegistry;

    protected function onEnable() : void{
        @mkdir($this->getDataFolder());
        $this->saveDefaultConfig();
        $this->saveResource("no-mentions.yml");
        $this->saveResource("filter-words.yml");
        $this->saveResource("players.yml");

        $this->playerRegistry = new PlayerRegistry(new Config($this->getDataFolder() . "players.yml", Config::YAML));
        $this->noMentionManager = new NoMentionManager(new Config($this->getDataFolder() . "no-mentions.yml", Config::YAML));
        $this->filterWordsManager = new FilterWordsManager(new Config($this->getDataFolder() . "filter-words.yml", Config::YAML));
        $this->chatManager = new ChatManager($this, $this->noMentionManager, $this->filterWordsManager, $this->playerRegistry);

        $this->getServer()->getPluginManager()->registerEvents(new ChatListener($this), $this);
        $this->getServer()->getCommandMap()->register("chatsystem", new ChatCommand($this));
    }

    protected function onDisable() : void{
        $this->playerRegistry->save();
        $this->noMentionManager->save();
        $this->filterWordsManager->save();
        $this->saveConfig();
    }

    public function getChatManager() : ChatManager{ return $this->chatManager; }
    public function getNoMentionManager() : NoMentionManager{ return $this->noMentionManager; }
    public function getFilterWordsManager() : FilterWordsManager{ return $this->filterWordsManager; }
    public function getPlayerRegistry() : PlayerRegistry{ return $this->playerRegistry; }
}
