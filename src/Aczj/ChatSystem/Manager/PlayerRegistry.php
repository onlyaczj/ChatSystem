<?php

declare(strict_types=1);

namespace Aczj\ChatSystem\Manager;

use pocketmine\player\Player;
use pocketmine\utils\Config;

final class PlayerRegistry{
    public function __construct(private Config $config){}

    public function record(Player $player) : void{
        $players = $this->config->get("players", []);
        $players[strtolower($player->getName())] = $player->getName();
        $this->config->set("players", $players);
        $this->config->save();
    }

    public function resolve(string $name) : string{
        $players = $this->config->get("players", []);
        return (string)($players[strtolower($name)] ?? $name);
    }

    public function save() : void{ $this->config->save(); }
}
