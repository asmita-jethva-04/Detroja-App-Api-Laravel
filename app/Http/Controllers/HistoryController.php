<?php

namespace App\Http\Controllers;

use App\Models\History;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class HistoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $history = History::where('is_delete', 0)->where('status', 0)->get();

        if ($history->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No history found.',
                'data' => null

            ]);
        }

        $historyData = $history->map(function ($histories) {
            return [
                'id' => $histories->id,
                'name' => $histories->name,
                'old_data' => $histories->old_data,
                'new_data' => $histories->new_data,
            ];
        });

        return response()->json([
            'status' => true,
            'message' => 'data get successfully',
            'data' => $historyData,
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
        
    }

    /**
     * Display the specified resource.
     */
    public function show(History $history)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(History $history)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, History $history)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete(Request $request)
    {
        $id = $request->id;
        $history = History::where('id',$id)->first();

        $old_data = [
            'id' => $history->id,
            'name' =>  $history->name,
            'old_data' => $history->old_data,
            'new_data' => $history->new_data,
            'status' => $history->status,
            'is_delete' => $history->is_delete,
            'created_at' => $history->created_at ,
            'updated_at' => $history->updated_at,
        ] ;
        $json_old_data = json_encode($old_data);


        if (!$history) {
            return response()->json(['success' => false, 'message' => 'History not found','data'=>NULL], 404);
        }

        $history->update(['is_delete' => 1]); // Mark as deleted

        $json_new_data = json_encode($history);

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
            'message' => 'History deleted successfully.',
        ],200);
    }
}
