<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ArchiveSearchController;

// Route Halaman Utama & Pencarian (GET)
Route::get('/', [ArchiveSearchController::class, 'index'])->name('archives.index');

// Route Import CSV (POST)
Route::post('/archives/import-csv', [ArchiveSearchController::class, 'importCsv'])->name('archives.import.csv');