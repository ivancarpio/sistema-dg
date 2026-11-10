<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller
{
    public function index()
    {
        // Protegemos la ruta para administradores o usuarios con permiso 'usuarios'
        if (!auth()->check() || !auth()->user()->tienePermiso('usuarios')) {
            return redirect('/pos')->with('error', 'No tienes permiso para acceder a la gestión de usuarios.');
        }

        $usuarios = User::orderBy('id', 'desc')->get();
        $catalogoPermisos = User::catalogoPermisos();

        return view('usuarios.index', compact('usuarios', 'catalogoPermisos'));
    }

    public function store(Request $request)
    {
        if (!auth()->check() || !auth()->user()->tienePermiso('usuarios')) {
            return redirect('/pos')->with('error', 'Acceso denegado.');
        }

        $request->validate([
            'name'             => 'required|string|max:255',
            'email'            => 'required|string|max:255|unique:users,email',
            'password'         => 'required|string|min:6',
            'rol'              => 'required|in:admin,cajero',
            'ci'               => 'nullable|string|max:30',
            'celular'          => 'nullable|string|max:30',
            'fecha_nacimiento' => 'nullable|date',
            'fecha_ingreso'    => 'nullable|date',
            'permisos'         => 'nullable|array',
        ], [
            'name.required'     => 'El nombre completo es obligatorio.',
            'email.required'    => 'El nombre de usuario de acceso es obligatorio.',
            'email.unique'      => 'Este usuario ya está registrado por otra cuenta.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min'      => 'La contraseña debe tener al menos 6 caracteres.',
        ]);

        $permisos = $request->input('permisos', []);
        // Si el rol es admin, le garantizamos todos los permisos del catálogo
        if ($request->rol === 'admin') {
            $permisos = array_keys(User::catalogoPermisos());
        }

        $user = new User();
        $user->name             = trim($request->name);
        $user->email            = trim($request->email);
        $user->password         = Hash::make($request->password);
        $user->rol              = $request->rol;
        $user->ci               = trim($request->ci ?? '');
        $user->celular          = trim($request->celular ?? '');
        $user->fecha_nacimiento = $request->fecha_nacimiento ? $request->fecha_nacimiento : null;
        $user->fecha_ingreso    = $request->fecha_ingreso ? $request->fecha_ingreso : now()->toDateString();
        $user->permisos         = $permisos;
        $user->save();

        return back()->with('success', "Usuario '{$user->name}' registrado exitosamente con sus permisos configurados.");
    }

    public function update(Request $request, $id)
    {
        if (!auth()->check() || !auth()->user()->tienePermiso('usuarios')) {
            return redirect('/pos')->with('error', 'Acceso denegado.');
        }

        $user = User::findOrFail($id);

        $request->validate([
            'name'             => 'required|string|max:255',
            'email'            => 'required|string|max:255|unique:users,email,' . $id,
            'password'         => 'nullable|string|min:6',
            'rol'              => 'required|in:admin,cajero',
            'ci'               => 'nullable|string|max:30',
            'celular'          => 'nullable|string|max:30',
            'fecha_nacimiento' => 'nullable|date',
            'fecha_ingreso'    => 'nullable|date',
            'permisos'         => 'nullable|array',
        ], [
            'name.required'  => 'El nombre completo es obligatorio.',
            'email.required' => 'El nombre de usuario de acceso es obligatorio.',
            'email.unique'   => 'Este usuario ya pertenece a otra cuenta.',
            'password.min'   => 'La contraseña debe tener al menos 6 caracteres.',
        ]);

        // Evitar que el único admin se degrade a cajero
        if ($user->rol === 'admin' && $request->rol !== 'admin') {
            $adminCount = User::where('rol', 'admin')->count();
            if ($adminCount <= 1) {
                return back()->withErrors(['No puedes quitar el rol de Administrador al único administrador registrado en el sistema.']);
            }
        }

        $permisos = $request->input('permisos', []);
        if ($request->rol === 'admin') {
            $permisos = array_keys(User::catalogoPermisos());
        }

        $user->name             = trim($request->name);
        $user->email            = trim($request->email);
        $user->rol              = $request->rol;
        $user->ci               = trim($request->ci ?? '');
        $user->celular          = trim($request->celular ?? '');
        $user->fecha_nacimiento = $request->fecha_nacimiento ? $request->fecha_nacimiento : null;
        $user->fecha_ingreso    = $request->fecha_ingreso ? $request->fecha_ingreso : null;
        $user->permisos         = $permisos;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return back()->with('success', "Usuario '{$user->name}' actualizado correctamente.");
    }

    public function destroy($id)
    {
        if (!auth()->check() || !auth()->user()->tienePermiso('usuarios')) {
            return redirect('/pos')->with('error', 'Acceso denegado.');
        }

        if (auth()->id() == $id) {
            return back()->withErrors(['No puedes eliminar tu propia cuenta de usuario en sesión.']);
        }

        $user = User::findOrFail($id);

        if ($user->rol === 'admin') {
            $adminCount = User::where('rol', 'admin')->count();
            if ($adminCount <= 1) {
                return back()->withErrors(['No puedes eliminar al único Administrador del sistema.']);
            }
        }

        $nombre = $user->name;
        $user->delete();

        return back()->with('success', "Usuario '{$nombre}' eliminado del sistema.");
    }
}
