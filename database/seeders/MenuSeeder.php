<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\School;
use App\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $school = School::first();
        $menus = [
            [
                'name' => 'Dashboard',
                'icon' => 'material-symbols:space-dashboard',
                'url' => 'dashboard',
                'status' => 1,
                'order' => 1,
                'parent_id' => null,
            ],
            [
                'name' => 'Siswa',
                'icon' => 'mdi:account-multiple',
                'url' => 'students',
                'status' => 1,
                'order' => 10,
                'parent_id' => null,
                'permission_id' => Permission::where('name', 'student-list')->first()->id,
            ],
            [
                'name' => 'Guru',
                'icon' => 'mdi:account-multiple',
                'url' => 'teachers',
                'status' => 1,
                'order' => 11,
                'parent_id' => null,
                'permission_id' => Permission::where('name', 'teacher-list')->first()->id,
            ],
            [
                'name' => 'IDUKA',
                'icon' => 'mdi:building',
                'url' => 'companies',
                'status' => 1,
                'order' => 20,
                'parent_id' => null,
                'permission_id' => Permission::where('name', 'company-list')->first()->id,
            ],
            [
                'name' => 'Berita',
                'icon' => 'mdi:newspaper',
                'url' => 'news',
                'status' => 1,
                'order' => 30,
                'parent_id' => null,
                'permission_id' => Permission::where('name', 'news-list')->first()->id,
            ],
            [
                'name' => 'Berita Sekolah',
                'icon' => 'mdi:newspaper',
                'url' => 'news?category=school&school=' . encrypt($school->id),
                'status' => 1,
                'order' => 31,
                'parent_id' => 5,
                'permission_id' => Permission::where('name', 'news-list')->first()->id,
            ],
            [
                'name' => 'Berita Jurusan',
                'icon' => 'mdi:newspaper',
                'url' => 'news?category=department&school=' . encrypt($school->id),
                'status' => 1,
                'order' => 32,
                'parent_id' => 5,
                'permission_id' => Permission::where('name', 'news-list')->first()->id,
            ],
            [
                'name' => 'Kompetensi Keahlian',
                'icon' => 'mdi:book-open-page-variant',
                'url' => 'departments?school=' . encrypt($school->id),
                'status' => 1,
                'order' => 40,
                'parent_id' => null,
                'permission_id' => Permission::where('name', 'department-list')->first()->id,
            ],
            [
                'name' => 'Sekolah',
                'icon' => 'mdi:home-city',
                'url' => 'schools/' . encrypt($school->id) . '/edit',
                'status' => 1,
                'order' => 50,
                'parent_id' => null,
                'permission_id' => Permission::where('name', 'school-list')->first()->id,
            ],
            [
                'name' => 'Kuisioner',
                'icon' => 'mdi:comment-question-outline',
                'url' => 'questions?school=' . encrypt($school->id),
                'status' => 1,
                'order' => 60,
                'parent_id' => null,
                'permission_id' => Permission::where('name', 'question-list')->first()->id,
            ],
            [
                'name' => 'Predikat Nilai',
                'icon' => 'mdi:format-list-bulleted',
                'url' => 'score-predicates?school=' . encrypt($school->id),
                'status' => 1,
                'order' => 70,
                'parent_id' => null,
                'permission_id' => Permission::where('name', 'score-predicate-list')->first()->id,
            ],
            [
                'name' => 'Status Presensi',
                'icon' => 'mdi:format-list-bulleted',
                'url' => 'presence-statuses?school=' . encrypt($school->id),
                'status' => 1,
                'order' => 80,
                'parent_id' => null,
                'permission_id' => Permission::where('name', 'presence-status-list')->first()->id,
            ],
            // [
            //     'name' => 'Kontrol Akses',
            //     'icon' => 'icon-park-outline:permissions',
            //     'url' => '#',
            //     'status' => 1,
            //     'order' => 50,
            //     'parent_id' => null,
            //     'permission_id' => Permission::where('name', 'user-list')->first()->id,
            // ],
            [
                'name' => 'User',
                'icon' => 'mdi:user-multiple',
                'url' => 'users',
                'status' => 1,
                'order' => 90,
                'parent_id' => null,
                'permission_id' => Permission::where('name', 'user-list')->first()->id,
            ],
            [
                'name' => 'Role',
                'icon' => 'icon-park-outline:permissions',
                'url' => 'roles',
                'status' => 1,
                'order' => 100,
                'parent_id' => null,
                'permission_id' => Permission::where('name', 'role-list')->first()->id,
            ],
        ];

        foreach ($menus as $menu) {
            $menu = Menu::create($menu);
        }
    }
}
