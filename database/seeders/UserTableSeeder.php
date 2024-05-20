<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Course;
use App\Models\Review;
use App\Models\School;
use App\Models\Company;
use App\Models\Journal;
use App\Models\Vacancy;
use Nette\Utils\Random;
use App\Models\Appliance;
use App\Models\Department;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $school = School::where('name', 'SMKN 1 Cibinong')->first();
        $user = User::create([
            'name' => 'Superadmin',
            'email' => 'superadmin@test.dev',
            'password' => bcrypt('123qweasd'),
        ]);
        $user->assignRole('super-admin');
        $user->schools()->attach($school->id);

        // $user = User::create([
        //     'name' => 'Admin',
        //     'email' => 'admin@test.dev',
        //     'password' => bcrypt('123qweasd'),
        // ]);
        // $user->assignRole('admin');


        $user = User::create([
            'name' => 'Manager',
            'email' => 'manager@test.dev',
            'password' => bcrypt('123qweasd'),
        ]);
        $user->assignRole('manager');
        $user->schools()->attach($school->id);

        $department = Department::where('name', 'SIJA')->where('school_id', $school->id)->first();
        $user = User::create([
            'name' => 'Teacher',
            'email' => 'teacher@test.dev',
            'password' => bcrypt('123qweasd'),
        ]);
        $user->assignRole('teacher');
        $user->schools()->attach($school->id);
        $user->departments()->attach($department->id);

        $company = Company::where('department_id', $department->id)->first();
        $vacancy = Vacancy::where('company_id', $company->id)->first();
        $user = User::create([
            'name' => 'Mentor',
            'email' => 'mentor@test.dev',
            'password' => bcrypt('123qweasd'),
        ]);
        $user->assignRole('mentor');
        $user->companies()->attach($company->id);

        $startDate = now()->subDays(30);
        $endDate = now()->addDays(180);
        $course = Course::where('name', 'XIII SIJA 1')->where('department_id', $department->id)->first();
        $user = User::create([
            'name' => 'Student',
            'email' => 'student@test.dev',
            'password' => bcrypt('123qweasd'),
            'resume' => 'storage/resumes/CV_Hermawan.pdf'
        ]);
        $user->assignRole('student');
        $user->schools()->attach($school->id);
        $user->departments()->attach($department->id);
        $user->courses()->attach($course->id);

        Appliance::create([
            'user_id' => $user->id,
            'vacancy_id' => $vacancy->id,
            'status' => 'pending',        ]);

        $company = Company::where('department_id', $department->id)->orderBy('id', 'desc')->first();
        $vacancy = Vacancy::where('company_id', $company->id)->first();
        $user = User::create([
            'name' => 'Student Two',
            'email' => 'student2@test.dev',
            'password' => bcrypt('123qweasd'),
            'phone' => '081234567890',
            'gender' => 'male',
            'address' => 'Jl. Cibinong No. 1',
            'date_of_birth' => '2000-01-01',
            'bio' => 'I am a student',
            'avatar' => 'avatars/placeholder.jpg',
            'skills' => 'PHP, Laravel, VueJS, MySQL, HTML, CSS, JavaScript, Bootstrap',
            'resume' => 'storage/resumes/CV_Hermawan.pdf'
        ]);
        $user->assignRole('student');
        $user->schools()->attach($school->id);
        $user->departments()->attach($department->id);
        $user->courses()->attach($course->id);
        $user->companies()->attach($company->id);
        $user->internDates()->create([
            'start_date' => $startDate,
            'end_date' => $endDate,
            'company_id' => $company->id,
            'user_id' => $user->id,
        ]);
        $user->monitors()->create([
            'company_id' => $company->id,
            'date' => now()->format('Y-m-d'),
            'match' => 3,
        ]);
        for ($now = now(); $now >= $startDate; $now->subDays(1)) {
            $check_in = now()->subMinutes(rand(0, 60))->format('H:i:s');
            $check_out = now()->addHours(8)->addMinutes(rand(0, 60))->format('H:i:s');
            $user->presences()->create([
                'company_id' => $company->id,
                'presence_status_id' => 1,
                'date' => $now->format('Y-m-d'),
                'check_in' => $check_in,
                'check_out' => $check_out,
                'is_approved' => date_diff($now, $startDate)->days <= 23  ? 1 : 0,
            ]);

            Journal::factory()->count(1)->create([
                'user_id' => $user->id,
                'company_id' => $company->id,
                'date' => $now->format('Y-m-d'),
                'is_approved' => date_diff($now, $startDate)->days <= 23  ? 1 : 0,
            ]);
        }

        Appliance::create([
            'user_id' => $user->id,
            'vacancy_id' => $vacancy->id,
            'status' => 'accepted',        ]);

        $startDate = now()->subDays(240);
        $endDate = now()->subDays(30);
        $company = Company::where('department_id', $department->id)->inRandomOrder()->first();
        $vacancy = Vacancy::where('company_id', $company->id)->inRandomOrder()->first();
        $user = User::create([
            'name' => 'Student Three',
            'email' => 'student3@test.dev',
            'password' => bcrypt('123qweasd'),
            'phone' => '081234567890',
            'gender' => 'male',
            'address' => 'Jl. Cibinong No. 1',
            'date_of_birth' => '2000-01-01',
            'bio' => 'I am a student',
            'avatar' => 'avatars/placeholder.jpg',
            'skills' => 'Linux, Mikrotik, VMWare',
            'resume' => 'storage/resumes/CV_Hermawan.pdf'
        ]);
        $user->assignRole('student');
        $user->schools()->attach($school->id);
        $user->departments()->attach($department->id);
        $user->courses()->attach($course->id);
        $user->companies()->attach($company->id);
        $user->internDates()->create([
            'start_date' => $startDate,
            'end_date' => $endDate,
            'company_id' => $company->id,
            'user_id' => $user->id,
            'finished' => 1,
        ]);
        $user->monitors()->create([
            'company_id' => $company->id,
            'date' => now()->subDays(40)->format('Y-m-d'),
            'notes' => 'Pekerjaan sesuai dengan kompetensi',
            'suggest' => 'Tidak ada saran',
            'match' => 4,
        ]);
        for ($now = $endDate; $now >= $startDate; $now->subDays(1)) {
            $check_in = now()->subMinutes(rand(0, 60))->format('H:i:s');
            $check_out = now()->addHours(8)->addMinutes(rand(0, 60))->format('H:i:s');
            $user->presences()->create([
                'company_id' => $company->id,
                'presence_status_id' => 1,
                'date' => $now->format('Y-m-d'),
                'check_in' => $check_in,
                'check_out' => $check_out,
                'is_approved' => 1,
            ]);

            Journal::factory()->count(1)->create([
                'user_id' => $user->id,
                'company_id' => $company->id,
                'date' => $now->format('Y-m-d'),
                'is_approved' => 1,
            ]);
        }

        Appliance::create([
            'user_id' => $user->id,
            'vacancy_id' => $vacancy->id,
            'status' => 'accepted',        ]);

        Review::create([
            'user_id' => $user->id,
            'reviewable_id' => $company->id,
            'reviewable_type' => 'App\Models\Company',
            'rating' => 5,
            'title' => 'Cocok untuk berkembang',
            'body' => 'Perusahaan ini sangat bagus untuk pengembangan skill, pembimbing peduli dengan siswa PKL, dan lingkungan kerja yang nyaman.',
        ]);

        $courses = Course::all();
        foreach ($courses as $course) {
            User::factory()->count(2)->create(['resume' => 'storage/resumes/CV_Hermawan.pdf'])->each(function ($user) use ($course) {
                $user->assignRole('student');
                $user->schools()->attach($course->department->school_id);
                $user->departments()->attach($course->department_id);
                $user->courses()->attach($course->id);
            });

            User::factory()->count(2)->create(['resume' => 'storage/resumes/CV_Hermawan.pdf'])->each(function ($user) use ($course) {
                $user->assignRole('student');
                $user->schools()->attach($course->department->school_id);
                $user->departments()->attach($course->department_id);
                $user->courses()->attach($course->id);

                Appliance::create([
                    'user_id' => $user->id,
                    'vacancy_id' => $course->department->companies->random()->vacancies->random()->id,
                    'status' => 'pending',
                ]);
            });

            User::factory()->count(2)->create(['resume' => 'storage/resumes/CV_Hermawan.pdf'])->each(function ($user) use ($course) {
                $user->assignRole('student');
                $user->schools()->attach($course->department->school_id);
                $user->departments()->attach($course->department_id);
                $user->courses()->attach($course->id);

                $company = $course->department->companies->random();
                $vacancy = $company->vacancies->random();
                $user->companies()->attach($company->id);

                Appliance::create([
                    'user_id' => $user->id,
                    'vacancy_id' => $vacancy->id,
                    'status' => 'accepted',
                ]);

                $startDate = now()->subDays(rand(100, 200));
                $endDate = now()->subDays(rand(5,10));

                $user->internDates()->create([
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'company_id' => $company->id,
                    'user_id' => $user->id,
                    'finished' => 1,
                ]);

                $user->monitors()->create([
                    'company_id' => $company->id,
                    'date' => now()->subDays(70)->format('Y-m-d'),
                    'notes' => 'Pekerjaan sesuai dengan kompetensi',
                    'suggest' => 'Tidak ada saran',
                    'match' => 4,
                ]);
                for ($now = $endDate; $now >= $startDate; $now->subDays(1)) {
                    $check_in = now()->subMinutes(rand(0, 60))->format('H:i:s');
                    $check_out = now()->addHours(8)->addMinutes(rand(0, 60))->format('H:i:s');
                    $user->presences()->create([
                        'company_id' => $company->id,
                        'presence_status_id' => 1,
                        'date' => $now->format('Y-m-d'),
                        'check_in' => $check_in,
                        'check_out' => $check_out,
                        'is_approved' => 1,
                    ]);

                    Journal::factory()->count(1)->create([
                        'user_id' => $user->id,
                        'company_id' => $company->id,
                        'date' => $now->format('Y-m-d'),
                        'is_approved' => 1,
                    ]);
                }

                Review::create([
                    'user_id' => $user->id,
                    'reviewable_id' => $company->id,
                    'reviewable_type' => 'App\Models\Company',
                    'rating' => rand(1, 5),
                    'title' => 'Cocok untuk berkembang',
                    'body' => 'Perusahaan ini sangat bagus untuk pengembangan skill, pembimbing peduli dengan siswa PKL, dan lingkungan kerja yang nyaman.',
                ]);
            });

            User::factory()->count(2)->create()->each(function ($user) use ($course) {
                $user->assignRole('teacher');
                $user->schools()->attach($course->department->school_id);
                $user->departments()->attach($course->department->id);
            });
        }

        $schools = School::all();
        foreach ($schools as $school) {
            User::factory()->count(1)->create()->each(function ($user) use ($school) {
                $user->assignRole('manager');
                $user->schools()->attach($school->id);
            });
        }
    }
}
