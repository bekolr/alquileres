<?php

namespace App\Http\Controllers;

use App\Models\IndiceIpc;
use Illuminate\Http\Request;

class IndiceIpcController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $indices = IndiceIpc::all();    
        return view('ipc.index', compact('indices'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('ipc.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        

        $request->validate([
            'anio' => 'required|integer|min:2000|max:2100',
            'mes' => 'required|integer|min:1|max:12',
            'valor' => 'required|numeric|min:0',
        ]); 
        IndiceIpc::create($request->all());
        return redirect()->route('ipc.index')->with('success', 'Índice IPC creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(IndiceIpc $indiceIpc)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(IndiceIpc $indiceIpc)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, IndiceIpc $indiceIpc)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(IndiceIpc $indiceIpc)
    {
        //
    }
}
