<?php

declare(strict_types=1);

namespace AEDXDEV\ECMD\args;

final class IntegerArgument extends Argument{
    public function parse(array $input, int &$offset) : mixed{
        if(!isset($input[$offset])){
            if($this->isOptional()){ return null; }
            throw new \InvalidArgumentException("Missing argument: " . $this->getName());
        }
        $value = $input[$offset++];
        if(!is_numeric($value)){ throw new \InvalidArgumentException("Invalid number: " . $this->getName()); }
        return (int)$value;
    }
}
