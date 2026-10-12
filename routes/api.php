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

/**
 * ============================================================================
 * Master Field Types CRUD Routes
 * ============================================================================
 */
Route::get(EndPoints::master_field_type_list, ControllerPaths::MasterFieldTypeController . ControllerMethods::list)->name('master_field_type.list');
Route::get(EndPoints::master_field_type_detail, ControllerPaths::MasterFieldTypeController . ControllerMethods::detail)->name('master_field_type.detail');
Route::get(EndPoints::master_field_type_detail . '/{id}', ControllerPaths::MasterFieldTypeController . ControllerMethods::detail)->name('master_field_type.detail_by_id');
Route::post(EndPoints::master_field_type_add, ControllerPaths::MasterFieldTypeController . ControllerMethods::add)->name('master_field_type.add');
Route::post(EndPoints::master_field_type_update, ControllerPaths::MasterFieldTypeController . ControllerMethods::update)->name('master_field_type.update');
Route::put(EndPoints::master_field_type_update . '/{id}', ControllerPaths::MasterFieldTypeController . ControllerMethods::update)->name('master_field_type.update_by_id');
Route::post(EndPoints::master_field_type_delete, ControllerPaths::MasterFieldTypeController . ControllerMethods::delete)->name('master_field_type.delete');
Route::delete(EndPoints::master_field_type_delete . '/{id}', ControllerPaths::MasterFieldTypeController . ControllerMethods::delete)->name('master_field_type.delete_by_id');
Route::post(EndPoints::master_field_type_change_status, ControllerPaths::MasterFieldTypeController . ControllerMethods::changeStatus)->name('master_field_type.change_status');
Route::post(EndPoints::master_field_type_change_status . '/{id}', ControllerPaths::MasterFieldTypeController . ControllerMethods::changeStatus)->name('master_field_type.change_status_by_id');

// RESTful resource aliases
Route::apiResource('master-field-types', 'Api\MasterFieldTypeController');


/**
 * ============================================================================
 * Master Template Categories CRUD Routes
 * ============================================================================
 */
Route::get(EndPoints::master_template_category_list, ControllerPaths::MasterTemplateCategoryController . ControllerMethods::list)->name('master_template_category.list');
Route::get(EndPoints::master_template_category_detail, ControllerPaths::MasterTemplateCategoryController . ControllerMethods::detail)->name('master_template_category.detail');
Route::get(EndPoints::master_template_category_detail . '/{id}', ControllerPaths::MasterTemplateCategoryController . ControllerMethods::detail)->name('master_template_category.detail_by_id');
Route::post(EndPoints::master_template_category_add, ControllerPaths::MasterTemplateCategoryController . ControllerMethods::add)->name('master_template_category.add');
Route::post(EndPoints::master_template_category_update, ControllerPaths::MasterTemplateCategoryController . ControllerMethods::update)->name('master_template_category.update');
Route::put(EndPoints::master_template_category_update . '/{id}', ControllerPaths::MasterTemplateCategoryController . ControllerMethods::update)->name('master_template_category.update_by_id');
Route::post(EndPoints::master_template_category_delete, ControllerPaths::MasterTemplateCategoryController . ControllerMethods::delete)->name('master_template_category.delete');
Route::delete(EndPoints::master_template_category_delete . '/{id}', ControllerPaths::MasterTemplateCategoryController . ControllerMethods::delete)->name('master_template_category.delete_by_id');
Route::post(EndPoints::master_template_category_change_status, ControllerPaths::MasterTemplateCategoryController . ControllerMethods::changeStatus)->name('master_template_category.change_status');
Route::post(EndPoints::master_template_category_change_status . '/{id}', ControllerPaths::MasterTemplateCategoryController . ControllerMethods::changeStatus)->name('master_template_category.change_status_by_id');

// RESTful resource aliases
Route::apiResource('master-template-categories', 'Api\MasterTemplateCategoryController');


/**
 * ============================================================================
 * Master Templates CRUD Routes
 * ============================================================================
 */
Route::get(EndPoints::master_template_list, ControllerPaths::MasterTemplateController . ControllerMethods::list)->name('master_template.list');
Route::get(EndPoints::master_template_detail, ControllerPaths::MasterTemplateController . ControllerMethods::detail)->name('master_template.detail');
Route::get(EndPoints::master_template_detail . '/{id}', ControllerPaths::MasterTemplateController . ControllerMethods::detail)->name('master_template.detail_by_id');
Route::post(EndPoints::master_template_add, ControllerPaths::MasterTemplateController . ControllerMethods::add)->name('master_template.add');
Route::post(EndPoints::master_template_update, ControllerPaths::MasterTemplateController . ControllerMethods::update)->name('master_template.update');
Route::put(EndPoints::master_template_update . '/{id}', ControllerPaths::MasterTemplateController . ControllerMethods::update)->name('master_template.update_by_id');
Route::post(EndPoints::master_template_delete, ControllerPaths::MasterTemplateController . ControllerMethods::delete)->name('master_template.delete');
Route::delete(EndPoints::master_template_delete . '/{id}', ControllerPaths::MasterTemplateController . ControllerMethods::delete)->name('master_template.delete_by_id');
Route::post(EndPoints::master_template_duplicate, ControllerPaths::MasterTemplateController . ControllerMethods::duplicate)->name('master_template.duplicate');
Route::post(EndPoints::master_template_duplicate . '/{id}', ControllerPaths::MasterTemplateController . ControllerMethods::duplicate)->name('master_template.duplicate_by_id');
Route::post(EndPoints::master_template_change_status, ControllerPaths::MasterTemplateController . ControllerMethods::changeStatus)->name('master_template.change_status');
Route::post(EndPoints::master_template_change_status . '/{id}', ControllerPaths::MasterTemplateController . ControllerMethods::changeStatus)->name('master_template.change_status_by_id');

// RESTful resource aliases
Route::apiResource('master-templates', 'Api\MasterTemplateController');
