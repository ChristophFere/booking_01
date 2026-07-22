<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DrinkItem extends Model
{
    public const DEFAULT_LIST_KEY = 'default';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'list_key',
        'name',
        'name_key',
        'quantity',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
        ];
    }

    public static function normalizeNameKey(string $name): string
    {
        return mb_strtolower(trim($name));
    }
}
