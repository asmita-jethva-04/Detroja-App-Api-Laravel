<?php
namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\History;
use Dotenv\Exception\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $categories = Category::where('is_delete', 0)->where('status', 0)->get();

        if ($categories->isEmpty()) {
            return response()->json([
                'status'  => false,
                'message' => 'No category found.',
                'data'    => null,

            ]);
        }

        $category = $categories->map(function ($category) {
            return [
                'id'   => $category->id,
                'name' => $category->name,
                'img'  => env('APP_URL') . '/categories/' . $category->img,
            ];
        });

        return response()->json([
            'status'  => true,
            'message' => 'Data show successfully',
            'data'    => $category,
        ]);

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
                    'img'  => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                ], [
                    'name.required' => 'The name field is required.',
                    'img.required'  => 'The image field is required.',
                ]);
            } catch (ValidationException $e) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Validation failed',
                    'errors'  => $e->errors(),
                ], 422);
            }

            // Store the image in the 'public/categories' directory
            $imageName = time() . '.' . $request->img->getClientOriginalExtension();
            $imagePath = $request->img->move(public_path('categories'), $imageName);

            // Create the category with the image path
            $category = Category::create([
                'name' => $request->name,
                'img'  => $imageName,
            ]);

            // Generate the public URL of the stored image
            // $imageUrl = asset('categories/' . $category->img);
            // $imageUrl = env('APP_URL') . '/app/public/' . $category->img;

            return response()->json([
                'status'  => true,
                'message' => 'Category created successfully.',
                'data'    => [
                    'id'   => $category->id,
                    'name' => $category->name,
                    'img'  => env('APP_URL') . '/categories/' . $category->img,
                ],
            ]);
        }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        $id = $request->id;

        $category = Category::where('is_delete', 0)->where('status', 0)->find($id);

        if (! $category) {
            return response()->json([
                'status'  => false,
                'message' => 'Category not found.',
            ]);
        } else {
            return response()->json([
                'status'  => true,
                'message' => 'Category found.',
                'data'    => [
                    'id'   => $category->id,
                    'name' => $category->name,
                    'img'  => env('APP_URL') . '/categories/' . $category->img,
                ],
            ]);
        }

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {

        $category = Category::where('is_delete', 0)->where('status', 0)->find($request->id);

        if (! $category) {
            return response()->json([
                'status'  => false,
                'message' => 'Please enter valid id.',
                'data'    => null,

            ]);
        }

        $old_data = [
            'id'         => $category->id,
            'name'       => $category->name,
            'img'        => env('APP_URL') . '/categories/' . $category->img,
            'status'     => $category->status,
            'is_delete'  => $category->is_delete,
            'created_at' => $category->created_at,
            'updated_at' => $category->updated_at,
        ];

        $json_old_data = json_encode($old_data);

        // return response()->json([
        //             'status' => false,
        //             'data' => $old_data,
        //         ]);

        if (! $category) {
            return response()->json([
                'status'  => false,
                'message' => 'Category not found.',
            ]);
        }

        if ($request->name) {
            $category->name = $request->name;

        }

        if ($request->img) {

            // $path =  storage_path() . '/app/public/' . $category->img ;

            // if(file_exists($path)){
            //     unlink($path);
            // }
             // Store the image in the 'public/categories' directory
             $imageName = time() . '.' . $request->img->getClientOriginalExtension();
             $imagePath = $request->img->move(public_path('categories'), $imageName);
 
            $category->img = $imageName;

        }
        $category->save();

        $json_new_data = json_encode($category);

        // this for history

        $user = Auth::user();

        $history = History::create([
            'name'     => $user->name,
            'old_data' => $json_old_data,
            'new_data' => $json_new_data,
        ]);

        // end history

        return response()->json([
            'status'  => true,
            'message' => 'Category updated successfully',
            'data'    => [
                'id'   => $category->id,
                'name' => $category->name,
                'img'  => env('APP_URL') . '/categories/' . $category->img,
            ],
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete(Request $request)
    {
        $id       = $request->id;
        $category = Category::where('id', $id)->first();

        // $path =  storage_path() . '/app/public/' . $category->img ;

        // if(file_exists($path)){
        //     unlink($path);
        // }

        $old_data = [
            'id'         => $category->id,
            'name'       => $category->name,
            'img'        => env('APP_URL') . '/categories/' . $category->img,
            'status'     => $category->status,
            'is_delete'  => $category->is_delete,
            'created_at' => $category->created_at,
            'updated_at' => $category->updated_at,
        ];
        $json_old_data = json_encode($old_data);

        if (! $category) {
            return response()->json(['success' => false, 'message' => 'category not found', 'data' => null], 404);
        }

        $category->update(['is_delete' => 1]); // Mark as deleted

        $json_new_data = json_encode($category);

        // this for history

        $user = Auth::user();

        $history = History::create([
            'name'     => $user->name,
            'old_data' => $json_old_data,
            'new_data' => $json_new_data,
        ]);

        // end history

        return response()->json([
            'status'  => true,
            'message' => 'Category deleted successfully.',
        ], 200);
    }
}
