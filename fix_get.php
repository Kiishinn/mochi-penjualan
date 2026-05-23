<?php
$files = glob(__DIR__ . '/app/Http/Controllers/**/*.php');
foreach ($files as $file) {
    $content = file_get_contents($file);
    // Replace array assignments: 'var' => Model::...->get()
    // but only if it's returning to a view list.
    // Let's just find and replace all `->get()` to `->paginate(10)` 
    // EXCEPT inside create/edit methods, or drop downs.
    
    // Specifically targeting these views:
    $patterns = [
        "['categories' => Category::orderBy('name')->get()]",
        "['units' => Unit::orderBy('name')->get()]",
        "['suppliers' => Supplier::orderBy('name')->get()]",
    ];
    $replacements = [
        "['categories' => Category::orderBy('name')->paginate(10)]",
        "['units' => Unit::orderBy('name')->paginate(10)]",
        "['suppliers' => Supplier::orderBy('name')->paginate(10)]",
    ];
    $newContent = str_replace($patterns, $replacements, $content);
    
    if ($newContent !== $content) {
        file_put_contents($file, $newContent);
    }
}

// Kasir transactions:
$kasirTx = __DIR__ . '/app/Http/Controllers/Kasir/TransactionController.php';
$content = file_get_contents($kasirTx);
$content = str_replace("->where('branch_id', \$branchId)->latest()->get();", "->where('branch_id', \$branchId)->latest()->paginate(10);", $content);
file_put_contents($kasirTx, $content);

// Kepala cabang transactions:
$kcTx = __DIR__ . '/app/Http/Controllers/KepalaCabang/ReportController.php';
$content = file_get_contents($kcTx);
$content = str_replace("->where('branch_id', \$branchId)->latest()->get();", "->where('branch_id', \$branchId)->latest()->paginate(10);", $content);
file_put_contents($kcTx, $content);

echo "Fixed missing get() calls.\n";
