<?php

namespace App\Console\Commands;

use App\Services\Apilo\ApiloClient;
use Illuminate\Console\Command;

class TestConnectionCommand extends Command
{
    protected $signature = 'apilo:test-connection';
    protected $description = 'Connection test with API Apilo';

    public function __construct(private readonly ApiloClient $client)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $response = $this->client->get('/rest/api');

        if (!$response->successful()) {
            $this->error("Nie udało się nawiązać połączenia... " . $response->json('message'));
            return Command::FAILURE;
        }

        $this->info("Udało się nawiązać połączenie!");
        return Command::SUCCESS;
    }
}
