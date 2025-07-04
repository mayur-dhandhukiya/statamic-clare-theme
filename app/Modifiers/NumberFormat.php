<?php

namespace App\Modifiers;

use Statamic\Modifiers\Modifier;

class NumberFormat extends Modifier
{
    public function index($value, $params, $context)
    {
        $decimals = $params[0] ?? 2;
        return number_format($value, $decimals);
    }
}
