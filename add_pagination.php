<?php

$dir = __DIR__ . '/app/Http/Controllers';
$viewDir = __DIR__ . '/resources/views';

$controllers = [
    'BranchController.php' => ['branches'],
    'UserController.php' => ['users'],
    'Kasir/MasterDataController.php' => ['categories', 'units', 'suppliers', 'products'],
    'Kasir/ReturnController.php' => ['returnItems'],
    'Kasir/TransactionController.php' => ['transactions'],
    'KepalaCabang/CategoryController.php' => ['categories'],
    'KepalaCabang/ProductController.php' => ['products'],
    'KepalaCabang/ReturnController.php' => ['returnItems'],
    'KepalaCabang/StockInController.php' => ['stockIns'],
    'KepalaCabang/StockOutController.php' => ['stockOuts'],
    'KepalaCabang/StockTransferController.php' => ['transfers'],
    'KepalaCabang/SupplierController.php' => ['suppliers'],
    'KepalaCabang/UnitController.php' => ['units'],
    'Owner/ViewController.php' => ['categories', 'units', 'suppliers', 'products', 'stockIns', 'stockOuts', 'transfers', 'sales', 'returnItems', 'stocks'],
];

foreach ($controllers as $file => $vars) {
    $path = $dir . '/' . $file;
    if (file_exists($path)) {
        $content = file_get_contents($path);
        
        // We only want to replace ->get() with ->paginate(10) if it's the main list
        // It's tricky to automate cleanly without parsing AST.
        // Let's use regex for specific assignments: $categories = Category::...->get();
        foreach ($vars as $var) {
            // Match `$var = Model::...->get();`
            $pattern = '/(\$' . $var . '\s*=\s*.*?)->get\(\)/s';
            $content = preg_replace($pattern, '$1->paginate(10)', $content);
        }
        
        file_put_contents($path, $content);
    }
}

// Next, add links() to the blade views
$views = [
    'owner/branches/index.blade.php' => 'branches',
    'owner/users/index.blade.php' => 'users',
    'owner/categories/index.blade.php' => 'categories',
    'owner/units/index.blade.php' => 'units',
    'owner/suppliers/index.blade.php' => 'suppliers',
    'owner/products/index.blade.php' => 'products',
    'owner/stock-ins/index.blade.php' => 'stockIns',
    'owner/stock-outs/index.blade.php' => 'stockOuts',
    'owner/stock-transfers/index.blade.php' => 'transfers',
    'owner/sales/index.blade.php' => 'sales',
    'owner/returns/index.blade.php' => 'returnItems',
    'owner/reports/stocks.blade.php' => 'stocks',

    'kepala-cabang/categories/index.blade.php' => 'categories',
    'kepala-cabang/units/index.blade.php' => 'units',
    'kepala-cabang/suppliers/index.blade.php' => 'suppliers',
    'kepala-cabang/products/index.blade.php' => 'products',
    'kepala-cabang/stock-ins/index.blade.php' => 'stockIns',
    'kepala-cabang/stock-outs/index.blade.php' => 'stockOuts',
    'kepala-cabang/stock-transfers/index.blade.php' => 'transfers',
    'kepala-cabang/returns/index.blade.php' => 'returnItems',

    'kasir/categories/index.blade.php' => 'categories',
    'kasir/units/index.blade.php' => 'units',
    'kasir/suppliers/index.blade.php' => 'suppliers',
    'kasir/products/index.blade.php' => 'products',
    'kasir/transactions/index.blade.php' => 'transactions',
    'kasir/returns/index.blade.php' => 'returnItems',
];

foreach ($views as $file => $var) {
    $path = $viewDir . '/' . $file;
    if (file_exists($path)) {
        $content = file_get_contents($path);
        
        // Add pagination links below the table if not exists
        if (strpos($content, '->links()') === false) {
            $content = str_replace('</table>', "</table>\n            <div class=\"mt-4\">\n                {{ \$" . $var . "->links() }}\n            </div>", $content);
            file_put_contents($path, $content);
        }
    }
}

echo "Done.\n";
