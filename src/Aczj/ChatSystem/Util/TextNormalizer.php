<?php

declare(strict_types=1);

namespace Aczj\ChatSystem\Util;

final class TextNormalizer{
    public static function compact(string $text) : string{
        $text = mb_strtolower($text);
        $text = str_replace(["§", "&"], "", $text);
        return preg_replace('/[^a-z0-9\x{0600}-\x{06FF}]+/u', '', $text) ?? '';
    }

    public static function hasUppercaseLetter(string $text) : bool{
        return preg_match('/[A-Z]/', $text) === 1;
    }
}
