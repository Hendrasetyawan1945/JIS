<?php

use App\Http\Controllers\ChatController;
use App\Models\Wisata;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('pariwisata', ['wisata' => Wisata::with('kategori')->where('status_aktif', true)->get()]);
});

// Chat API
Route::post('/chat/session', [ChatController::class, 'session']);
Route::post('/chat', [ChatController::class, 'kirim']);
