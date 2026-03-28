<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SectionController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Section::with(['adviser', 'students'])->get());
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'section_name' => 'required|string|max:50',
            'year_level'   => 'required|integer',
            'school_year'  => 'required|string|max:20',
            'adviser_id'   => 'required|exists:faculty,faculty_id',
        ]);

        $section = Section::create($validated);
        return response()->json($section->load('adviser'), 201);
    }

    public function show(int $id): JsonResponse
    {
        return response()->json(
            Section::with(['adviser', 'students'])->findOrFail($id)
        );
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $section = Section::findOrFail($id);

        $validated = $request->validate([
            'section_name' => 'sometimes|string|max:50',
            'year_level'   => 'sometimes|integer',
            'school_year'  => 'sometimes|string|max:20',
            'adviser_id'   => 'sometimes|exists:faculty,faculty_id',
        ]);

        $section->update($validated);
        return response()->json($section->load('adviser'));
    }

    public function destroy(int $id): JsonResponse
    {
        Section::findOrFail($id)->delete();
        return response()->json(['message' => 'Section deleted successfully']);
    }

    // GET /api/v1/sections/{id}/students
    public function students(int $id): JsonResponse
    {
        return response()->json(
            Section::with('students')->findOrFail($id)->students
        );
    }
}