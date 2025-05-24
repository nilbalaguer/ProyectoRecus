<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MarkerList;

class MarkerListController extends Controller
{
    //Show markers lists
    public function index(Request $request)
    {
        try {
            $user = auth()->id();

            $lists = MarkerList::where('owner_user_id', $user)->get();

            return response()->json($lists, 200);

        } catch (\Throwable $th) {
            return response()->json(["Error" => $th->getMessage()], 500);

        }
    }

    public function showAll()
    {
        try {
            $lists = MarkerList::query()->get();
            
            return response()->json($lists, 200);
            
        } catch (\Throwable $th) {
            return response()->json([
                "error" => "Error al obtener las listas",
                "details" => $th->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $list = MarkerList::where("id", $id)->first();

            if (!$list) {
                return response()->json(['message' => "Marker list not found with id: $id"], 404);
            }

            return response()->json($list, 200);

        } catch (\Throwable $th) {
            return response()->json(["Error" => $th->getMessage()], 500);
        }
    }

    //Creates a maker list
    public function store(Request $request)
    {
        try {
            $request->validate([
                "name" => "required|string",
                "emoji_identifier" => "required|int",
            ]);

            $user = auth()->id();

            if (!$user) {
                return response()->json(['message' => 'User no authenticated'], 403);
            }

            $exists = MarkerList::where('name', $request->name)->exists();

            if ($exists) {
                return response()->json(['message' => 'This List already exists'], 400);
            }

            $marker = MarkerList::create([
                'name' => $request->name,
                'emoji_identifier' => $request->emoji_identifier,
                'owner_user_id' => $user
            ]);

            return response()->json(["message" => "marker list created", "MarkerList" => $marker], 201);

        } catch (\Exception $e) {
            return response()->json(["Error" => $e->getMessage()], 500);

        }
    }

    //Deletes a marker list
    public function destroy($id)
    {
        try {
            $user = auth()->user();
            $list = MarkerList::find($id);

            if (!$list) {
                return response()->json(['message' => 'Marker list not found'], 404);
            }

            if (!$user->hasRole('admin') && $list->owner_user_id != $user->id) {
                return response()->json([
                    'message' => 'Unauthorized - You must be admin or the owner to delete this list'
                ], 404);
            }

            $list->delete();

            return response()->json(['message' => "Marker list deleted successfully"], 200);

        } catch (\Throwable $th) {
            return response()->json([
                "error" => "Error deleting marker list",
                "details" => $th->getMessage()
            ], 500);
        }
    }


    public function update($id, Request $request)
    {
        try {
            $request->validate([
                "name" => "required|string",
                "emoji_identifier" => "required|int"
            ]);

            $user = auth()->user();
            $list = MarkerList::find($id);

            if (!$list) {
                return response()->json(['message' => 'Marker list not found'], 404);
            }

            if (!$user->hasRole('admin') && $list->owner_user_id != $user->id) {
                return response()->json(['message' => 'You are not the owner of this list'], 401);
            }

            $list->update([
                "name" => $request->name,
                "emoji_identifier" => $request->emoji_identifier
            ]);

            return response()->json(['message' => 'Group updated'], 200);

        } catch (\Throwable $th) {
            //throw $th;

            return response()->json(["Error" => $th->getMessage()], 500);
        }
    }
}
