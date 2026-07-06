<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Domain extends Model
{
    public function nameservers(): BelongsToMany
    {
        return $this
            ->belongsToMany(Nameserver::class)
            ->using(DomainNameserver::class);
    }
}
