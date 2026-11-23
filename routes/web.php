<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\VirtualDiskController;
use App\Http\Controllers\DirectoryController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\SharedFileController;

/*
|--------------------------------------------------------------------------
| Home Route
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return Auth::check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

/*
|--------------------------------------------------------------------------
| Guest Routes
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {

    Route::get('/login', [AuthController::class, 'showLogin'])
        ->name('login');

    Route::post('/login', [AuthController::class, 'login'])
        ->name('login.submit');

    Route::get('/register', [AuthController::class, 'showRegister'])
        ->name('register');

    Route::post('/register', [AuthController::class, 'register'])
        ->name('register.submit');

});

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('logout');

    Route::resource('virtual-disks', VirtualDiskController::class);

    /*
    |--------------------------------------------------------------------------
    | Shared Files
    |--------------------------------------------------------------------------
    */

    Route::get('/shared-with-me', [PermissionController::class, 'sharedWithMe'])
        ->name('permissions.shared');

    /*
    |--------------------------------------------------------------------------
    | Shared File Access
    |--------------------------------------------------------------------------
    */

    // View a shared file (requires Read permission).
    Route::get(
        '/shared-files/{virtual_disk}/{directory}/{file}',
        [SharedFileController::class, 'show']
    )->name('shared-files.show');

    // Download a shared file (requires Read permission).
    Route::get(
        '/shared-files/{virtual_disk}/{directory}/{file}/download',
        [SharedFileController::class, 'download']
    )->name('shared-files.download');

    // Edit a shared file (requires Write permission).
    Route::get(
        '/shared-files/{virtual_disk}/{directory}/{file}/edit',
        [SharedFileController::class, 'edit']
    )->name('shared-files.edit');

    // Update a shared file (requires Write permission).
    Route::put(
        '/shared-files/{virtual_disk}/{directory}/{file}',
        [SharedFileController::class, 'update']
    )->name('shared-files.update');

    /*
    |--------------------------------------------------------------------------
    | Directory Management
    |--------------------------------------------------------------------------
    */

    Route::prefix('virtual-disks/{virtual_disk}/directories')
        ->name('directories.')
        ->group(function () {

            Route::get('/', [DirectoryController::class, 'index'])
                ->name('index');

            Route::get('/create', [DirectoryController::class, 'create'])
                ->name('create');

            Route::post('/', [DirectoryController::class, 'store'])
                ->name('store');

            Route::get('/{directory}', [DirectoryController::class, 'show'])
                ->name('show');

            Route::delete('/{directory}', [DirectoryController::class, 'destroy'])
                ->name('destroy');

        });

    /*
    |--------------------------------------------------------------------------
    | File Permissions Management
    |--------------------------------------------------------------------------
    */

    Route::prefix('virtual-disks/{virtual_disk}/directories/{directory}/files/{file}/permissions')
        ->name('permissions.')
        ->group(function () {

            Route::get('/', [PermissionController::class, 'create'])
                ->name('create');

            Route::post('/', [PermissionController::class, 'store'])
                ->name('store');

            Route::delete('/{permission}', [PermissionController::class, 'destroy'])
                ->name('destroy');

        });

    /*
    |--------------------------------------------------------------------------
    | File Management (Owner Access)
    |--------------------------------------------------------------------------
    */

    Route::prefix('virtual-disks/{virtual_disk}/directories/{directory}/files')
        ->name('files.')
        ->group(function () {

            Route::get('/create', [FileController::class, 'create'])
                ->name('create');

            Route::post('/create', [FileController::class, 'storeCreated'])
                ->name('store-created');

            Route::post('/', [FileController::class, 'store'])
                ->name('store');

            Route::get('/{file}/edit', [FileController::class, 'edit'])
                ->name('edit');

            Route::put('/{file}', [FileController::class, 'update'])
                ->name('update');

            Route::get('/{file}', [FileController::class, 'show'])
                ->name('show');

            Route::get('/{file}/download', [FileController::class, 'download'])
                ->name('download');

            Route::patch('/{file}/rename', [FileController::class, 'rename'])
                ->name('rename');

            Route::delete('/{file}', [FileController::class, 'destroy'])
                ->name('destroy');

        });

});
