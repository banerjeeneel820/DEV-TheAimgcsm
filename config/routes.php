<?php

use App\Core\Route;
use App\Middleware\AdminMiddleware;
use App\Controllers\Admin\NewsController;

defined('ROOTPATH') or exit('No direct script access allowed');

Route::group([
    'prefix' => 'admin',
    'as' => 'admin.',
], function () {

    // News pages
    Route::get(
        '/news',
        [NewsController::class, 'fetch_news_data']
    )->name('news.index');

    Route::get(
        '/news/add',
        [NewsController::class, 'manage_news_data_view']
    )->name('news.create');

    Route::get(
        '/news/edit',
        [NewsController::class, 'manage_news_data_view']
    )->name('news.edit');

    // News AJAX endpoint
    Route::ajax(
        '/news/manage',
        [NewsController::class, 'manage_global_news']
    )->name('news.manage');

});