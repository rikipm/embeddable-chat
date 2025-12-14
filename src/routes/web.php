<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return redirect('demo.php');
});

Route::inertia('/chat', 'Chat')->name('chat');
