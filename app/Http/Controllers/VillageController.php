<?php

namespace App\Http\Controllers;

use App\Models\Directory;
use App\Models\History;
use App\Models\User;
use App\Models\Village;
use Dotenv\Exception\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class VillageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // public function index()
    // {
    //     $villages = Village::where('is_delete', 0) // Exclude soft-deleted records
    //         ->get(['id', 'name', 'status', 'created_at', 'updated_at']) // Send only required fields
    //         ->map(function ($village) {
    //             return [
    //                 'id' => $village->id,
    //                 'name' => $village->name,
    //                 'status' => $village->status == 0 ? 'active' : 'deactive',
    //             ];
    //         });

    //     return response()->json([
    //         'status' => true,
    //         'message' => 'Data show successfully',
    //         'data' => $villages
    //     ]);
    // }

    public function index()
    {

        $villages = Village::where('is_delete', 0)->where('status', 0)->get();

        if ($villages->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No villages found.',
                'data' => null

            ]);
        }

        $villageData = $villages->map(function ($village) {
            return [
                'id' => $village->id,
                'name' => $village->name,
            ];
        });

        return response()->json([
            'status' => true,
            'message' => 'data get successfully',
            'data' => $villageData
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
        // $request->validate([
        //     'name' => 'required|string|max:255',
        //     'status' => 'required|in:0,1',
        // ]);

        try {
            $request->validate([
                'name' => 'required|string|max:255',
                // 'status' => 'required|in:0,1',
            ], [
                'name.required' => 'The name field is required.',
                // 'status.required' => 'The status field is required.',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        }



        $village = Village::create([
            'name' => $request->name,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Village created successfully.',
            'data' => [
                'id' => $village->id,
                'name' => $village->name,
            ],
        ]);
    }

    /**
     * Display the specified resource.
     */
    // public function show(Request $request)
    // {
        // $id = $request->id;
        // $village = Village::where('is_delete', 0)->where('status', 0)->find($id);

   
        // if (!$village) {
        //     return response()->json(['success' => false, 'message' => 'Village not found'], 404);
        // }

    //     return response()->json([
    //         'status' => true,
    //         'message' => 'Data show successfully',
    //         'data' => [
    //             'id' => $village->id,
    //             'name' => $village->name,
    //             'status' => $village->status == 0 ? 'active' : 'deactive',
    //         ],
    //     ]);
    // }


    public function show(Request $request)
    {
        $villageName = $request->name;
        // $childId = $request->child_id;
    
        // Get records with the given village name and child_id = 0
        $users = Directory::where('is_delete', 0)
                    ->where('status', 0)
                    ->where('village', $villageName)
                    ->where('child_id', 0)
                    ->get();
    
        // Count records with the same child_id
        // $childCount = Directory::where('child_id', $childId)->count();
    
        // If no users found
        if ($users->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No users found for this village'
            ], 404);
        }
    
        // Return response with data and count
        return response()->json([
            'status' => true,
            'message' => 'Data retrieved successfully',
            // 'count' => $childCount,
            'data' => $users->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'relations' => $user->relations,
                    'age' => $user->age,
                    'surname' => $user->surname,
                    'qualification' => $user->qualification,
                    'business' => $user->business,
                    'marital_status' => $user->marital_status,
                    'home_country' => $user->home_country,
                    'village' => $user->village,
                    'bussiness_address' => $user->bussiness_address,
                    'current_address' => $user->current_address,
                    'user_id' => $user->user_id,
                    'child_id' => $user->child_id,
                    'child_id_count' => Directory::where('child_id', $user->id)->where('is_delete',0)->count(),
                    'status' => $user->status == 0 ? 'active' : 'deactive',
                ];
            }),
        ]);
    }
    
    

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Village $village)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Village $village)
    {
        $id = $request->id;
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'status' => 'sometimes|in:0,1',
        ]);

        $village = Village::where('id', $id)->where('is_delete', 0)->first();

        if (!$village) {
            return response()->json(['success' => false, 'message' => 'Village not found'], 404);
        }

        $old_data = [
            'id' => $village->id,
            'name' =>  $village->name,
            'status' => $village->status,
            'is_delete' => $village->is_delete,
            'created_at' => $village->created_at ,
            'updated_at' => $village->updated_at,
        ] ;
        $json_old_data = json_encode($old_data);

        $village->update($request->only(['name', 'status']));

        $json_new_data = json_encode($village);


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
            'message' => 'Village updated successfully.',
            'data' => [
                'id' => $village->id,
                'name' => $village->name,
                'status' => $village->status == 0 ? 'active' : 'deactive',
                'updated_at' => $village->updated_at,
            ],
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete(Request $request)
    {
        $id = $request->id;
        $village = Village::where('id',$id)->first();

        $old_data = [
            'id' => $village->id,
            'name' =>  $village->name,
            'status' => $village->status,
            'is_delete' => $village->is_delete,
            'created_at' => $village->created_at ,
            'updated_at' => $village->updated_at,
        ] ;
        $json_old_data = json_encode($old_data);


        if (!$village) {
            return response()->json(['success' => false, 'message' => 'Village not found','data'=>NULL], 404);
        }

        $village->update(['is_delete' => 1]); // Mark as deleted

        $json_new_data = json_encode($village);

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
            'message' => 'Village deleted successfully.',
        ],200);
    }


    public function search(Request $request) {
        $village = $request->name;
    
         // Fetch users based on village name
         if(!empty($village)){
            $users = Village::where('is_delete', 0)->where('status', 0)->where('name', 'like', '%' . $village . '%')->get();   
        }
        if(empty($village)){
            $users = Village::where('is_delete', 0)->where('status', 0)->get();   
        }



        $villages = Village::where('is_delete', 0)->where('status', 0)->get();


        // Validate village input
        // if (empty($village)) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Village name is required'
        //     ], 400);
        // }
    
       
        // Check if users exist
        if ($users->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No users found for this village'
            ], 404);
        }
    
        // Format response
        return response()->json([
            'status' => true,
            'message' => 'Data retrieved successfully',
            'data' => $users->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    // 'relations' => $user->relations,
                    // 'age' => $user->age,
                    // 'surname' => $user->surname,
                    // 'qualification' => $user->qualification,
                    // 'business' => $user->business,
                    // 'marital_status' => $user->marital_status,
                    // 'home_country' => $user->home_country,
                    // 'village' => $user->village,
                    // 'bussiness_address' => $user->bussiness_address,
                    // 'current_address' => $user->current_address,
                    // 'user_id' => $user->user_id,
                    // 'child_id' => $user->child_id,
                    'status' => $user->status == 0 ? 'active' : 'deactive',
                ];
            }),
        ]);
    }
    
}
