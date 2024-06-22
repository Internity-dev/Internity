<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Department;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $departments = Department::where('name', '!=', 'SIJA')->where('name', '!=', 'TFLM')->where('name', '!=', 'TOI')->get();
        foreach ($departments as $department) {
            $courses = [
                [
                    'name' => "XII $department->name 1",
                    'department_id' => $department->id,
                ],
                [
                    'name' => "XII $department->name 2",
                    'department_id' => $department->id,
                ],
            ];

            foreach ($courses as $course) {
                $created = Course::create($course);
                // generate random 6 digit lowercase alphanumeric
                $created->code()->create([
                    'code' => substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyz', ceil(6/strlen($x)) )),1,6),
                ]);
            }
        }

        $fourYears = Department::where('name', 'SIJA')->orWhere('name', 'TFLM')->orWhere('name', 'TOI')->get();
        foreach ($fourYears as $fourYear) {
            $courses = [
                [
                    'name' => "XIII $fourYear->name 1",
                    'department_id' => $fourYear->id,
                ],
                [
                    'name' => "XIII $fourYear->name 2",
                    'department_id' => $fourYear->id,
                ]
            ];

            foreach ($courses as $course) {
                $created = Course::create($course);
                // generate random 6 digit lowercase alphanumeric
                $created->code()->create([
                    'code' => substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyz', ceil(6/strlen($x)) )),1,6),
                ]);
            }
        }

    }
}
