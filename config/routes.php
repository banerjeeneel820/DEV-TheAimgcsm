<?php

use App\Core\Route;
use App\Controllers\Admin\NewsController;

defined('ROOTPATH') or exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| New-style routes
|--------------------------------------------------------------------------
*/

// News pages
Route::get('/admin/news', [
    NewsController::class,
    'fetch_news_data',
])->name('admin.news.index');

Route::get('/admin/news/add', [
    NewsController::class,
    'manage_news_data_view',
])->name('admin.news.create');

Route::get('/admin/news/edit', [
    NewsController::class,
    'manage_news_data_view',
])->name('admin.news.edit');

// News AJAX
Route::ajax('/admin/news/manage', [
    NewsController::class,
    'manage_global_news',
])->name('admin.news.manage');