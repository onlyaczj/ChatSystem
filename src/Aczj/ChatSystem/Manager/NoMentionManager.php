<?php

declare(strict_types=1);

namespace Aczj\ChatSystem\Manager;

use pocketmine\utils\Config;

final class NoMentionManager{
    public function __construct(private Config $config){}

    public function add(string $player) : void{
        $list = $this->allAssoc();
        $list[strtolower($player)] = $player;
        $this->config->set("players", array_values($list));
        $this->config->save();
    }

    public function remove(string $player) : bool{
        $list = $this->allAssoc();
        $key = strtolower($player);
        if(!isset($list[$key])){ return false; }
        unset($list[$key]);
        $this->config->set("players", array_values($list));
        $this->config->save();
        return true;
    }

    public function isBlocked(string $player) : bool{ return isset($this->allAssoc()[strtolower($player)]); }

    /** @return string[] */
    public function all() : array{ return array_values($this->allAssoc()); }

    /** @return array<string, string> */
    private function allAssoc() : array{
        $assoc = [];
        foreach((array)$this->config->get("players", []) as $name){
            $assoc[strtolower((string)$name)] = (string)$name;
        }
        return $assoc;
    }

    public function save() : void{ $this->config->save(); }
}
