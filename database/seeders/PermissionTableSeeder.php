<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [
            'role-list',
            'role-create',
            'role-edit',
            'role-delete',

            'permission-list',
            'permission-create',
            'permission-edit',

            'user-list',
            'user-create',
            'user-edit',
            'user-delete',

            'school-list',
            'school-create',
            'school-edit',
            'school-delete',

            'company-list',
            'company-create',
            'company-edit',
            'company-delete',

            'department-list',
            'department-create',
            'department-edit',
            'department-delete',

            'vacancy-list',
            'vacancy-create',
            'vacancy-edit',
            'vacancy-delete',

            'course-list',
            'course-create',
            'course-edit',
            'course-delete',

            'room-list',
            'room-create',
            'room-edit',
            'room-delete',

            'score-predicate-list',
            'score-predicate-create',
            'score-predicate-edit',
            'score-predicate-delete',

            'score-list',
            'score-create',
            'score-edit',
            'score-delete',

            'news-list',
            'news-create',
            'news-edit',
            'news-delete',

            'presence-list',
            'presence-create',
            'presence-edit',
            'presence-delete',
            'presence-approve',

            'student-list',

            'teacher-list',

            'journal-list',
            'journal-create',
            'journal-edit',
            'journal-delete',
            'journal-approve',

            'presence-status-list',
            'presence-status-create',
            'presence-status-edit',
            'presence-status-delete',

            'appliance-list',
            'appliance-edit',
            'appliance-delete',
            'appliance-approve',

            'monitor-list',
            'monitor-create',
            'monitor-edit',
            'monitor-delete',

            'review-list',
            'review-create',
            'review-edit',
            'review-delete',

            'question-list',
            'question-create',
            'question-edit',
            'question-delete',
        ];

        foreach ($permissions as $permission) {
            Permission::create([
                'name' => $permission,
            ]);
        }
    }
}
