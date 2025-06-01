<?php

namespace App\Http\Controllers;

use App\Models\History;
use App\Models\Relations;
use Dotenv\Exception\ValidationException;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class RelationsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $relation = Relations::where('is_delete', 0)->where('status', 0)->get();

        if ($relation->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No relations found.',
                'data' => null

            ]);
        }

        $RelationData = $relation->map(function ($relation) {
            return [
                'id' => $relation->id,
                'name' => $relation->name,
            ];
        });

        return response()->json([
            'status' => true,
            'message' => 'data get successfully',
            'data' => $RelationData
        ],200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
            ], [
                'name.required' => 'The name field is required.',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        }



        $relation = Relations::create([
            'name' => $request->name,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Relations created successfully.',
            'data' => [
                'id' => $relation->id,
                'name' => $relation->name,
            ],
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {

        $id = $request->id;
        $relations = Relations::where('is_delete', 0)->where('status', 0)->find($id);


        if (!$relations) {
            return response()->json([
                'status' => false,
                'message' => 'Relations not found.'
            ]);
        }
        else{
            return response()->json([
                'status' => true,
                'message' => 'Relations found.',
                'data' => [
                    'id' => $relations->id,
                    'name' => $relations->name,
                ]
            ]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Relations $relations)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Relations $relations)
    {
        $id = $request->id;

        $relation = Relations::where('id', $id)->where('is_delete', 0)->first();

        $old_data = [
            'id' => $relation->id,
            'name' =>  $relation->name,
            'status' => $relation->status,
            'is_delete' => $relation->is_delete,
            'created_at' => $relation->created_at ,
            'updated_at' => $relation->updated_at,
        ] ;


        $json_old_data = json_encode($old_data);

        if (!$relation) {
            return response()->json([
                'status' => false,
                'message' => 'Relation not found.'
            ]);
        }

        $relation->name = $request->name;

        $relation->save();

        $json_new_data = json_encode($relation);


        // this for history

        $user = Auth::user();

        $history = History::create([
            'name' => $user->name,
            'old_data' =>  $json_old_data ,
            'new_data' => $json_new_data ,
        ]);

        // end history


        return response()->json([
            'status' => true,
            'message' => 'Relation updated successfully',
            'data' => [
                'id' => $relation->id,
                'name' => $relation->name,
            ]
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete(Request $request)
    {
        $id = $request->id;
        $relations = Relations::where('id',$id)->first();

        $old_data = [
            'id' => $relations->id,
            'name' =>  $relations->name,
            'status' => $relations->status,
            'is_delete' => $relations->is_delete,
            'created_at' => $relations->created_at ,
            'updated_at' => $relations->updated_at,
        ] ;


        $json_old_data = json_encode($old_data);

        if (!$relations) {
            return response()->json(['success' => false, 'message' => 'relations not found','data'=>NULL], 404);
        }

        
        $relations->is_delete = 1; // Mark as deleted
        $relations->save();

        $json_new_data = json_encode($relations);


        // this for history

        $user = Auth::user();

        $history = History::create([
            'name' => $user->name,
            'old_data' =>  $json_old_data ,
            'new_data' => $json_new_data ,
        ]);

        // end history

        return response()->json([
            'status' => true,
            'message' => 'relations deleted successfully.',
        ],200);
    }
}
