<?php

namespace App\Console\Commands;

use App\Models\Domain;
use App\Models\DomainNameserver;
use App\Models\Nameserver;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:reverse-lookup-nameserver')]
#[Description('Command description')]
class ReverseLookupNameserver extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!$key = env('VIEWDNS_API_KEY')) {
            $this->error('VIEWDNS_API_KEY not set');
            return;
        }

        if (!$nameserver = $this->ask('Nameserver')) {
            $this->error('Nameserver not set');
            return;
        }

        $nameserver = Nameserver::query()->firstOrCreate([
            'nameserver' => strtolower($nameserver)
        ]);

        if (!$nameserver->wasRecentlyCreated and !$this->confirm('Nameserver already exists, do you want to clear old data and fetch new?')) {
            return;
        }

        // Clear old records
        DomainNameserver::query()->where('nameserver_id', $nameserver->id)->delete();
        $this->info('Cleared old records');

        $page = 1;
        $count = 0;
        while (true) {
            $this->line('Fetching page '.$page);
            $data = $this->reverseLookupNameserver($key, $page, $nameserver->nameserver);

            foreach ($data['response']['domains'] as $domainData) {
                $domain = Domain::query()->firstOrCreate([
                    'domain' => $domainData['domain']
                ]);

                DomainNameserver::query()->firstOrCreate([
                    'domain_id' => $domain->id,
                    'nameserver_id' => $nameserver->id
                ]);

                $count++;
            }

            $this->line('---> Fetched '.$count.' records');

            if ($data['response']['total_pages'] === $data['response']['current_page']) {
                $this->info('Fetched all!');
                break;
            }

            $page++;
        }
    }

    protected function reverseLookupNameserver(string $apiKey, int $page, string $nameserver): array
    {
        return json_decode(file_get_contents('https://api.viewdns.info/reversens/?apikey='.$apiKey.'&output=json&ns='.$nameserver.'&page='.$page), true);
    }
}
