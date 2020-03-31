<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TournamentBracket extends Model
{
    protected $fillable = [
        'tournament_id',
        'match_no',
        'team_a',
        'team_b',
        'description',
    ];
}
