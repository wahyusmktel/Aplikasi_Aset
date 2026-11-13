<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Database\Events\ConnectionEstablished;
use Illuminate\Database\SQLiteConnection;

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
        if (app()->environment('production')) {
            URL::forceScheme('https');
        }

        // Register custom SQLite functions (MD5, CONCAT) for database compatibility
        $registerSqliteFunctions = function ($connection) {
            if ($connection instanceof SQLiteConnection) {
                $pdo = $connection->getPdo();
                if (method_exists($pdo, 'sqliteCreateFunction')) {
                    $pdo->sqliteCreateFunction('MD5', 'md5');
                    $pdo->sqliteCreateFunction('CONCAT', fn(...$args) => implode('', $args));
                }
            }
        };

        Event::listen(ConnectionEstablished::class, function ($event) use ($registerSqliteFunctions) {
            $registerSqliteFunctions($event->connection);
        });

        // Register immediately if default connection is already active
        try {
            $registerSqliteFunctions(DB::connection());
        } catch (\Throwable $e) {
            // Ignore if connection not ready yet
        }
    }
}
