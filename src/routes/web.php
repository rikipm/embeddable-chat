<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('demo.php');
});

Route::inertia('/chat', 'Chat')->name('chat');
