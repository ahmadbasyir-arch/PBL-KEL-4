<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DosenPenggunaController extends Controller
{
    public function index()
    {
        $mahasiswa = User::where('role', 'mahasiswa')->orderBy('name')->get();
        $dosen = User::where('role', 'dosen')->orderBy('name')->get();

        return view('dosen.sidebar-pengguna', compact('mahasiswa', 'dosen'));
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('dosen.pengguna.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'role' => 'required|in:admin,dosen,mahasiswa,staff',
            'password' => 'nullable|min:6',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('dosen.pengguna.index')->with('success', 'Data pengguna berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('dosen.pengguna.index')->with('success', 'Pengguna berhasil dihapus.');
    }
}
