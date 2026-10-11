<?php

use App\Constants\ControllerMethods;
use App\Constants\ControllerPaths;
use App\Constants\EndPoints;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/**
 * Authentication Routes (Public)
 */
Route::post(EndPoints::user_register, ControllerPaths::UserController . ControllerMethods::register)->name('user.register');
Route::post('/register', ControllerPaths::UserController . ControllerMethods::register);

Route::post(EndPoints::user_login, ControllerPaths::UserController . ControllerMethods::login)->name('user.login');
Route::post('/login', ControllerPaths::UserController . ControllerMethods::login);

/**
 *  Authenticated Routes
 */
Route::group(["middleware" => "auth:api"], function () {
    Route::get(EndPoints::user_profile, ControllerPaths::UserController . ControllerMethods::profile)->name('user.profile');
    Route::get(EndPoints::user_detail, ControllerPaths::UserController . ControllerMethods::profile)->name('user.detail');
    Route::get('/profile', ControllerPaths::UserController . ControllerMethods::profile);
    Route::post(EndPoints::user_logout, ControllerPaths::UserController . ControllerMethods::logout)->name('user.logout');
    Route::post('/logout', ControllerPaths::UserController . ControllerMethods::logout);
});

/**
 * Called When Unauthorised user Access services. Called From Middleware UnauthorisedUser.php -> redirectTo()
 */
Route::get(EndPoints::unauthorised, ControllerPaths::UserController . ControllerMethods::unauthorised)->name(EndPoints::unauthorised);

/**
 * Called When Non Admin user Access Admin's services. Called From Middleware AdminAccess.php -> handle()
 */
Route::get(EndPoints::adminaccess, ControllerPaths::UserController . ControllerMethods::adminaccess)->name(EndPoints::adminaccess);

/**
 * Called When Un-Active user Access Active User services. Called From Middleware ActiveUserAccess.php -> handle()
 */
Route::get(EndPoints::activeaccess, ControllerPaths::UserController . ControllerMethods::activeaccess)->name(EndPoints::activeaccess);

/**
 *  For Email Changes in ".env" File Must Follow below Steps
 *          First Check "APP_URL" in ".env" file is with port if not then add port number example -> "http://localhost:8000"
 *          1. terminate server if already started
 *          2. run command "php artisan config:cache"  For Clear Caches
 *          3. then run command "php artisan serve"  to start Server with update ".env" file config
 * */

Route::group(['middleware' => ['web']], function () {
    //routes here
    Route::get(EndPoints::password_reset, ControllerPaths::UserController . ControllerMethods::resetPassword)->name('password.reset');
    Route::get(EndPoints::password_reset . '/{token}', ControllerPaths::UserController . ControllerMethods::resetPassword)->name('password.reset');

});
Route::post(EndPoints::password_update, ControllerPaths::UserController . ControllerMethods::updatePassword)->name('password.update');
