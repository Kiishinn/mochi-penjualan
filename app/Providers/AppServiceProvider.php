<?php

namespace App\Providers;

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
        \Illuminate\Pagination\Paginator::useBootstrapFive();

        \Illuminate\Support\Facades\View::composer('layouts.dashboard', function ($view) {
            $user = \Illuminate\Support\Facades\Auth::user();
            if ($user && in_array($user->role, ['owner', 'kepala_cabang'])) {
                $lowStocks = \App\Models\Stock::with(['product', 'branch'])
                    ->join('products', 'stocks.product_id', '=', 'products.id')
                    ->whereColumn('stocks.quantity', '<=', 'products.minimum_stock')
                    ->select('stocks.*');
                
                if ($user->role === 'kepala_cabang') {
                    $lowStocks->where('stocks.branch_id', $user->branch_id);
                }
                
                $view->with('lowStockNotifications', $lowStocks->get());

                $pendingReturns = \App\Models\ReturnItem::with(['product', 'branch', 'user'])
                    ->where('status', 'pending');
                
                if ($user->role === 'kepala_cabang') {
                    $pendingReturns->where('branch_id', $user->branch_id);
                }
                
                $view->with('pendingReturnNotifications', $pendingReturns->get());
            } else {
                $view->with('lowStockNotifications', collect());
                $view->with('pendingReturnNotifications', collect());
            }
        });
    }
}
