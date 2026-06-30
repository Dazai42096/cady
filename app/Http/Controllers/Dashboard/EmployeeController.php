<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = User::query()
            ->whereIn('role', ['admin', 'sales', 'support'])
            ->latest()
            ->paginate(15);

        return view('dashboard.employees.index', compact('employees'));
    }

    public function create()
    {
        return view('dashboard.employees.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', Rule::in(['sales', 'support'])],
        ]);

        $user = new User();
        $user->name = $data['name'];

        if (Schema::hasColumn('users', 'phone')) {
            $user->phone = $data['phone'];
        }

        $user->email = $data['email'];
        $user->password = Hash::make($data['password']);
        $user->role = $data['role'];

        if (Schema::hasColumn('users', 'is_active')) {
            $user->is_active = true;
        }

        if (Schema::hasColumn('users', 'email_verified_at')) {
            $user->email_verified_at = now();
        }

        $user->save();

        return redirect()
            ->route('dashboard.employees.index')
            ->with('success', 'Employee account created successfully.');
    }
}