<?php

declare(strict_types=1);

namespace AEDXDEV\ECMD\args;

final class TextArgument extends Argument{
    public function parse(array $input, int &$offset) : mixed{
        if(!isset($input[$offset])){
            if($this->isOptional()){ return null; }
            throw new \InvalidArgumentException("Missing argument: " . $this->getName());
        }
        $text = implode(" ", array_slice($input, $offset));
        $offset = count($input);
        return $text;
    }
}
