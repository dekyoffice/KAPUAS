<?php

namespace App\Support;

class StrHelper
{
    /**
     * Escape karakter wildcard (% dan _) serta backslash untuk klausa LIKE pada query SQL.
     */
    public static function escapeLike(?string $value): string
    {
        return addcslashes($value ?? '', '%_\\');
    }
}
