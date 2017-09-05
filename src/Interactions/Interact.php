<?php

namespace Makeable\LaravelEscrow\Interactions;

use Illuminate\Support\Str;

class Interact
{
    /**
     * @param $interaction
     * @param array ...$parameters
     *
     * @return mixed
     */
    public static function call($interaction, ...$parameters)
    {
        if (!Str::contains($interaction, '@')) {
            $interaction = $interaction.'@handle';
        }

        list($class, $method) = explode('@', $interaction);

        return call_user_func([app($class), $method], ...$parameters);
    }
}
