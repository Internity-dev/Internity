<?php

namespace Database\Seeders;

use App\Models\School;
use App\Models\Company;
use App\Models\Department;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $schools = School::all();
        foreach ($schools as $school) {
            $departments = Department::where('school_id', $school->id)->get();
            foreach ($departments as $department) {
                Company::factory()->count(1)->create([
                    'department_id' => $department->id,
                ]);
            }
        }
    }
}
