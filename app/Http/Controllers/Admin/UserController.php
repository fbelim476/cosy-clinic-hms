<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with(['roles', 'branch', 'department'])->latest()->paginate(20);
        $roles = Role::all();

        return view('pages.admin.users', compact('users', 'roles'));
    }
}
