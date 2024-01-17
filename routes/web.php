<?php

use App\Livewire\Admin\Genres;
use App\Livewire\Admin\Records;
use App\Livewire\Admin\UsersBasic;
use App\Livewire\Demo;
use App\Livewire\Shop;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Unnamed route
//Route::view('/','home');

//named route
Route::view('/','home')->name('home');
Route::get('shop',Shop::class)->name('shop');
#long notation
//Route::get('contact', function (){
//    return view('contact');
//});
#short notation
Route::view('contact','contact')->name('contact');
Route::view('playground','playground')->name('playground');
Route::view('under-construction','under-construction')->name('under-construction');

#old version
//Route::get('admin/records',function (){
//    $records = [
//        'Queen - <b>Greatest Hits</b>',
//        'The Rolling Stones - <em>Sticky Fingers</em>',
//        'The Beatles - Abbey Road'
//    ];
//    return view('admin.records.index',[
//        'records'=>$records
//    ]);
//});

//New version + unnamed group prefix
//Route::prefix('admin')->group(function (){
//    Route::redirect('/','/admin/records');
//    Route::get('records',function (){
//        $records = [
//            'Queen - Greatest Hits',
//            'The Rolling Stones - Sticky Fingers',
//            'The Beatles - Abbey Road'
//        ];
//        return view('admin.records.index', [
//            'records' => $records
//        ]);
//    });
//});

// named group prefix
Route::middleware(['auth','admin','active'])->prefix('admin')->name('admin.')->group(function (){
    Route::redirect('/','/admin/records');
    Route::get('genres',Genres::class)->name('genres');
    Route::get('records',Records::class)->name('records');
    Route::get('users/basic', UsersBasic::class)->name('users.basic');
//    Route::get('users/advanced', UsersAdvanced::class)->name('users.advanced');
//    Route::get('users/expert', UsersExpert::class)->name('users.expert');
    //Route::get('records',Demo::class)->name('records');
//    Route::get('records',function (){
//        $records = [
//            'Queen - Greatest Hits',
//            'The Rolling Stones - Sticky Fingers',
//            'The Beatles - Abbey Road'
//        ];
//        return view('admin.records.index', [
//            'records' => $records
//        ]);
//    })->name('records');
});


Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
    'active',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});
