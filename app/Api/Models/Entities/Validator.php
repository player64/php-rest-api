<?php

namespace Api\Models\Entities;

class Validator
{

    /**
     * @throws EntityException
     */
    public static function required($value): void
    {
        if (!$value) {
            throw new EntityException('The value is required.');
        }
    }

    /**
     * @throws EntityException
     */
    public static function required_string(string $value): void
    {
        self::required(trim($value));
    }

    /**
     * @throws EntityException
     */
    public static function year(int $value): void
    {
        self::required($value);
        if ($value < 1900) {
            throw new EntityException('Wrong date the minimal year should be 1900.');
        }

        $now = new \DateTime();

        if ((int)$now->format('Y') < $value) {
            throw new EntityException('Wrong date. The release year cannot be in the future.');
        }
    }
}