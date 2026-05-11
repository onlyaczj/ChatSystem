<?php

declare(strict_types=1);

namespace AEDXDEV\ECMD\args;

use pocketmine\command\CommandSender;

final class ChoiceArgument extends Argument{
    /** @param string[] $choices */
    public function __construct(string $name, private array $choices, bool $optional = false){
        parent::__construct($name, $optional);
    }

    public function parse(array $input, int &$offset) : mixed{
        if(!isset($input[$offset])){
            if($this->isOptional()){ return null; }
            throw new \InvalidArgumentException("Missing argument: " . $this->getName());
        }
        $value = strtolower((string)$input[$offset++]);
        foreach($this->choices as $choice){
            if(strtolower($choice) === $value){ return strtolower($choice); }
        }
        throw new \InvalidArgumentException("Invalid option: " . $this->getName());
    }

    public function suggest(CommandSender $sender, string $current) : array{
        $current = strtolower($current);
        return array_values(array_filter($this->choices, static fn(string $choice) => str_starts_with(strtolower($choice), $current)));
    }
}
