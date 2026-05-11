<?php

declare(strict_types=1);

namespace Aczj\ChatSystem\Manager;

use Aczj\ChatSystem\Util\TextNormalizer;
use pocketmine\utils\Config;

final class FilterWordsManager{
    public function __construct(private Config $config){}

    public function add(string $word) : void{
        $word = trim($word);
        if($word === ''){ return; }
        $words = $this->allAssoc();
        $words[TextNormalizer::compact($word)] = $word;
        $this->config->set("words", array_values($words));
        $this->config->save();
    }

    public function remove(string $word) : bool{
        $words = $this->allAssoc();
        $key = TextNormalizer::compact($word);
        if(!isset($words[$key])){ return false; }
        unset($words[$key]);
        $this->config->set("words", array_values($words));
        $this->config->save();
        return true;
    }

    public function containsBlockedWord(string $message) : bool{
        $message = TextNormalizer::compact($message);
        if($message === ''){ return false; }
        foreach(array_keys($this->allAssoc()) as $word){
            if($word !== '' && str_contains($message, $word)){ return true; }
        }
        return false;
    }

    /** @return string[] */
    public function all() : array{ return array_values($this->allAssoc()); }

    /** @return array<string, string> */
    private function allAssoc() : array{
        $assoc = [];
        foreach((array)$this->config->get("words", []) as $word){
            $assoc[TextNormalizer::compact((string)$word)] = (string)$word;
        }
        return $assoc;
    }

    public function save() : void{ $this->config->save(); }
}
