<?php

declare(strict_types=1);

namespace AEDXDEV\ECMD\args;

use pocketmine\command\CommandSender;

abstract class Argument{
    public function __construct(private string $name, private bool $optional = false){}

    public function getName() : string{ return $this->name; }
    public function isOptional() : bool{ return $this->optional; }

    abstract public function parse(array $input, int &$offset) : mixed;

    /** @return string[] */
    public function suggest(CommandSender $sender, string $current) : array{ return []; }
}
