<?php

namespace App\Http\Controllers;

use App\Models\Directory;
use App\Models\History;
use Dotenv\Exception\ValidationException;
// use Illuminate\Container\Attributes\Auth;
use Illuminate\Support\Facades\Auth;

use App\Models\User;
use Illuminate\Http\Request;

class DirectoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $directory = Directory::where('is_delete', 0)->where('status', 0)->get();

        if ($directory->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No directory found.',
                'data' => null

            ]);
        }

        $directoryData = $directory->map(function ($directory) {
            return [
                'id' => $directory->id,
                'name' => $directory->name,
                'relations' => $directory->relations,
                'age' => $directory->age,
                'surname' => $directory->surname,
                'qualification' => $directory->qualification,
                'business' => $directory->business,
                'marital_status' => $directory->marital_status,
                'home_country' => $directory->home_country,
                'village' => $directory->village,
                'current_address' => $directory->current_address,
                'bussiness_address' => $directory->bussiness_address,
                'user_id' => $directory->user_id,
                'child_id' => $directory->child_id,
            ];
        });

        return response()->json([
            'status' => true,
            'message' => 'data get successfully',
            'data' => $directoryData
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
                'relations' => 'required',
                'age' => 'required',
                'surname' => 'required',
                // 'qualification' => 'required',
                // 'business' => 'required',
                'marital_status' => 'required',
                'home_country' => 'required',
                'village' => 'required',
                // 'current_address' => 'required',
                // 'bussiness_address' =>  'required',
                'user_id' => 'required',
                'village_id' => 'required',
                // 'child_id' => 'required',
                // 'status' => 'required|in:0,1',
            ], [
                'name.required' => 'The name field is required.',
                'relations.required' => 'The relations field is required.',
                'age.required' => 'The age field is required.',
                'surname.required' => 'The surname field is required.',
                // 'qualification.required' => 'The qualification field is required.',
                // 'business.required' => 'The business field is required.',
                'marital_status.required' => 'The marital_status field is required.',
                'village.required' => 'The village field is required.',
                // 'current_address.required' => 'The current address field is required.',
                // 'bussiness_address.required' => 'The bussiness address field is required.',
                'user_id.required' => 'The user id field is required.',
                'village_id.required' => 'The village id field is required.',
                // 'child_id.required' => 'The child id field is required.',
                // 'status.required' => 'The status field is required.',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        }


        if($request->child_id != 0){
            $child_id = $request->child_id;
        }else{
            $child_id=0;
        }

        if($request->qualification != NULL){
            $qualification = $request->qualification;
        }else{
            $qualification=" ";
        }

        if($request->business != NULL){
            $bussiness = $request->business;
        }else{
            $bussiness=" ";
        }

        if($request->current_address != NULL){
            $current_address = $request->current_address;
        }else{
            $current_address=" ";
        }

        if($request->bussiness_address != NULL){
            $bussiness_address = $request->bussiness_address;
        }else{
            $bussiness_address=" ";
        }

        $directory = Directory::create([
            'name' => $request->name,
            'relations' => $request->relations,
            'age' => $request->age,
            'surname' => $request->surname,
            'qualification' => $qualification,
            'business' => $bussiness,
            'marital_status' => $request->marital_status,
            'home_country' => $request->home_country,
            'village' => $request->village,
            'current_address' => $current_address,
            'bussiness_address' => $bussiness_address,
            'user_id' =>$request->user_id,
            'child_id' => $child_id,
            'village_id' => $request->village_id,

        ]);

        return response()->json([
            'status' => true,
            'message' => 'directory created successfully.',
            'data' => [
                'id' => $directory->id,
                'name' => $directory->name,
                'relations' => $directory->relations,
                'age' => $directory->age,
                'surname' => $directory->surname,
                'qualification' => $directory->qualification,
                'business' => $directory->business,
                'marital_status' => $directory->marital_status,
                'home_country' => $directory->home_country,
                'village' => $directory->village,
                'current_address' => $directory->current_address,
                'bussiness_address' => $directory->bussiness_address,
                'user_id' =>$directory->user_id,
                'child_id' => $directory->child_id,               
                'village_id' => $directory->village_id,

            ],
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        $id = $request->id;
        $directory = Directory::where('is_delete', 0)->where('status', 0)->find($id);

   
        if (!$directory) {
            return response()->json(['success' => false, 'message' => 'directory not found'], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Data show successfully',
            'data' => [
                'id' => $directory->id,
                'name' => $directory->name,
                'relations' => $directory->relations,
                'age' => $directory->age,
                'surname' => $directory->surname,
                'qualification' => $directory->qualification,
                'business' => $directory->business,
                'marital_status' => $directory->marital_status,
                'home_country' => $directory->home_country,
                'village' => $directory->village,
                'current_address' => $directory->current_address,
                'bussiness_address' => $directory->bussiness_address,
                'user_id' => $directory->user_id,
                'child_id' => $directory->child_id,
                'child_id_count' => Directory::where('child_id', $directory->id)->count(),
                'status' => $directory->status == 0 ? 'active' : 'deactive',
            ],
        ]);
    }


    public function childid(Request $request){
        // $id = $request->child_id;
        $directorys = Directory::where('child_id',$request->child_id)->where('is_delete', 0)->get();


        $directoryData = $directorys->map(function ($directory) {
            return [
                'id' => $directory->id,
                'name' => $directory->name,
                'relations' => $directory->relations,
                'age' => $directory->age,
                'surname' => $directory->surname,
                'qualification' => $directory->qualification,
                'business' => $directory->business,
                'marital_status' => $directory->marital_status,
                'home_country' => $directory->home_country,
                'village' => $directory->village,
                'current_address' => $directory->current_address,
                'bussiness_address' => $directory->bussiness_address,
                'user_id' => $directory->user_id,
                'user_id' => $directory->user_id,
                'child_id' => $directory->child_id,
            ];
        });

   
        if (!$directorys) {
            return response()->json(['success' => false, 'message' => 'directory not found'], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Data show successfully',
            'data' => $directoryData,
        ]);
    }


    
    public function edit(Directory $directory)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Directory $directory)
    {
        $id = $request->id;
        $request->validate([
            'name' => 'required',
            'relations' => 'required',
            'age' => 'required',
            'surname' => 'required',
            // 'qualification' => 'required',
            // 'business' => 'required',
            'marital_status' => 'required',
            'home_country' => 'required',
            'village' => 'required',
            // 'current_address' => 'required',
            // 'bussiness_address' => 'required',
            'user_id' => 'required',
            'status' => 'sometimes|in:0,1',
        ]);

        $directory = Directory::where('id', $id)->where('is_delete', 0)->first();

        if (!$directory) {
            return response()->json(['success' => false, 'message' => 'directory not found '], 404);
        }

        if($request->qualification != NULL){
            $qualification = $request->qualification;
        }else{
            $qualification=" ";
        }

        if($request->business != NULL){
            $bussiness = $request->business;
        }else{
            $bussiness=" ";
        }

        if($request->current_address != NULL){
            $current_address = $request->current_address;
        }else{
            $current_address=" ";
        }

        if($request->bussiness_address != NULL){
            $bussiness_address = $request->bussiness_address;
        }else{
            $bussiness_address=" ";
        }
 
        $old_data = [
            'id' => $directory->id,
            'name' =>  $directory->name,
            'relations' => $directory->relations,
            'age' => $directory->age,
            'surname' => $directory->surname,
            'qualification' => $directory->qualification,
            'business' => $directory->business,
            'marital_status' => $directory->marital_status,
            'home_country' => $directory->home_country,
            'village' => $directory->village,
            'current_address' => $directory->current_address,
            'bussiness_address' => $directory->bussiness_address,
            'user_id' => $directory->user_id,
            'child_id' => $directory->child_id,
            'status' => $directory->status,
            'is_delete' => $directory->is_delete,
            'created_at' => $directory->created_at ,
            'updated_at' => $directory->updated_at,
        ] ;
        $json_old_data = json_encode($old_data);

        $directory->update($request->only(['name','relations','age','surname','qualification','business','marital_status','home_country','village','current_address','bussiness_address','user_id','child_id','status']));

        $json_new_data = json_encode($directory);


        // this for history  


        $user = Auth::user();

        // if($request->child_id != 0){
        //     $child_id = $request->child_id;
        // }else{
        //     $child_id=0;
        // }

        $history = History::create([
            'name' => $user->name,
            'relations' => $user->relations,
            'age' => $user->age,
            'surname' => $user->surname,
            'qualification' => $qualification,
            'business' => $bussiness,
            'marital_status' => $user->marital_status,
            'home_country' => $user->home_country,
            'village' => $user->village,
            'current_address' => $current_address,
            'bussiness_address' => $bussiness_address,
            'user_id' => $user->user_id,
            'child_id' => $user->child_id,
            'old_data' =>  $json_old_data ,
            'new_data' => $json_new_data ,
        ]);
        // end history

        return response()->json([
            'status' => true,
            'message' => 'Directory updated successfully.',
            'data' => [
                'id' => $directory->id,
                'name' => $directory->name,
                'relations' => $directory->relations,
                'age' => $directory->age,
                'surname' => $directory->surname,
                'qualification' => $directory->qualification,
                'business' => $directory->business,
                'marital_status' => $directory->marital_status,
                'home_country' => $directory->home_country,
                'village' => $directory->village,
                'current_address' => $directory->current_address,
                'bussiness_address' => $directory->bussiness_address,
                'user_id' => $directory->user_id,
                'child_id' => $directory->child_id,
                'status' => $directory->status == 0 ? 'active' : 'deactive',
                'updated_at' => $directory->updated_at,
            ],
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete(Request $request)
    {
        $id = $request->id;
        $directory = Directory::where('id',$id)->first();

        $old_data = [
            'id' => $directory->id,
            'name' =>  $directory->name,
            'relations' => $directory->relations,
            'age' => $directory->age,
            'surname' => $directory->surname,
            'qualification' => $directory->qualification,
            'business' => $directory->business,
            'marital_status' => $directory->marital_status,
            'home_country' => $directory->home_country,
            'village' => $directory->village,
            'current_address' => $directory->current_address,
            'bussiness_address' => $directory->bussiness_address,
            'user_id' => $directory->user_id,
            'child_id' => $directory->child_id,
            'status' => $directory->status,
            'is_delete' => $directory->is_delete,
            'created_at' => $directory->created_at ,
            'updated_at' => $directory->updated_at,
        ] ;
        $json_old_data = json_encode($old_data);


        if (!$directory) {
            return response()->json(['success' => false, 'message' => 'directory not found','data'=>NULL], 404);
        }

        $directory->update(['is_delete' => 1]); // Mark as deleted

        $json_new_data = json_encode($directory);

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
            'message' => 'directory deleted successfully.',
        ],200);
    
    }


}
