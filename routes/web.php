<?php

use App\Http\Controllers\CompanyController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::get('/', [CompanyController::class, 'fileImportExport']);
Route::post('file-import', [CompanyController::class, 'fileImport'])->name('file-import');

Route::get('chart', [CompanyController::class, 'chart']);
Route::get('fact', [CompanyController::class, 'fact']);
Route::get('forecast', [CompanyController::class, 'forecast']);
Route::get('show', function (){
    return view('table');
});