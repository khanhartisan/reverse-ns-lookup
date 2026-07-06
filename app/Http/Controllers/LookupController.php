<?php

namespace App\Http\Controllers;

use App\Models\Domain;
use App\Models\DomainNameserver;
use App\Models\Nameserver;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class LookupController extends Controller
{
    protected array $nameserversMap = [];

    public function index(Request $request)
    {
        $nameservers = $request->query('nameservers');
        $nameservers = explode(',', $nameservers);
        $nameservers = array_map(fn ($ns) => strtolower($ns), $nameservers);
        $nameservers = array_unique($nameservers);
        if (!$nameservers) {
            abort(404);
        }

        $nameserverIds = [];
        $nameservers = array_map(function (string $nameserverString) use (&$nameserverIds) {
            $nameserver = $this->getNameserver($nameserverString);
            $nameserverIds[] = $nameserver->id;
            return $nameserver;
        }, $nameservers);

        $intersectDomains = [];

        $firstNameserver = $nameservers[0];
        $firstNameserver->domains()->chunkById(1000, function (Collection $domains) use (&$intersectDomains, $nameserverIds) {
            $domains->each(function (Domain $domain) use (&$intersectDomains, $nameserverIds) {
                if (DomainNameserver::query()->where('domain_id', $domain->id)->whereIn('nameserver_id', $nameserverIds)->count() === count($nameserverIds)) {
                    $intersectDomains[] = $domain->domain;
                }
            });
        });

//        $intersectDomains = array_map(fn (string $domain) => "'$domain', ", $intersectDomains);

        return 'Total: '.count($intersectDomains).'<br />----------<br />'.implode('<br />'."\n", $intersectDomains);
    }

    protected function getNameserver(string $nameserverString): Nameserver
    {
        return $this->nameserversMap[$nameserverString] ??= Nameserver::query()->where('nameserver', $nameserverString)->firstOrFail();
    }
}
