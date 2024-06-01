<?php

namespace App\Providers;

use App\Http\Services\ServicesPessoa;
use App\Models\Pessoa;
use Illuminate\Support\ServiceProvider;

class ServicesPessoaProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {

        $this->app->bind(ServicesPessoa::class, function ($app) {

            $Pessoa = new Pessoa();

            return new ServicesPessoa($Pessoa);
        });
    }

}
