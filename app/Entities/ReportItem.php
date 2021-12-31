<?php

namespace App\Entities;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use JsonSerializable;

class ReportItem implements Arrayable, Jsonable, JsonSerializable
{
    public function __construct(
        public string $name,
        public int $count
    ) {}

    public static function make(...$parameters)
    {
        return new static(...$parameters);
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'count' => $this->count
        ];
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }

    public function toJson($options = 0)
    {
        return json_encode($this->jsonSerialize(), $options);
    }
}
