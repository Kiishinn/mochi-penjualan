<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Category;
use App\Models\Product;
use App\Models\Stock;
use App\Models\Supplier;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Branches ──
        $pusat = Branch::create([
            'name' => 'Mochi Petshop Pusat',
            'address' => 'Jl. R.A. Abusamah, Suka Bangun, Kecamatan Sukarami, Kota Palembang, Sumatera Selatan 30961',
            'phone' => '0711-123456',
        ]);
        $ario = Branch::create([
            'name' => 'Mochi Petshop Ario Kemuning',
            'address' => 'Ario Kemuning, Kecamatan Kemuning, Kota Palembang, Sumatera Selatan',
            'phone' => '0711-234567',
        ]);
        $bukit = Branch::create([
            'name' => 'Mochi Petshop Bukit Lama',
            'address' => 'Jl. Sultan M. Mansyur, Bukit Lama, Kecamatan Ilir Barat I, Kota Palembang, Sumatera Selatan 30134',
            'phone' => '0711-345678',
        ]);

        // ── Users ──
        User::create([
            'name' => 'Korina Novia',
            'email' => 'owner@mochi.test',
            'password' => Hash::make('password'),
            'role' => 'owner',
            'branch_id' => null,
        ]);
        User::create([
            'name' => 'Kepala Cabang Pusat',
            'email' => 'kepala@mochi.test',
            'password' => Hash::make('password'),
            'role' => 'kepala_cabang',
            'branch_id' => $pusat->id,
        ]);
        User::create([
            'name' => 'Kasir Pusat',
            'email' => 'kasir@mochi.test',
            'password' => Hash::make('password'),
            'role' => 'kasir',
            'branch_id' => $pusat->id,
        ]);

        // ── Categories (global) ──
        $catMkKucing = Category::create(['name' => 'Makanan Kucing']);
        $catMkAnjing = Category::create(['name' => 'Makanan Anjing']);
        $catVitamin = Category::create(['name' => 'Vitamin']);
        $catPasir = Category::create(['name' => 'Pasir Kucing']);
        $catAksesoris = Category::create(['name' => 'Aksesoris']);

        // ── Units (global) ──
        $pcs = Unit::create(['name' => 'pcs']);
        $pack = Unit::create(['name' => 'pack']);
        $karung = Unit::create(['name' => 'karung']);
        $botol = Unit::create(['name' => 'botol']);
        $kaleng = Unit::create(['name' => 'kaleng']);

        // ── Suppliers (global) ──
        Supplier::create([
            'name' => 'Supplier Utama Mochi Petshop',
            'phone' => '081234567890',
            'address' => 'Palembang',
        ]);

        // ── Products (global) ──
        $p1 = Product::create([
            'category_id' => $catMkKucing->id,
            'unit_id' => $pack->id,
            'name' => 'Whiskas Adult',
            'barcode' => '8853301101196',
            'purchase_price' => 45000,
            'selling_price' => 55000,
            'minimum_stock' => 10,
            'description' => 'Makanan kucing dewasa rasa tuna',
        ]);
        $p2 = Product::create([
            'category_id' => $catMkKucing->id,
            'unit_id' => $karung->id,
            'name' => 'Royal Canin Kitten',
            'barcode' => '3182550702423',
            'purchase_price' => 120000,
            'selling_price' => 150000,
            'minimum_stock' => 5,
            'description' => 'Makanan kucing kitten premium',
        ]);
        $p3 = Product::create([
            'category_id' => $catPasir->id,
            'unit_id' => $karung->id,
            'name' => 'Pasir Kucing Wangi',
            'barcode' => '8992775888001',
            'purchase_price' => 25000,
            'selling_price' => 35000,
            'minimum_stock' => 10,
            'description' => 'Pasir kucing gumpal wangi',
        ]);
        $p4 = Product::create([
            'category_id' => $catVitamin->id,
            'unit_id' => $botol->id,
            'name' => 'Vitamin Kucing',
            'barcode' => '8997010234567',
            'purchase_price' => 30000,
            'selling_price' => 45000,
            'minimum_stock' => 5,
            'description' => 'Vitamin daya tahan tubuh kucing',
        ]);
        $p5 = Product::create([
            'category_id' => $catAksesoris->id,
            'unit_id' => $pcs->id,
            'name' => 'Kalung Hewan',
            'barcode' => '8997019876543',
            'purchase_price' => 10000,
            'selling_price' => 25000,
            'minimum_stock' => 10,
            'description' => 'Kalung hewan peliharaan dengan lonceng',
        ]);

        // ── Stocks (per cabang) ──
        // Stok awal untuk Cabang Pusat
        foreach ([$p1, $p2, $p3, $p4, $p5] as $product) {
            Stock::create([
                'branch_id' => $pusat->id,
                'product_id' => $product->id,
                'quantity' => 50,
            ]);
        }
        // Stok awal untuk Cabang Ario Kemuning
        foreach ([$p1, $p2, $p3, $p4, $p5] as $product) {
            Stock::create([
                'branch_id' => $ario->id,
                'product_id' => $product->id,
                'quantity' => 30,
            ]);
        }
        // Stok awal untuk Cabang Bukit Lama
        foreach ([$p1, $p2, $p3, $p4, $p5] as $product) {
            Stock::create([
                'branch_id' => $bukit->id,
                'product_id' => $product->id,
                'quantity' => 20,
            ]);
        }
    }
}
