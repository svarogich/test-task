<?php declare(strict_types=1);


namespace App\Helpers;


class PasswordHelper
{
    /**
     * @param string $string
     * @return string
     */
    public static function getHash(string $string): string
    {
        return password_hash($string, PASSWORD_ARGON2I);
    }
}