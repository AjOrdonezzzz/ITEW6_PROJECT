<?php

use Illuminate\Support\Facades\Route;
// Import your new Auth controller
use App\Http\Controllers\Auth\LoginController;

// Import your existing API controllers
use App\Http\Controllers\Api\StudentController;
use App\Http\Controllers\Api\GuardianController;
use App\Http\Controllers\Api\DepartmentController;
use App\Http\Controllers\Api\FacultyController;
use App\Http\Controllers\Api\SectionController;
use App\Http\Controllers\Api\SubjectController;
use App\Http\Controllers\Api\StudentSubjectController;
use App\Http\Controllers\Api\AcademicAwardController;
use App\Http\Controllers\Api\OrganizationController;
use App\Http\Controllers\Api\StudentOrganizationController;
use App\Http\Controllers\Api\NonAcademicActivityController;
use App\Http\Controllers\Api\SkillController;
use App\Http\Controllers\Api\StudentSkillController;
use App\Http\Controllers\Api\ViolationTypeController;
use App\Http\Controllers\Api\StudentViolationController;
use App\Http\Controllers\Api\FacultySubjectController;
use App\Http\Controllers\Api\FacultyOrganizationController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\ProfileController;

Route::get('/test', function () {
    return response()->json(['message' => 'Hello from API!']);
});

// The login route must be public so users can actually log in!
Route::post('/login', [LoginController::class, 'login']);


// We use 'auth:sanctum' to ensure only logged-in users with a token can enter

Route::middleware('auth:sanctum')->prefix('v1')->group(function () {

    Route::get('dashboard/stats', [StudentController::class, 'stats']);
    Route::get('notifications', [NotificationController::class, 'index']);

    Route::apiResource('students', StudentController::class);
    Route::get('students/{id}/violations', [StudentController::class, 'violations']);
    Route::get('students/{id}/subjects',   [StudentController::class, 'subjects']);
    Route::get('students/{id}/skills',     [StudentController::class, 'skills']);
    Route::get('students/{id}/awards',     [StudentController::class, 'awards']);

    Route::apiResource('faculty', FacultyController::class);
    Route::apiResource('departments', DepartmentController::class);
    Route::apiResource('sections', SectionController::class);
    Route::apiResource('subjects', SubjectController::class);
    

    Route::apiResource('guardians', GuardianController::class);
    Route::apiResource('student-subjects', StudentSubjectController::class);
    Route::apiResource('academic-awards', AcademicAwardController::class);
    Route::apiResource('organizations', OrganizationController::class);
    Route::apiResource('student-organizations', StudentOrganizationController::class);
    Route::apiResource('non-academic-activities', NonAcademicActivityController::class);


    Route::apiResource('skills', SkillController::class);
    Route::apiResource('student-skills', StudentSkillController::class);
    Route::apiResource('violation-types', ViolationTypeController::class);
    Route::apiResource('student-violations', StudentViolationController::class);


    Route::apiResource('faculty-subjects', FacultySubjectController::class);
    Route::apiResource('faculty-organizations', FacultyOrganizationController::class);
    
    Route::get('/user-profile', [ProfileController::class, 'show']);
    Route::put('/user-profile/update', [ProfileController::class, 'update']);

    Route::post('/logout', [LoginController::class, 'logout']);
});