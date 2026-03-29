<?php

namespace App\Services;

class Breadcrumbs
{
    protected static array $items = [];

    public static function add(string $title, string $url = null): void
    {
        self::$items[] = [
            'title' => $title,
            'url'   => $url,
        ];
    }

    public static function get(): array
    {
        return self::$items;
    }

    public static function reset(): void
    {
        self::$items = [];
    }
}
