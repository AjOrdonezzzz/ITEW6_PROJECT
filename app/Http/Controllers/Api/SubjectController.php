<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SubjectController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Subject::with('department')->get());
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'subject_code'  => 'required|string|max:20|unique:subjects',
            'subject_name'  => 'required|string|max:100',
            'department_id' => 'required|exists:departments,department_id',
        ]);

        $subject = Subject::create($validated);
        return response()->json($subject->load('department'), 201);
    }

    public function show(int $id): JsonResponse
    {
        return response()->json(
            Subject::with(['department', 'students', 'faculty'])->findOrFail($id)
        );
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $subject = Subject::findOrFail($id);

        $validated = $request->validate([
            'subject_code'  => 'sometimes|string|max:20|unique:subjects,subject_code,' . $id . ',subject_id',
            'subject_name'  => 'sometimes|string|max:100',
            'department_id' => 'sometimes|exists:departments,department_id',
        ]);

        $subject->update($validated);
        return response()->json($subject->load('department'));
    }

    public function destroy(int $id): JsonResponse
    {
        Subject::findOrFail($id)->delete();
        return response()->json(['message' => 'Subject deleted successfully']);
    }
}