<?php

namespace Database\Factories;

use App\Models\Subject;
use App\Models\Department;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Subject>
 */
class SubjectFactory extends Factory
{
    protected $model = Subject::class;

    public function definition(): array
    {
        $subjects = [
            ['code' => 'IT101', 'name' => 'Introduction to Computing'],
            ['code' => 'IT102', 'name' => 'Computer Programming 1'],
            ['code' => 'IT201', 'name' => 'Database Management Systems'],
            ['code' => 'IT202', 'name' => 'Networking 1'],
            ['code' => 'IT301', 'name' => 'Web Systems and Technologies'],
            ['code' => 'IT302', 'name' => 'Information Assurance and Security'],
            ['code' => 'IT401', 'name' => 'Capstone Project 1'],
            ['code' => 'IT402', 'name' => 'Capstone Project 2'],
        ];

        $picked = fake()->unique()->randomElement($subjects);

        return [
            'subject_code' => $picked['code'],
            'subject_name' => $picked['name'],
            'department_id' => Department::inRandomOrder()->value('department_id'),
        ];
    }
}