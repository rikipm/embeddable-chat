<?php

use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Demo')->name('demo');
Route::inertia('/chat', 'Chat')->name('chat');
