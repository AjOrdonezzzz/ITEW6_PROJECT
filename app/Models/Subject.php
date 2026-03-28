<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Subject extends Model
{
    use HasFactory;

    protected $primaryKey = 'subject_id';

    protected $fillable = [
        'subject_code',
        'subject_name',
        'department_id',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id', 'department_id');
    }

    public function students()
    {
        return $this->hasMany(StudentSubject::class, 'subject_id', 'subject_id');
    }

    public function faculty()
    {
        return $this->hasMany(FacultySubject::class, 'subject_id', 'subject_id');
    }
}