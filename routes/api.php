<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\ScraperController;
use App\Http\Controllers\API\ScraperTableController;

Route::get('/scrape', [ScraperController::class, 'scrape']);
Route::get('/scrapetable', [ScraperTableController::class, 'scrape']);

