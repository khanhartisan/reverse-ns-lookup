<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class DomainNameserver extends Pivot
{
    protected $fillable = [
        'domain_id',
        'nameserver_id',
    ];
}
