<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentController;

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

Route::get('/', function () {
    return view('welcome');
});
// goto students page from welcome
Route::get('std', [StudentController::class, 'gotoStd'])->name('gotoStd');
// fetch student data
Route::get('fetch-student', [StudentController::class, 'fetch_tudent']);
// goto students menually typing url 
Route::get('students', [StudentController::class, 'index']);
// insert student data
Route::post('students', [StudentController::class, 'store']);
// edit student
Route::get('edit-student/{id}', [StudentController::class, 'edit']);
// update student
Route::POST('update-student/{id}', [StudentController::class, 'update']);
// delete student
Route::delete('delete-student/{id}', [StudentController::class, 'destroy']);
