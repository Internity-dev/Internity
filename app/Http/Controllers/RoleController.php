<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Permission;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:role-list', ['only' => ['index', 'show']]);
        $this->middleware('permission:role-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:role-edit', ['only' => ['edit', 'update', 'updateStatus']]);
        $this->middleware('permission:role-delete', ['only' => ['destroy']]);
    }

    public function getData($search=null, $status=null, $sort=null)
    {
        $roles = Role::when($search, function ($query, $search) {
                return $query->search($search);
            })
            ->when($status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->when($sort, function ($query, $sort) {
                if ($sort[0] == '-') {
                    $sort = substr($sort, 1);
                    $sortType = 'desc';
                } else {
                    $sortType = 'asc';
                }
                return $query->orderBy($sort, $sortType);
            })
            ->paginate(10);

        $roles->withPath('/roles')->withQueryString();

        if ($roles->count() > 0) {
            $context = [
                'status' => true,
                'message' => 'Data role ditemukan',
                'roles' => $roles,
                'pagination' => $roles->links()->render(),
                'search' => $search,
                'statusData' => $status,
                'sort' => $sort,
            ];
        } else {
            $context = [
                'status' => false,
                'message' => 'Data role tidak ditemukan',
                'roles' => [],
                'pagination' => $roles->links()->render(),
                'search' => $search,
                'statusData' => $status,
                'sort' => $sort,
            ];
        }

        return $context;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $context = $this->getData();

        return $context['status']
        ? view('roles.index', $context)
        : view('roles.index', $context)->with('error', $context['message']);
    }

    public function search(Request $request)
    {
        $search = $request->query('search');
        $status = $request->query('status');
        $sort = $request->query('sort');

        $context = $this->getData($search, $status, $sort);

        return $context['status']
        ? response()->json($context)
        : response()->json($context, 204);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $permissions = Permission::all();
        $groupedPermissions = [];

        foreach ($permissions as $permission) {
            $parts = explode('-', $permission->name);

            if (count($parts)) {
                $group = $parts[0];
                $groupedPermissions[$group][] = $permission;
            }
        }
        return view('roles.create', compact('groupedPermissions'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name',
            'permissions' => 'required',
            'status' => 'required|boolean',
        ]);

        $role = Role::create([
            'name' => $request->name,
            'status' => $request->status,
        ]);

        $role->syncPermissions($request->permissions);

        return $role
        ? redirect()->route('roles.index')->with('success', "Role $role->name berhasil dibuat")
        : redirect()->route('roles.index')->with('error', 'Role gagal dibuat');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $id = decrypt($id);
        $role = Role::find($id);

        return $role
        ? view('roles.show', compact('role'))
        : redirect()->route('roles.index')->with('error', 'Role tidak ditemukan');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $id = decrypt($id);
        $role = Role::find($id);
        $permissions = Permission::all();

        $groupedPermissions = [];

        foreach ($permissions as $permission) {
            $parts = explode('-', $permission->name);

            if (count($parts)) {
                $group = $parts[0];
                $groupedPermissions[$group][] = $permission;
            }
        }

        return $role
        ? view('roles.edit', compact('role', 'groupedPermissions'))
        : redirect()->route('roles.index')->with('error', 'Role tidak ditemukan');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $id = decrypt($id);
        $role = Role::find($id);

        $request->validate([
            'name' => 'required|unique:roles,name,' . $role->id,
            'permissions' => 'required',
            'status' => 'required|boolean',
        ]);

        $role->name = $request->name;
        $role->status = $request->status;
        $role->save();

        $role->syncPermissions($request->permissions);

        return $role
        ? redirect()->route('roles.index')->with('success', "Data role $role->name berhasil diubah")
        : redirect()->route('roles.index')->with('error', 'Data role gagal diubah');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $id = decrypt($id);
        $role = Role::find($id);

        return $role->delete()
        ? redirect()->route('roles.index')->with('success', "Data role $role->name berhasil dihapus")
        : redirect()->route('roles.index')->with('error', 'Data role gagal dihapus');
    }

    public function updateStatus($id)
    {
        $id = decrypt($id);
        $role = Role::find($id);

        if ($role->status == 1) {
            $role->status = 0;
            $state = 'dinonaktifkan';
        } else {
            $role->status = 1;
            $state = 'diaktifkan';
        }
        $role->save();

        $users = User::whereHas('roles', function ($query) use ($role) {
            $query->where('name', $role->name);
        })->get();

        foreach ($users as $user) {
            $user->status = $role->status;
            $user->save();
        }

        return $role
            ? redirect()->route('roles.index')->with('success', "Role $role->name $state")
            : redirect()->route('roles.index')->with('error', 'Status role gagal diubah');
    }
}
