<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Member;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    public function index() {
        return response()->json(['success' => true, 'data' => Member::all()], 200);
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:members',
            'phone' => 'required'
        ]);

        $member = Member::create($validated);
        return response()->json(['success' => true, 'message' => 'Anggota terdaftar', 'data' => $member], 201);
    }

    public function show($id) {
        $member = Member::find($id);
        if (!$member) return response()->json(['message' => 'Anggota tidak ditemukan'], 404);
        return response()->json(['success' => true, 'data' => $member], 200);
    }

    public function update(Request $request, $id) {
        $member = Member::find($id);
        if (!$member) return response()->json(['message' => 'Data tidak ditemukan'], 404);

        $member->update($request->all());
        return response()->json(['success' => true, 'message' => 'Profil anggota diperbarui', 'data' => $member], 200);
    }

    public function destroy($id) {
        $member = Member::find($id);
        if (!$member) return response()->json(['message' => 'Gagal hapus'], 404);

        $member->delete();
        return response()->json(['success' => true, 'message' => 'Anggota berhasil dihapus'], 200);
    }
}
