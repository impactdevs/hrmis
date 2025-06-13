<?php
// app/Helpers/IdEncoderFactory.php
namespace App\Helpers;

class IdEncoderFactory
{
    public static function getDefaultEncoder(): IdEncoder
    {
        return new IdEncoder(null, true, 10);
    }
}
