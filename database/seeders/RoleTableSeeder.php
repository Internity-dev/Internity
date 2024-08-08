<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Database\Seeder;

class RoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role = Role::create([
            'name' => 'super-admin',
        ]);
        $permissions = Permission::pluck('id', 'id')->all();
        $role->syncPermissions($permissions);

        $role = Role::create([
            'name' => 'admin',
        ]);
        $permissions = Permission::where('name', 'not like', '%role%')
            ->pluck('id', 'id');
        $role->syncPermissions($permissions);

        $role = Role::create([
            'name' => 'manager',
        ]);
        $permissions = Permission::where('name', 'like', '%user%')
            ->orWhere('name', 'like', '%company%')
            ->orWhere('name', 'like', '%department%')
            ->orWhere('name', 'like', '%vacancy%')
            ->orWhere('name', 'like', '%course%')
            ->orWhere('name', 'like', '%room%')
            ->orWhere('name', 'like', '%score%')
            ->orWhere('name', 'like', '%news%')
            ->orWhere('name', 'like', '%presence%')
            ->orWhere('name', 'like', '%score-predicate%')
            ->orWhere('name', 'school-list')
            ->orWhere('name', 'like', '%journal%')
            ->orWhere('name', 'like', '%student%')
            ->orWhere('name', 'like', '%monitor%')
            ->orWhere('name', 'like', '%appliance%')
            ->orWhere('name', 'like', '%review%')
            ->orWhere('name', 'like', '%question%')
            ->pluck('id', 'id');
        $role->syncPermissions($permissions);

        $role = Role::create([
            'name' => 'kepala program',
        ]);
        $permissions = Permission::where('name', 'department-edit')
            ->orWhere('name', 'department-list')
            ->orWhere('name', 'like', '%vacancy%')
            ->orWhere('name', 'like', '%course%')
            ->orWhere('name', 'like', '%room%')
            ->orWhere('name', 'like', '%user%')
            ->orWhere('name', 'like', '%company%')
            ->orWhere('name', 'score-list')
            ->orWhere('name', 'like', '%news%')
            ->orWhere('name', 'like', '%appliance%')
            ->orWhere('name', 'presence-list')
            ->orWhere('name', 'score-predicate-list')
            ->orWhere('name', 'journal-list')
            ->orWhere('name', 'student-list')
            ->orWhere('name', 'like', '%news%')
            ->orWhere('name', 'like', '%monitor%')
            ->orWhere('name', 'like', '%review%')
            ->pluck('id', 'id');
        $role->syncPermissions($permissions);

        $role = Role::create([
            'name' => 'mentor',
        ]);
        $permissions = Permission::where('name', 'like', '%vacancy%')
            ->orWhere('name', 'room-list')
            ->orWhere('name', 'room-edit')
            ->orWhere('name', 'score-list')
            ->orWhere('name', 'score-edit')
            ->orWhere('name', 'score-create')
            ->orWhere('name', 'score-delete')
            ->orWhere('name', 'like', '%appliance%')
            ->orWhere('name', 'score-predicate-list')
            ->orWhere('name', 'presence-list')
            ->orWhere('name', 'presence-edit')
            ->orWhere('name', 'presence-approve')
            ->orWhere('name', 'company-list')
            ->orWhere('name', 'company-edit')
            ->orWhere('name', 'journal-list')
            ->orWhere('name', 'journal-approve')
            ->orWhere('name', 'student-list')
            ->pluck('id', 'id');
        $role->syncPermissions($permissions);

        $role = Role::create([
            'name' => 'student',
        ]);
        $permissions = Permission::where('name', 'school-list')
            ->orWhere('name', 'company-list')
            ->orWhere('name', 'department-list')
            ->orWhere('name', 'vacancy-list')
            ->orWhere('name', 'course-list')
            ->orWhere('name', 'room-list')
            ->orWhere('name', 'score-list')
            ->orWhere('name', 'score-predicate-list')
            ->orWhere('name', 'presence-list')
            ->orWhere('name', 'presence-create')
            ->orWhere('name', 'news-list')
            ->pluck('id', 'id');
        $role->syncPermissions($permissions);
    }
}
