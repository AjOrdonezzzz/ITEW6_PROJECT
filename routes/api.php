<?php

use Illuminate\Support\Facades\Route;

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

Route::prefix('v1')->group(function () {

    // Dashboard Stats
    Route::get('dashboard/stats', [StudentController::class, 'stats']);
    Route::get('notifications', [NotificationController::class, 'index']);

    // Students
    Route::apiResource('students', StudentController::class);
    Route::get('students/{id}/violations', [StudentController::class, 'violations']);
    Route::get('students/{id}/subjects',   [StudentController::class, 'subjects']);
    Route::get('students/{id}/skills',     [StudentController::class, 'skills']);
    Route::get('students/{id}/awards',     [StudentController::class, 'awards']);

    // Guardians
    Route::apiResource('guardians', GuardianController::class);

    // Departments
    Route::apiResource('departments', DepartmentController::class);

    // Faculty
    Route::apiResource('faculty', FacultyController::class);
    Route::get('faculty/{id}/subjects',      [FacultyController::class, 'subjects']);
    Route::get('faculty/{id}/organizations', [FacultyController::class, 'organizations']);
    Route::get('faculty/{id}/sections',      [FacultyController::class, 'sections']);

    // Sections
    Route::apiResource('sections', SectionController::class);
    Route::get('sections/{id}/students', [SectionController::class, 'students']);

    // Subjects
    Route::apiResource('subjects', SubjectController::class);

    // Student Subjects (Grades)
    Route::apiResource('student-subjects', StudentSubjectController::class);

    // Academic Awards
    Route::apiResource('academic-awards', AcademicAwardController::class);

    // Organizations
    Route::apiResource('organizations', OrganizationController::class);

    // Student Organizations
    Route::apiResource('student-organizations', StudentOrganizationController::class);

    // Non Academic Activities
    Route::apiResource('non-academic-activities', NonAcademicActivityController::class);

    // Skills
    Route::apiResource('skills', SkillController::class);

    // Student Skills
    Route::apiResource('student-skills', StudentSkillController::class);

    // Violation Types
    Route::apiResource('violation-types', ViolationTypeController::class);

    // Student Violations
    Route::apiResource('student-violations', StudentViolationController::class);

    // Faculty Subjects
    Route::apiResource('faculty-subjects', FacultySubjectController::class);

    // Faculty Organizations
    Route::apiResource('faculty-organizations', FacultyOrganizationController::class);

});
