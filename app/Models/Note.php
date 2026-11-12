<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    protected $fillable = [
        'poster_name',
        'is_censored',
        'title',
        'content',
        'password',
    ];

    protected $hidden = [
        'password',
    ];

    protected function casts(): array
    {
        return [
            'is_censored' => 'boolean',
        ];
    }

    public function getDisplayAuthorAttribute(): string
    {
        $posterName = $this->poster_name ?: 'Anonymous';

        if (! $this->is_censored) {
            return $posterName;
        }

        $displayAuthor = preg_replace_callback(
            '/[\p{L}\p{N}]+/u',
            static function (array $matches): string {
                $characters = mb_str_split($matches[0], 1, 'UTF-8');
                $length = count($characters);

                if ($length === 1) {
                    return $matches[0];
                }

                if ($length === 2) {
                    return $characters[0].'*';
                }

                return $characters[0].str_repeat('*', $length - 2).$characters[$length - 1];
            },
            $posterName,
        );

        return $displayAuthor ?? $posterName;
    }
}
