<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller
{
    public function index()
    {
        // Protegemos la ruta para que solo entre el admin
        if(auth()->user()->rol !== 'admin'){
            return redirect('/pos')->with('error', 'No tienes permiso para ver esta sección.');
        }

        $usuarios = User::orderBy('id', 'desc')->get();
        return view('usuarios.index', compact('usuarios'));
    }

    public function store(Request $request)
    {
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->rol = $request->rol;
        $user->save();

        return back()->with('success', 'Usuario creado exitosamente.');
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->name = $request->name;
        $user->email = $request->email;
        $user->rol = $request->rol;
        
        // Solo actualizamos la contraseña si el admin escribió una nueva
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        $user->save();

        return back()->with('success', 'Usuario actualizado correctamente.');
    }

    public function destroy($id)
    {
        if(auth()->id() == $id) {
            return back()->withErrors(['No puedes eliminar tu propia cuenta.']);
        }
        
        User::destroy($id);
        return back()->with('success', 'Usuario eliminado.');
    }
}