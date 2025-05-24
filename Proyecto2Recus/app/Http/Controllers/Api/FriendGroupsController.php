<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Friend;
use Illuminate\Http\Request;
use App\Models\FriendGroup;
use App\Models\FriendGroupFriends;

class FriendGroupsController extends Controller
{
    //Creates a group of friends
    public function createGroup(Request $request) {
        $request->validate([
            'name' => 'required|string',
        ]);

        if(FriendGroup::where('name', $request->name)->exists()) {
            return response()->json(['message' => 'Group with the same name already exists', 'type' => 'bad'], 200);
        }
        
        FriendGroup::create([
            'name' => $request->name,
            'owner_user_id' => auth()->id(),
        ]);

        return response()->json(['message' => 'Group created', 'type' => 'good'], 201);

    }

    public function dropGroup(Request $request) {
        try {
            $request->validate([
                'id_group' => 'required|int',
            ]);
    
            $group = FriendGroup::find($request->id_group);
    
            if (!$group) {
                return response()->json(['message' => 'This Group doesn\'t exist', 'type' => 'bad']);
            }
    
            $group->delete();
    
            return response()->json(['message' => 'Group deleted', 'type' => 'good']);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th, 'type' => 'warn']);
        }
    }

    //Show auth user groups
    public function showMyGroups(Request $request) {

        $result = FriendGroup::where("owner_user_id", auth()->id())->get();

        return response()->json(
            $result
        );
    }

    public function showJoinedGroups(Request $request) {
        $request = "Not working right now";

        return response()->json(
            $request
        );
    }

    //Añadir un amigo a un grupo
    public function addToGroup(Request $request) {
        $request->validate([
            'id_group' => 'required|int|exists:friend_groups,id',
            'id_target_user' => 'required|int',
        ]);

        $group = FriendGroup::find($request->id_group);

        if (!$group) {
            return response()->json(['message' => 'This group doesn\'t exist'], 404);
        }

        if (auth()->id() != $group->owner_user_id) {
            return response()->json(['message' => 'You are not the owner of this group'], 403);
        }

        $exists = FriendGroupFriends::where('id_friend', $request->id_target_user)
            ->where('friend_group_id', $request->id_group)
            ->exists();

        if (!$exists) {
            $group->friends()->attach($request->id_target_user);

            return response()->json(['message' => 'User added to the group', 'type' => 'good'], 201);
        }

        return response()->json(['message' => 'This User is already in this group', 'type' => 'bad'], 200);
    }

    public function showPeopleInGroup(Request $request) {
        $request->validate([
            'id_group' => 'required|int'
        ]);

        $group = FriendGroup::find($request->id_group);

        if (!$group) {
            return response()->json(['message' => 'This group doesn\'t exist'], 404);
        }
        
        if (auth()->id() != $group->owner_user_id) {
            return response()->json(['message' => 'You are not the owner of this group'], 403);
        }
        
        $users = $group->friends()->select('username', 'name', 'email', 'users.id')->get();

        return response()->json([
            'group' => $group->name,
            'users' => $users
        ], 200);
    }

    //Kicks a user from a group
    public function kickFromGroup(Request $request) {
        $request->validate([
            'id_user' => 'required|int',
            'id_group' => 'required|int'
        ]);
    
        $group = FriendGroup::where('id', $request->id_group)->first();
        if (!$group || auth()->id() != $group->owner_user_id) {
            return response()->json([
                'message' => 'You are not the owner of this group'
            ], 403);
        }
    
        $relation = FriendGroupFriends::where('id_friend', $request->id_user)
            ->where('friend_group_id', $request->id_group)
            ->first();
    
        if (!$relation) {
            return response()->json([
                'message' => 'This user is not in this group'
            ], 404);
        }
    
        $relation->delete();
    
        return response()->json([
            'message' => 'Friend Kicked',
            'type' => 'good'
        ], 200);
    }    

}
