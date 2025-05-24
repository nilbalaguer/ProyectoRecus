<?php

use App\Http\Controllers\Api\AuthorController;
use App\Http\Controllers\Api\FriendController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\api\FriendGroupsController;
use App\Http\Controllers\Api\MarkerController;
use App\Http\Controllers\Api\MarkerListController;
use App\Http\Controllers\Api\MarkerReviewsController;
use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\PostControllerAdvance;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('forget-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('forget.password.post');
Route::post('reset-password', [ResetPasswordController::class, 'reset'])->name('password.reset');

Route::group(['middleware' => 'auth:sanctum'], function () 
{

    Route::apiResource('posts', PostControllerAdvance::class);
    Route::apiResource('categories', CategoryController::class);
    Route::apiResource('roles', RoleController::class);

    Route::get('role-list', [RoleController::class, 'getList']);
    Route::get('role-permissions/{id}', [PermissionController::class, 'getRolePermissions']);
    Route::put('/role-permissions', [PermissionController::class, 'updateRolePermissions']);
    Route::apiResource('permissions', PermissionController::class);

    Route::get('category-list', [CategoryController::class, 'getList']);


    Route::get('abilities', function (Request $request) {
        return $request->user()->roles()->with('permissions')
            ->get()
            ->pluck('permissions')
            ->flatten()
            ->pluck('name')
            ->unique()
            ->values()
            ->toArray();
    });

    Route::get('category-list', [CategoryController::class, 'getList']);

    Route::get('get-posts', [PostControllerAdvance::class, 'getPosts']);
    Route::get('get-category-posts/{id}', [PostControllerAdvance::class, 'getCategoryByPosts']);
    Route::get('get-post/{id}', [PostControllerAdvance::class, 'getPost']);

    //API
    // Users
    Route::apiResource('users', UserController::class);
    Route::post('/users/updateimg', [UserController::class, 'updateimg']);
    Route::post('/users/updateimgAnother', [UserController::class, 'updateimgAnother']); //Modifica la imagen de otro usuario
    Route::post('/users/updateusername', [UserController::class, 'updateUserUsername']);
    Route::post('/users/updatedescription', [UserController::class, 'updateUserDescription']);

    Route::get('/user', [ProfileController::class, 'user']);
    Route::put('/user', [ProfileController::class, 'update']);

    Route::post('/users/deleteEverything', [UserController::class, 'deleteAllRelationWithUser']);

    // My User functions
    Route::get('/user/showUserByUsername', [UserController::class, 'showUserByUsername']);

    //Friends
    Route::get('/friends/showFriends', [FriendController::class, 'showUsers']);
    Route::get('/friends/myFriends', [FriendController::class, 'ShowrequestsRecived']);
    Route::get('/friends/requestsSend', [FriendController::class, 'ShowrequestsSent']);
    Route::get('/friends/allFriends', [FriendController::class, 'ShowAllFriends']);
    Route::get('/friends/getRequestStatus', [FriendController::class, 'getRequestStatus']);
    Route::get('/friends/GetUsersWithFriendRequests', [FriendController::class, 'GetUsersWithFriendRequests']);
    Route::get('/friends/destroyRequestAsReciver', [FriendController::class, 'destroyFriendRequestAsReciver']);
    Route::get('/friends/destroyRequestAsSender', [FriendController::class, 'destroyFriendRequestAsSender']);

    Route::post('/friends/accept', [FriendController::class, "acceptFriend"]);
    Route::post('/friends/delete', [FriendController::class, 'deleteFriend']);
    Route::post('/friends/accept', [FriendController::class, "acceptFriend"]);
    Route::post('/friends/request', [FriendController::class, 'createRequest']);
    Route::apiResource('friend', FriendController::class);

    //Route::post('/friends/request', [FriendController::class, 'createRequest']); Old
    // Route::get('/friends/showFriends', [FriendController::class, 'showFriends']); Old
    // Route::get('/friends/myFriends', [FriendController::class, 'showMyFriends']); Old
    // Route::get('/friends/requestsSend', [FriendController::class, 'requestsSent']); Old

    //Friend Groups
    Route::get('/friends/showMyGroups', [FriendGroupsController::class, 'showMyGroups']);
    Route::get('/friends/showJoinedGroups', [FriendGroupsController::class, 'showJoinedGroups']);
    Route::get('/friends/friendsInGroup', [FriendGroupsController::class, 'showPeopleInGroup']);
    
    Route::post('/friends/kickFromGroup', [FriendGroupsController::class, 'kickFromGroup']);
    Route::post('/friends/createGroup', [FriendGroupsController::class, 'createGroup']);
    Route::post('/friends/dropGroup', [FriendGroupsController::class, 'dropGroup']);
    Route::post('/friends/addToGroup', [FriendGroupsController::class, 'addToGroup']);

    // Markers
    Route::get('/markers/getAllMarkersFromFriendId',[MarkerController::class, 'getAllMarkersFromUserId']);
    Route::post('/markers/getLastMarkerFromFriends',[MarkerController::class, 'getLastMarkerFromFriends']);
    Route::apiResource('markers', MarkerController::class); // siempre ultimo ya que puede dar errores con el contenido posterior a markers/...

    // Markers lists
    Route::apiResource('markersLists', MarkerListController::class);
    Route::get('/markerList/showAll', [MarkerListController::class, 'showAll']);


    // Marker Reviews
    Route::get('/markerReviews/getAvgStarsByMarkerId/{marker_id}', [MarkerReviewsController::class, 'getAvgStarsByMarkerId']);
    Route::get('/markerReviews/getReviewByMarkerId/{marker_id}', [MarkerReviewsController::class, 'getReviewByMarkerId']);
    Route::apiResource('markerReviews', MarkerReviewsController::class);

    
});