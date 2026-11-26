<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Faction;
use Illuminate\Http\Request;

class FactionController extends Controller
{
    // Ambil faksi milik user login saja, kalau bukan miliknya -> 404
    private function findOwned($id): Faction
    {
        return Faction::where('user_id', auth()->id())->findOrFail($id);
    }

    // GET /api/factions
    public function index()
    {
        $factions = Faction::where('user_id', auth()->id())
            ->withCount('characterCards') // -> character_cards_count
            ->latest()
            ->get();

        return response()->json($factions);
    }

    // POST /api/factions
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
        ]);

        $faction = Faction::create([
            'user_id' => auth()->id(),
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
        ]);

        return response()->json(['message' => 'Faksi dibuat', 'data' => $faction], 201);
    }

    // GET /api/factions/{id}
    public function show($id)
    {
        return response()->json($this->findOwned($id)->load('characterCards'));
    }

    // PUT/PATCH /api/factions/{id}
    public function update(Request $request, $id)
    {
        $faction = $this->findOwned($id);

        $data = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
        ]);

        $faction->update($data);

        return response()->json(['message' => 'Faksi diupdate', 'data' => $faction]);
    }

    // DELETE /api/factions/{id}  (kartu di dalamnya ikut terhapus: cascade)
    public function destroy($id)
    {
        $this->findOwned($id)->delete();

        return response()->json(['message' => 'Faksi dihapus']);
    }
}
