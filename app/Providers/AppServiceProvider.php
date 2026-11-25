<?php

namespace App\Providers;

use App\Models\Configuracion;
use App\Services\TipoCambioService;
use Carbon\Carbon;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Carbon::setLocale('es');

        // Compartir variables globales de país, moneda y cotización con todas las vistas Blade
        View::composer('*', function ($view) {
            $pais = Configuracion::get('pais_operacion', 'BO');
            $simbolo = Configuracion::get('moneda_simbolo', $pais === 'PE' ? 'S/' : 'Bs.');
            $monedaPrincipal = Configuracion::get('moneda_principal', $pais === 'PE' ? 'PEN' : 'BOB');
            $simboloSecundario = $pais === 'PE' ? 'Bs.' : 'S/';
            $monedaSecundaria = $pais === 'PE' ? 'BOB' : 'PEN';

            $view->with([
                'paisOperacion' => $pais,
                'monedaSimbolo' => $simbolo,
                'monedaPrincipal' => $monedaPrincipal,
                'monedaSecundariaSimbolo' => $simboloSecundario,
                'monedaSecundaria' => $monedaSecundaria,
            ]);
        });
    }
}

