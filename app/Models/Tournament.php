<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tournament extends Model
{
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->hash = (string)$model->generateOrderHash();
        });
    }

    protected function generateOrderHash()
    {
        $data = random_bytes(16);
        assert(strlen($data) == 16);

        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    public function brackets()
    {
        return $this->hasMany(TournamentBracket::class);
    }
}
