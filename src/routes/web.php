<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('demo.html');
});

Route::inertia('/chat', 'Chat')->name('chat');
