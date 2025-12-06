<?php

namespace App\Console\Commands;

use App\Services\Apilo\ApiloAuthService;
use Illuminate\Console\Command;

class CreateTokensCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'apilo:tokens-create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create refresh and access tokens for API authentication';

    public function __construct(private ApiloAuthService $authService)
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $authorizationCode = $this->ask("Podaj authorization code z Apilo:");

        if(! $authorizationCode || $authorizationCode === '' || !is_string($authorizationCode)) {
            $this->error('Nieprawidłowy kod!');
            return;
        }

        $tokens = $this->authService->exchangeAuthorizationCode($authorizationCode);

        $this->info("Pomyślnie utworzono tokeny:");
        $this->line(json_encode($tokens, JSON_PRETTY_PRINT));
    }
}