<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PassportAuthController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\PermissionController;
use API\RoleController;
use API\CompanyController;
use API\BranchController;
use API\CustomerController;
use API\BranchUsersController;
use API\TaskController;
use API\LeadStatusController;
use API\LifecycleStageController;
use API\ContactOwnersController;
use API\ContactController;
use API\TeamController;
use API\TeamMemberController;
use API\CallController;
use API\DealController;
use API\ContactDealController;
use API\PropertyOwnersController;
use API\PropertyController;
use API\TenantController;
use API\TenancyContractController;
use API\CommissionsController;
use API\PermissionsController;
use API\RolesPermissionsController;
use API\EmployeesController;
use API\AttendanceController;
use API\PunchingController;
use API\LeaveController;
use API\BankDetailsController;
use API\TransferAmountController;
use API\InvoiceController;
use API\UnitController;
use API\TaxesController;
use API\CategoriesController;
use API\ModulesController;
use API\CustomFieldTypesController;
use API\CustomFieldController;
use API\SettingsBusinesController;
use API\ForgotPasswordController;
use API\ResetPasswordController;
use API\ProjectController;
use API\DepartmentController;
use API\RolePermissionController;
use API\AnnouncementController;
use API\CompanyInfoController;


// header('Access-Control-Allow-Origin:  *');
// header('Access-Control-Allow-Headers:  Content-Type, X-Auth-Token, Authorization, Origin');
// header('Access-Control-Allow-Methods:  GET, POST, PUT,PATCH');

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::post('register', [PassportAuthController::class, 'register']);
Route::post('login', [PassportAuthController::class, 'login']);
Route::middleware('auth:api')->group(function () {
    Route::resource('posts', PostController::class);
});

Route::get('announcement_types', "API\AnnouncementController@announcement_type");
Route::get('announcements', "API\AnnouncementController@index");
Route::post('announcement', "API\AnnouncementController@store");
Route::get('announcements/{id}', "API\AnnouncementController@show");
Route::put('announcements/{id}', "API\AnnouncementController@update");
Route::delete('announcements/{id}', "API\AnnouncementController@destroy");

// protected using role based permission in spatie package - laravel
Route::middleware(['auth:api'])->group(function () {
    Route::get('users', [UserController::class,'index']); 
    
    Route::resource('leave', LeaveController::class);
    Route::post('attend_by_user', "API\AttendController@all_attends_user");
    Route::get('totalhours', "API\AttendController@totalhours");
    Route::get('departments', "API\DepartmentController@index");
 

    Route::get('recentattend', "API\AttendController@recent");

    

    Route::post('attends', "API\AttendController@store");
    Route::get('attendexists', "API\AttendController@attendexists");
    Route::get('mysalary', 'API\MySalaryController@index');

    Route::get('alllabours', 'API\SupervisorAttendController@index');
    Route::get('labours', 'API\SupervisorAttendController@labours');
    Route::get('checked_in', 'API\SupervisorAttendController@checked_in');
    Route::get('go_break', 'API\SupervisorAttendController@go_break');
    Route::get('resume', 'API\SupervisorAttendController@resume');
    Route::get('checked_out', 'API\SupervisorAttendController@checked_out_today');
    Route::post('labour-attend', 'API\SupervisorAttendController@store');
    
});  
Route::get('designations', "API\DesignationController@index");
Route::get('alldepartments', "API\DepartmentController@alldepartments");


Route::get('permissions', [PermissionController::class,'index']);
Route::post('permissions', [PermissionController::class,'store']);

Route::get('countries', "API\CountryController@index");
Route::get('users', "API\UserManagementController@index");
Route::get('users/{id}', "API\UserManagementController@edit");
Route::put('users/{id}', "API\UserManagementController@update");
Route::post('reset-password-request', "API\PasswordResetRequestController@sendPasswordResetEmail");
Route::post('/change-password', "API\ChangePasswordController@passwordResetProcess");

Route::post('departments', "API\DepartmentController@store");
Route::get('departments/{id}', "API\DepartmentController@departmentById");
Route::put('departments/{id}', "API\DepartmentController@update");
Route::delete('departments/{id}', "API\DepartmentController@destroy");
Route::post('designations', "API\DesignationController@store");
Route::get('designations/{id}', "API\DesignationController@designationById");
Route::put('designations/{id}', "API\DesignationController@update");
Route::delete('designations/{id}', "API\DesignationController@destroy");
Route::post('applogin', 'API\AttendUserController@login');
Route::get('leavetypes', "API\LeaveTypeController@index");
Route::get('leavestatus', "API\LeaveTypeController@leavestatus");
Route::resource('employees', EmployeesController::class);
Route::post('searchEmployee', "API\EmployeesController@search");
Route::resource('roles', RoleController::class);
Route::get('role-permissions', 'API\RolePermissionController@index');
Route::post('role-permissions', 'API\RolePermissionController@store');
Route::get('role-permissions/{id}', 'API\RolePermissionController@edit');
Route::put('role-permissions/{id}', 'API\RolePermissionController@update');
// Route::get('roles/index/{id}', 'API\RoleController@index');
Route::post('roles/update/{id}', 'API\RoleController@updateById');
Route::delete('roles', 'API\RoleController@destroy');

Route::get('attends', "API\AttendController@index");



Route::get('leaves', "API\LeaveManagementController@index");
Route::post('leaves', "API\LeaveManagementController@store");
Route::post('employeeleave', "API\LeaveManagementController@postleave");
Route::get('employeeleave/{id}', "API\LeaveManagementController@editleave");
Route::post('leavesearch', "API\LeaveManagementController@search");

Route::get('attendmanagements', "API\AttendManagementController@index");
Route::get('allemployee', "API\AttendManagementController@allemployee");
Route::post('attendsearch', "API\AttendManagementController@search");
Route::post('attend-report', "API\AttendManagementController@report");

//Forgot Password Routes
Route::post('password/forgot-password','API\ForgotPasswordController@sendResetLinkResponse')->name('passwords.sent');

Route::post('app-forgot-password','API\AppForgotPasswordController@sendResetMsgResponse');

// Route::get('password/reset/{token}', 'API\ResetPasswordController@ResetResponse')->name('passwords.reset');
Route::post('password/reset', 'API\ResetPasswordController@sendResetResponse')->name('passwords.reset');

Route::get('timecategory', 'API\CompanyInfoController@timecategory');
Route::get('company', 'API\CompanyInfoController@index');
Route::post('company', 'API\CompanyInfoController@updatecompany');

Route::get('accounttype', 'API\AccountController@accounttype');

Route::get('accounts', 'API\AccountController@index');
Route::post('accounts', 'API\AccountController@store');
Route::get('accounts/{id}', 'API\AccountController@show');
Route::put('accounts/{id}', 'API\AccountController@update');
Route::delete('accounts/{id}', 'API\AccountController@destroy');

Route::get('payroll-employee', 'API\PayrollController@employees');
Route::post('getemployee', 'API\PayrollController@getemployee');
Route::post('payroll', 'API\PayrollController@store');

Route::get('payrolls', 'API\PayrollController@index');
Route::get('payrolls/{id}', 'API\PayrollController@show');

Route::get('transactions', 'API\AccountTransactionController@index');
Route::post('credit', 'API\AccountTransactionController@store');
Route::get('transactions/{id}', 'API\AccountTransactionController@show');


Route::get('empattend', 'API\EmployeeAttendanceController@index');


