<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Friend;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class FriendController extends Controller
{
    /*
     * Show list of all friends (Index)
     */
    public function index()
    {
        try {
            $friends = Friend::all();
            return response()->json($friends, 200);
        } catch (\Exception $e) {
            return response()->json(["status" => 500, "Error" => $e->getMessage()]);
        }
    }

    /*
     * Creates a new friend request (Store)
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_sender' => 'required|exists:users,id',
            'id_receiver' => 'required|exists:users,id',
        ]);

        if ($request->id_sender != auth()->id()) {
            return response()->json(['message' => 'Error you are not authorized to do this'], 401);
        }

        if ($request->id_sender == $request->id_receiver) {
            return response()->json(['message' => 'You cant be your own friend', 'type' => 'bad'], 200);
        }

        $existingFriendship = Friend::where(function ($query) use ($request) {
            $query->where('sender_user_id', $request->id_sender)
                ->where('reciver_user_id', $request->id_receiver);
        })
            ->orWhere(function ($query) use ($request) {
                $query->where('sender_user_id', $request->id_receiver)
                    ->where('reciver_user_id', $request->id_sender);
            })
            ->first();

        if ($existingFriendship) {
            return response()->json(['message' => 'Already sent', 'type' => 'bad'], 200);
        }

        $friendship = Friend::create([
            'sender_user_id' => $request->id_sender,
            'reciver_user_id' => $request->id_receiver,
            'request_status' => 0, // 0 = pending
        ]);

        return response()->json([
            'message' => 'Friend request sent',
            'type' => 'good',
            'friendship' => $friendship
        ], 201);
    }

    /*
     * Show a specific friendship (Show)
     */
    public function show($id)
    {
        try {
            $friendship = Friend::findOrFail($id);
            return response()->json($friendship, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(["status" => 404, "Error" => "Friendship not found"]);
        }
    }

    /*
     * Update a friend request status (Update) Accepts the friend request
     */
    public function update(Request $request, $id)
    {
        try {
            $friendship = Friend::findOrFail($id);
            $friendship->update($request->all());
            return response()->json(["friendship" => $friendship], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(["Error" => "Friendship not found"], 404);
        }
    }

    /*
     * Delete a friend or friend request (Destroy)
     */
    public function destroyFriendRequestAsSender(Request $request)
    {
        $request->validate([
            'id_sender' => 'required|exists:users,id',
            'id_receiver' => 'required|exists:users,id',
        ]);

        $idSender = $request->query('id_sender');
        $idReceiver = $request->query('id_receiver');

        if ($idSender != auth()->id()) {
            return response()->json(['message' => 'Error you are not authorized to do this'], 401);
        }

        if ($idSender == $idReceiver) {
            return response()->json(['message' => 'You cant be your own friend', 'type' => 'bad'], 200);
        }

        $query = Friend::where('sender_user_id', $idSender)
            ->where('reciver_user_id', $idReceiver)
            ->delete();

        return response()->json(['data' => $query], 200);
    }

    public function destroyFriendRequestAsReciver(Request $request)
    {
        $request->validate([
            'id_sender' => 'required|exists:users,id',
            'id_receiver' => 'required|exists:users,id',
        ]);

        $idSender = $request->query('id_sender');
        $idReceiver = $request->query('id_receiver');

        if ($idReceiver != auth()->id()) {
            return response()->json(['message' => 'Error you are not authorized to do this'], 401);
        }

        if ($idSender == $idReceiver) {
            return response()->json(['message' => 'You cant be your own friend', 'type' => 'bad'], 200);
        }

        $query = Friend::where('reciver_user_id', $idSender)
            ->where('sender_user_id', $idReceiver)
            ->delete();

        return response()->json(['data' => $query], 200);
    }



    /*
     Funciones personalizadas
    */

    //Shows all users that registred in the app
    public function showUsers(Request $request)
    {
        $search = $request->query('search');
        $query = User::query();

        if ($search) {
            $query->where('name', 'like', "%$search%");
        }

        $friends = $query->orderBy('name', 'asc')->get(['id', 'name', 'username', 'last_lng', 'last_lat']);

        foreach ($friends as $friend) {
            $media = $friend->getFirstMedia('users');
            $friend->media_url = $media ? $media->getUrl() : null;
        }

        return response()->json($friends);
    }


    //Only show friends that recived a request
    public function ShowrequestsRecived(Request $request)
    {
        $userId = auth()->user()->id;
        $user = User::findOrFail($userId);
        $friendRequests = $user->friendsReceived()->where('request_status', '!=', 1)->get();

        return response()->json($friendRequests);
    }

    //Only shows friends i send a request
    public function ShowrequestsSent(Request $request)
    {
        $userId = $request->query('user');
        $user = User::findOrFail($userId);
        $friendRequests = $user->friendsSent()->where('request_status', '!=', 1)->get();

        return response()->json($friendRequests);
    }


    //Shows all people that is in a friendship with you

    public function ShowAllFriends(Request $request)
    {
        $userId = $request->query('user_id');
        $user = User::findOrFail($userId);

        $friendsSent = $user->friendsSent()->where('request_status', '!=', 0)->get();
        $friendsReceived = $user->friendsReceived()->where('request_status', '!=', 0)->get();

        $allFriends = $friendsSent->merge($friendsReceived);

        $formattedFriends = $allFriends->map(function ($friend) use ($user) {
            return [
                'id' => $friend->id,
                'request_status' => $friend->request_status,
                'user' => $friend->sender_user_id == $user->id ? $friend->reciver : $friend->sender,
                'created_at' => $friend->created_at,
                'updated_at' => $friend->updated_at,
            ];
        });

        return response()->json($formattedFriends);
    }

    //Obtains friends that i send a request
    public function GetUsersWithFriendRequests(Request $request)
    {
        $userId = auth()->user()->id;

        $users = User::join('friends', 'users.id', '=', 'friends.reciver_user_id')
            ->where('friends.sender_user_id', $userId)
            ->select('users.id')
            ->get();

        return response()->json($users, 200);
    }

    public function getRequestStatus(Request $request)
    {
        $request->validate([
            'friend_id' => 'required|exists:users,id',
        ]);

        $userId = auth()->id();
        $friendId = $request->query('friend_id');

        $friendship = Friend::where(function ($query) use ($userId, $friendId) {
            $query->where('sender_user_id', $userId)
                ->where('reciver_user_id', $friendId);
        })
            ->first();

        if (!$friendship) {
            return response()->json([
                'value' => false,
                'error' => 'Friendship request not found',
            ]);
        }

        return response()->json([
            'value' => true,
            'message' => 'Friendship request found and sent',
        ]);
    }

    //Delete Friendship or friend request (friendship or friend request its the same in the bbdd)
    public function deleteFriend(Request $request)
    {
        $request->validate([
            'friend_id' => 'required',
        ]);

        $user = auth()->user();

        $friend = Friend::where(function ($query) use ($user) {
            $query->where('sender_user_id', $user->id)
                ->orWhere('reciver_user_id', $user->id);
        })->findOrFail($request->friend_id);

        $friend->delete();

        return response()->json([
            'message' => 'Friend deleted successfully',
        ], 200);
    }

    //Accepts a friend request
    public function acceptFriend(Request $request)
    {
        $friendship_id = $request->id;
        $friendship = Friend::find($friendship_id);

        if (!$friendship) {
            return response()->json(['error' => 'Friendship not found'], 404);
        }

        $friendship->request_status = 1;
        $friendship->save();

        return response()->json([
            'message' => 'Friend request accepted successfully',
            'friendship' => $friendship
        ]);
    }
}
