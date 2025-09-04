<?php

namespace App\Http\Controllers;

use App\Models\Inquilino;
use Illuminate\Http\Request;

class InquilinoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $inquilinos = Inquilino::all();
        return view('inquilinos.index', compact('inquilinos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('inquilinos.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'dni' => 'required|string|max:20|unique:inquilinos,dni',
            'nombre' => 'required|string|max:100',
            'email' => 'nullable|email|max:100',
            'telefono' => 'nullable|string|max:20',
        ]);

        Inquilino::create($data);
 return redirect()->route('inquilinos.index')->with('success', 'Cliente creado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Inquilino $inquilino)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Inquilino $inquilino)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Inquilino $inquilino)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Inquilino $inquilino)
    {
        //
    }
}
