<?php

namespace thmrxx\yii2\gearman;

/**
 * Class Serialize
 * @package thmrxx\yii2\gearman
 */
class Serialize
{
    /**
     * @param mixed $data
     * @return string
     */
    public static function encode($data)
    {
        return json_encode($data);
    }

    /**
     * @param string $data
     * @return mixed
     */
    public static function decode($data)
    {
        return json_decode($data);
    }
}
