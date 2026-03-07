<?php

namespace App\Data\Reference;

use _HumbugBox7ff99e199a36\Symfony\Component\Console\Exception\InvalidArgumentException;
use Spatie\LaravelData\Data;

class PersonData extends Data
{
    public function __construct(
        public ?string $name = null,
        public ?string $given = null,
        public ?string $family = null,
        public ?string $prefix = null,
        public ?string $suffix = null,
        public ?string $alias = null,
    ) {}

    public static function fromString(string $name): PersonData
    {
        $words = explode(' ', $name);
        $len = count($words);

        if ($len == 0) {
            throw new InvalidArgumentException("name should not be blank");
        }

        if ($len == 1) {
            return new self(name: $words[0]);
        }

        return new self(
            given: $words[0],
            family: $words[$len - 1],
        );
    }
}

