<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Employee;
use Illuminate\Support\Str;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string',
            'division_name' => 'nullable|string',
            'perPage' => 'nullable|integer|min:1',
            'page' => 'nullable|integer|min:1',
            'sortBy' => 'nullable|string|in:id,name,phone,position,division_name',
            'sortDirection' => 'nullable|string|in:asc,desc',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data tidak valid',
                'errors' => $validator->errors(),
            ], 422);
        }

        $name = $request->input('name');
        $divisionName = $request->input('division_name');
        $perPage = $request->input('perPage', 10);
        $sortBy = $request->input('sortBy', 'name');
        $sortDirection = $request->input('sortDirection', 'asc');

        $query = Employee::query()
            ->join('divisions', 'employees.division_id', '=', 'divisions.id')
            ->select('employees.*', 'divisions.name as division_name');

        if ($name) {
            $query->where(function ($query) use ($name) {
                $query->where('employees.name', 'like', "%$name%")
                    ->orWhere('employees.phone', 'like', "%$name%");
            });
        }

        if ($divisionName) {
            $query->where('divisions.name', 'like', "%$divisionName%");
        }

        $employees = $query->paginate($perPage);

        $formattedEmployees = $employees->map(function ($employee) {
            return [
                'id' => $employee->id,
                'image' => asset($employee->image),
                'name' => $employee->name,
                'phone' => $employee->phone,
                'division' => [
                    'id' => $employee->division_id,
                    'name' => $employee->division_name,
                ],
                'position' => $employee->position,
            ];
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Data retrieved successfully',
            'data' => [
                'employees' => $formattedEmployees,
            ],
            'pagination' => [
                'current_page' => $employees->currentPage(),
                'per_page' => $employees->perPage(),
                'total' => $employees->total(),
                'last_page' => $employees->lastPage(),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'name' => 'required|string',
            'phone' => 'required|string',
            'division_id' => 'required|uuid|exists:divisions,id',
            'position' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data yang dikirim tidak valid.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('images', 'public');
        }

        Employee::create([
            'id' => Str::uuid(),
            'image' => $imagePath,
            'name' => $request->name,
            'phone' => $request->phone,
            'division_id' => $request->division_id,
            'position' => $request->position,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Tambah Data Karyawan Sukses.',
        ]);
    }

    public function update(Request $request, $uuid)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'division' => 'nullable|uuid|exists:divisions,id',
            'position' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data tidak valid',
                'errors' => $validator->errors(),
            ], 422);
        }

        $employee = Employee::where('id', $uuid)->first();

        if (!$employee) {
            return response()->json([
                'status' => 'error',
                'message' => 'Karyawan tidak ditemukan',
            ], 404);
        }

        $employee->name = $request->input('name', $employee->name);
        $employee->phone = $request->input('phone', $employee->phone);
        $employee->position = $request->input('position', $employee->position);

        if ($request->hasFile('image')) {
            if ($employee->image && file_exists(public_path($employee->image))) {
                unlink(public_path($employee->image));
            }

            $image = $request->file('image');
            $imagePath = $image->store('images', 'public');
            $employee->image = $imagePath;
        }

        if ($request->has('division_id')) {
            $employee->division_id = $request->input('division_id');
        }

        $employee->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Data karyawan berhasil diperbarui',
        ]);
    }

    public function destroy($uuid)
    {
        $employee = Employee::where('id', $uuid)->first();

        if (!$employee) {
            return response()->json([
                'status' => 'error',
                'message' => 'Karyawan tidak ditemukan',
            ], 404);
        }

        if ($employee->image && file_exists(public_path($employee->image))) {
            unlink(public_path($employee->image));
        }

        $employee->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Data karyawan berhasil dihapus',
        ]);
    }
}
