<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Friend;
use Illuminate\Http\Request;
use App\Models\FriendGroup;

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
    public function addToGroup(Request $request) 
    {
        $request->validate([
            'id_group' => 'required|int|exists:friend_groups,id',
            'id_target_user' => 'required|int|exists:users,id',
        ]);

        $group = FriendGroup::findOrFail($request->id_group);

        // Verify the current user is the group owner
        if (auth()->id() != $group->owner_user_id) {
            return response()->json([
                'message' => 'You are not the owner of this group',
                'type' => 'error'
            ], 403);
        }

        // Check if user already in group
        if (!$group->friends()->where('users.id', $request->id_target_user)->exists()) {
            $group->friends()->attach($request->id_target_user);

            return response()->json([
                'message' => 'User added to the group',
                'type' => 'success'
            ], 201);
        }

        return response()->json([
            'message' => 'User is already in this group',
            'type' => 'info'
        ], 200);
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
    public function kickFromGroup(Request $request) 
    {
        $request->validate([
            'id_user' => 'required|int|exists:users,id',
            'id_group' => 'required|int|exists:friend_groups,id'
        ]);

        $group = FriendGroup::findOrFail($request->id_group);

        if (auth()->id() != $group->owner_user_id) {
            return response()->json([
                'message' => 'You are not the owner of this group',
                'type' => 'error'
            ], 403);
        }

        $removed = $group->friends()->detach($request->id_user);

        if ($removed) {
            return response()->json([
                'message' => 'User removed from group',
                'type' => 'success'
            ], 200);
        }

        return response()->json([
            'message' => 'User was not in this group',
            'type' => 'info'
        ], 404);
    }

}
