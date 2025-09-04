<?php

namespace App\Http\Controllers;

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;


use Illuminate\Http\Request;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
       
        
        

        // Fetch all roles and permissions
        $roles = Role::all();
       // $roles = Permission::all();
        return view('users.roles' , compact('roles'));
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
        $request->validate([
            'role' => 'required|string|max:255',
        ]);
        // Create a new role
        $role = Role::create(['name' => $request->role]);
        // Optionally, you can redirect or return a response
        return redirect()->route('roles.index')->with('success', 'Rol Creado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
        // Find the role by ID
        $role = Role::findOrFail($id);
        
        // Fetch all permissions
        $permissions = Permission::all();
       
        // Return the view with the role and permissions
        return view('users.rolepermiso', compact('role', 'permissions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        
        $request->validate([
            'name' => 'required|string|max:255',
        ]);
        // Find the role by ID
        $role = Role::findOrFail($id);
       
        // Update the role's name
        $role->name = $request->name;
        $role->save();
        // Optionally, you can redirect or return a response
        return redirect()->route('roles.index')->with('success', 'Rol actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        // Find the role by ID
        $role = Role::findOrFail($id);
        // Delete the role
        $role->delete();
        // Optionally, you can redirect or return a response
        return redirect()->route('roles.index')->with('success', 'Rol eliminado correctamente.');
    }
    public function updatePermisos(Request $request, $id)
{
    $role = Role::findById($id);
    
    $role->syncPermissions($request->permissions ?? []);

    return redirect()->back()->with('success', 'Permisos actualizados correctamente.');
}
}
