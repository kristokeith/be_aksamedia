<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Division;
use Illuminate\Support\Facades\Validator;

class DivisionController extends Controller
{
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data tidak valid',
                'errors' => $validator->errors(),
            ], 422);
        }

        $query = Division::query();

        if ($request->has('name') && !empty($request->name)) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        $divisions = $query->paginate(5);

        return response()->json([
            'status' => 'success',
            'message' => 'Ambil Data Divisi Sukses.',
            'data' => [
                'divisions' => $divisions->items(),
            ],
            'pagination' => [
                'current_page' => $divisions->currentPage(),
                'per_page' => $divisions->perPage(),
                'total' => $divisions->total(),
                'last_page' => $divisions->lastPage(),
            ],
        ]);
    }
}
