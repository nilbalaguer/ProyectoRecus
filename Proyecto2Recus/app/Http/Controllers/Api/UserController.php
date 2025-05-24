<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\Friend;
use App\Models\Marker;
use App\Models\MarkerList;
use App\Models\MarkerListMarkers;
use App\Models\MarkerReviews;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;

class UserController extends Controller
{

    public function index()
    {
        $orderColumn = request('order_column', 'created_at');
        if (!in_array($orderColumn, ['id', 'name', 'created_at'])) {
            $orderColumn = 'created_at';
        }
        $orderDirection = request('order_direction', 'desc');
        if (!in_array($orderDirection, ['asc', 'desc'])) {
            $orderDirection = 'desc';
        }
        $users = User::
        when(request('search_id'), function ($query) {
            $query->where('id', request('search_id'));
        })
            ->when(request('search_title'), function ($query) {
                $query->where('name', 'like', '%'.request('search_title').'%');
            })
            ->when(request('search_global'), function ($query) {
                $query->where(function($q) {
                    $q->where('id', request('search_global'))
                        ->orWhere('name', 'like', '%'.request('search_global').'%');

                });
            })
            ->orderBy($orderColumn, $orderDirection)
            ->paginate(500);

        return UserResource::collection($users);
    }

    public function store(StoreUserRequest $request)
    {
        $role = Role::find($request->role_id);
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->username = $request->username;
        $user->desc = $request->desc;

        $user->password = Hash::make($request->password);

        if ($user->save()) {
            if ($role) {
                $user->assignRole($role);
            }
            return new UserResource($user);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return UserResource
     */
    public function show(User $user)
    {
        $user->load('roles')->get();
        return new UserResource($user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateUserRequest $request
     * @param User $user
     * @return UserResource
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $role = Role::find($request->role_id);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->username = $request->username;
        $user->desc = $request->desc;

        if(!empty($request->password)) {
            $user->password = Hash::make($request->password) ?? $user->password;
        }

        if ($user->save()) {
            if ($role) {
                $user->syncRoles($role);
            }
            return new UserResource($user);
        }
    }

    public function updateimg(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:65536', // max 65MB
        ]);

        $user = auth()->user();

        // Elimina la imagen anterior
        $user->clearMediaCollection('users');

        $user->addMediaFromRequest('image')->toMediaCollection('users');

        return response()->json(['message' => 'Imagen actualizada']);
    }

    public function updateimgAnother(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:65536', // max 65MB
        ]);

        $user = User::findOrFail($request->input('id')); 

        // Elimina la imagen anterior
        $user->clearMediaCollection('users');

        $user->addMediaFromRequest('image')->toMediaCollection('users');

        return response()->json(['message' => 'Imagen actualizada']);
    }


    public function destroy(User $user)
    {
        $this->authorize('user-delete');
        $user->delete();

        return response()->noContent();
    }    

    /*
     *
     */
    public function showUserByUsername(Request $request)
    {
        $user = User::where('username', $request->username)->first(['id','name','username','desc','email']);

        $media = $user->getFirstMedia('users');
        $user->media_url = $media ? $media->getUrl() : null;

        return response()->json($user,200);
    }

    public function updateUserUsername(Request $request) {
        $id = auth()->id();

        $user = User::where('id', $id)->first();

        if (!$user) {
            return response()->json(['message' => 'User not found']);
        }

        $user->update([
            "name"=>$request->username
        ]);

        return response()->json(['message' => 'Username updated']);
    }

    public function updateUserDescription(Request $request) {
        $id = auth()->id();

        $user = User::where('id', $id)->first();

        if (!$user) {
            return response()->json(['message' => 'User not found']);
        }

        $user->update([
            "desc"=>$request->desc
        ]);

        return response()->json(['message' => 'Description updated']);
    }

    //Eliminar todo lo que este relacionado con un usuario incluido el mismo
    public function deleteAllRelationWithUser(Request $request) {
        try {
            $user = User::findOrFail($request->id);

            $this->authorize('user-delete');
        
            // Eliminar amigos
            Friend::where("sender_user_id", $request->id)
                ->orWhere("reciver_user_id", $request->id)
                ->delete();

            // Eliminar reseñas
            MarkerReviews::where("user_id", $request->id)->delete();
        
            // Eliminar marcadores
            Marker::where("user_id", $request->id)->delete();
        
            $markerLists = MarkerList::where("owner_user_id", $request->id)->get();
        
            // Eliminar relación marcador-lista (para cada lista del usuario)
            $markerListIds = $markerLists->pluck('id');
            MarkerListMarkers::whereIn('marker_list_id', $markerListIds)->delete();
        
            // Eliminar las listas de marcadores
            MarkerList::whereIn('id', $markerListIds)->delete();
        
            // Eliminar el usuario
            $user->delete();
        
            return response()->noContent();
        } catch (\Exception $e) {
            return response()->json(['message'=>$e], 404);
        }
        
    }
}