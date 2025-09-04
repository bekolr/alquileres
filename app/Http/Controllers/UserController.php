<?php

namespace App\Http\Controllers;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        // Fetch all users
        $users = User::all();
         $roles = Role::all();   
        return view('users.user', compact('users', 'roles'));   
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        
        //
        // Validate the request data
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]); 
   
        // Create a new user
        $user = User::create([
            'name' => $request->name,           
            'email' => $request->email,
            'password' => bcrypt($request->password), // Encrypt the password
        ]); 
        // Assign roles if provided
        if ($request->has('roles')) {
            $user->syncRoles($request->roles);
        } else {
            $user->syncRoles([]); // Clear roles if none are provided       
             }
        // Redirect back with success message
        return redirect()->back()->with('success', 'Usuario creado correctamente.');


    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
        // Find the user by ID
        $user = User::findOrFail($id);  
        // Fetch all roles
        $roles = Role::all();   
        // Return the view with the user and roles
        return view('users.userrole', compact('user', 'roles'));

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }


public function updatePermisos(Request $request, $id)
{
    $user = User::findOrFail($id);

    // Elimina todos los roles actuales y asigna los nuevos
    $user->syncRoles($request->roles ?? []);

    return redirect()->back()->with('success', 'Roles actualizados correctamente.');
}
}
