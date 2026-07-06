<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Nameserver extends Model
{
    protected $fillable = [
        'nameserver'
    ];

    public function domains(): BelongsToMany
    {
        return $this
            ->belongsToMany(Domain::class)
            ->using(DomainNameserver::class);
    }
}
