<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FriendController extends Controller
{
    // Mostrar tabla intermedia completa
    public function index()
    {
        $friends = DB::table('friend_user')->get();
        return response()->json($friends, 200);
    }

    // Crear amistad mutua
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'friend_id' => 'required|exists:users,id',
        ]);

        $userId = $request->user_id;
        $friendId = $request->friend_id;

        if ($userId == $friendId) {
            return response()->json(['message' => 'You can’t be your own friend'], 400);
        }

        $alreadyFriends = DB::table('friend_user')->where(function ($query) use ($userId, $friendId) {
            $query->where('user_id', $userId)->where('friend_id', $friendId);
        })->orWhere(function ($query) use ($userId, $friendId) {
            $query->where('user_id', $friendId)->where('friend_id', $userId);
        })->exists();

        if ($alreadyFriends) {
            return response()->json(['message' => 'Already friends'], 200);
        }

        DB::table('friend_user')->insert([
            ['user_id' => $userId, 'friend_id' => $friendId],
            ['user_id' => $friendId, 'friend_id' => $userId],
        ]);

        return response()->json(['message' => 'Friendship created'], 201);
    }

    // Mostrar una amistad individual (dummy)
    public function show($id)
    {
        return response()->json([]);
    }

    // Actualizar amistad (dummy)
    public function update(Request $request, $id)
    {
        return response()->json([]);
    }

    // Eliminar amistad desde sender
    public function destroyFriendRequestAsSender(Request $request)
    {
        return response()->json([]);
    }

    // Eliminar amistad desde receiver
    public function destroyFriendRequestAsReciver(Request $request)
    {
        return response()->json([]);
    }

    // Mostrar usuarios
    public function showUsers(Request $request)
    {
        $search = $request->query('search');
        $query = User::query();

        if ($search) {
            $query->where('name', 'like', "%$search%");
        }

        $users = $query->orderBy('name', 'asc')->get(['id', 'name', 'username', 'last_lng', 'last_lat']);

        foreach ($users as $user) {
            $media = $user->getFirstMedia('users');
            $user->media_url = $media ? $media->getUrl() : null;
        }

        return response()->json($users);
    }

    // Mostrar solicitudes recibidas (dummy)
    public function ShowrequestsRecived(Request $request)
    {
        return response()->json([]);
    }

    // Mostrar solicitudes enviadas (dummy)
    public function ShowrequestsSent(Request $request)
    {
        return response()->json([]);
    }

    // Mostrar todos los amigos reales
    public function ShowAllFriends(Request $request)
    {
        $userId = $request->query('user_id');
        $user = User::findOrFail($userId);
        $friends = $user->friends()->get();

        return response()->json($friends);
    }

    // Obtener IDs de usuarios con los que ya hay amistad
    public function GetUsersWithFriendRequests(Request $request)
    {
        $userId = auth()->id();

        $friends = DB::table('friend_user')
            ->where('user_id', $userId)
            ->pluck('friend_id');

        return response()->json($friends);
    }

    // Ver estado de la solicitud de amistad
    public function getRequestStatus(Request $request)
    {
        $userId = auth()->id();
        $friendId = $request->query('friend_id');

        if (!$friendId) {
            return response()->json(['error' => 'friend_id is required'], 400);
        }

        $areFriends = DB::table('friend_user')
            ->where('user_id', $userId)
            ->where('friend_id', $friendId)
            ->exists();

        return response()->json(['value' => $areFriends], 200);
    }


    // Eliminar amistad mutua
    public function deleteFriend(Request $request)
    {
        $request->validate([
            'friend_id' => 'required|exists:users,id',
        ]);

        $userId = auth()->id();
        $friendId = $request->friend_id;

        DB::table('friend_user')->where(function ($query) use ($userId, $friendId) {
            $query->where('user_id', $userId)->where('friend_id', $friendId);
        })->orWhere(function ($query) use ($userId, $friendId) {
            $query->where('user_id', $friendId)->where('friend_id', $userId);
        })->delete();

        return response()->json(['message' => 'Friendship deleted'], 200);
    }

    // Aceptar amistad (dummy)
    public function acceptFriend(Request $request)
    {
        return response()->json([]);
    }
}
