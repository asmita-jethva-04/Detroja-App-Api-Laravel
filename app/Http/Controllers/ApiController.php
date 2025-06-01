<?php

namespace App\Http\Controllers;

use App\Models\History;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Directory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use League\CommonMark\Node\Query\OrExpr;

class ApiController extends Controller
{

    public function guest(){
      // Find the guest user (ID 1 in this case)
    $user = User::find(1);

    // If user exists, log them in and create a token
    if ($user) {
        Auth::login($user);  // Log in the user
        
        // Generate a token for the user
        $token = $user->createToken('auth_token')->plainTextToken;

        // Return a JSON response with the token
        return response()->json([
            'status' => true,
            'message' => 'Login successful',
            'token' => $token,
            'user_id' => 0,
        ], 200);  // HTTP 200 OK
    }

    // If the user with ID 1 doesn't exist, return an error
    return response()->json([
        'status' => false,
        'message' => 'User not found',
    ], 404);  // HTTP 404 Not Found

    }


    public function search(Request $request){
        $village = $request->village;
        $name = $request->name;
        

        // Fetch users based on village name
        if(!empty($village)){
            $users = Directory::where('is_delete', 0)->where('village', $village)->where('village','like','%'.$village.'%')->get();
            // $users = Directory::where('village', $village)->where('village','like','%'.$village.'%')->get();
        }
        
        if(!empty($name)){
            $users = Directory::where('is_delete', 0)->where('name', $name)->where('name','like','%'.$name.'%')->get();
        }

        if(empty($name) && empty($village)){
            $users = Directory::where('is_delete', 0)->get();
        }

        // $users = Directory::where('village', $village)->get();

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
                    'status' => $user->status == 0 ? 'active' : 'deactive',
                ];
            }),
        ]);
    }
    // User Registration
    public function register(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'phone' => 'required',
                'email' => 'required|email',
                'address' => 'required',
                'gender' => 'required',
                'password' => 'required|confirmed',
                'village_name' => 'required',
                                
            ], [
                'name.required' => 'The name field is required.',
                'phone.required' => 'The phone field is required.',
                'email.required' => 'The email field is required.',
                'address.required' => 'The address field is required.',
                'gender.required' => 'The gender field is required.',
                'password.required' => 'The password field is required.',
                'village_name.required' => 'The village name field is required.',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'gender' => $request->gender,
            'address' => $request->address,
            'password' => Hash::make($request->password),
            'village_name' => $request->village_name,
        ]);

        return response()->json([
            'message' => 'User registered successfully',
            'token' => $user->createToken('auth_token')->plainTextToken
        ], 201);
    }

    // User Login
    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|string|email',
                'password' => 'required|string',
            ], [
                'email.required' => 'The email field is required.',
                'email.email' => 'Please enter a valid email address.',
                'password.required' => 'The password field is required.',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        }

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid credentials'
            ], 401);
        }

        $user = Auth::user();

        return response()->json([
            'status' => true,
            'user_id' => $user->id,
            'message' => 'Login successful',
            'token' => $user->createToken('auth_token')->plainTextToken
        ], 200); // HTTP 200 OK

    }

    // Get Authenticated User
    public function index(Request $request)
    {
        // $name = $request->id;
        $users = User::where('is_delete', 0)->where('status', 0)->get();

        // $village_name = $request->village_name;

        // $users = User::where('is_delete', 0)->where('status', 0)->where('child_id', 0)->where('village_name', $village_name)->get();

        if ($users->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No users found.'
            ]);
        }

        $usersData = $users->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'gender' => $user->gender,
                'address' => $user->address,
                'village_name' => $user->village_name,

            ];
        });

        return response()->json([
            'status' => true,
            'message' => 'data get successfully',
            'data' => $usersData
        ]);
    }

    public function show(Request $request){
        $id = $request->id;

        $user = User::where('is_delete', 0)->where('status', 0)->find($id);

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found.'
            ]);
        }
        else{
            return response()->json([
                'status' => true,
                'message' => 'User found.',
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'gender' => $user->gender,
                    'address' => $user->address,
                    'village_name' => $user->village_name,
                ]
            ]);
        }
    }

    public function update(Request $request){
        $id = $request->id;

        $user = User::where('is_delete', 0)->where('status', 0)->find($id);

        $old_data = [
            'id' => $user->id,
            'name' =>  $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'gender' => $user->gender,
            'address' => $user->address,
            'village_name' => $user->village_name,
            'status' => $user->status,
            'is_delete' => $user->is_delete,
            'created_at' => $user->created_at ,
            'updated_at' => $user->updated_at,
        ] ;


        $json_old_data = json_encode($old_data);

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found.'
            ]);
        }

        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->gender = $request->gender;
        $user->address = $request->address;
        $user->village_name = $request->village_name;

        $user->save();

        $json_new_data = json_encode($user);


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
            'message' => 'User updated successfully',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'gender' => $user->gender,
                'address' => $user->address,
                'village_name' => $user->village_name,
            ]
        ]);
    }

    public function delete(Request $request){
        $id = $request->id;
        $user = User::where('id',$id)->first();

        $old_data = [
            'id' => $user->id,
            'name' =>  $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'gender' => $user->gender,
            'address' => $user->address,
            'village_name' => $user->village_name,
            'status' => $user->status,
            'is_delete' => $user->is_delete,
            'created_at' => $user->created_at ,
            'updated_at' => $user->updated_at,
        ] ;


        $json_old_data = json_encode($old_data);

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'user not found','data'=>NULL], 404);
        }

        $user->update(['is_delete' => 1]); // Mark as deleted

        $json_new_data = json_encode($user);


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
            'message' => 'user deleted successfully.',
        ],200);

    }

    // Logout User
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }
}
