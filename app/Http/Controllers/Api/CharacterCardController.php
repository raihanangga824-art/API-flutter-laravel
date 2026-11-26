<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CharacterCard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CharacterCardController extends Controller
{
    private function findOwned($id): CharacterCard
    {
        return CharacterCard::where('user_id', auth()->id())->findOrFail($id);
    }

    // faction_id harus ada DAN milik user yang login
    private function rules(): array
    {
        return [
            'faction_id' => [
                'required',
                Rule::exists('factions', 'id')->where('user_id', auth()->id()),
            ],
            'title' => 'required|string|max:150',
            'race' => 'nullable|string|max:100',
            'ki' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
            'image_url' => 'nullable|string|max:500',
            // file foto dari image_picker (opsional), maks 2 MB
            'image' => 'nullable|image|max:2048',
        ];
    }

    /**
     * Kalau ada file "image" diupload: simpan ke storage/app/public/cards
     * lalu isi image_url dengan URL lengkapnya.
     */
    private function handleUpload(Request $request, array $data, ?CharacterCard $old = null): array
    {
        unset($data['image']);

        if ($request->hasFile('image')) {
            if ($old) {
                $this->deleteLocalImage($old->image_url);
            }
            $path = $request->file('image')->store('cards', 'public');
            // pakai host dari request agar URL bisa dibuka emulator (10.0.2.2) / Chrome (127.0.0.1)
            $data['image_url'] = $request->getSchemeAndHttpHost() . '/storage/' . $path;
        }

        return $data;
    }

    // Hapus file lama jika foto itu hasil upload ke server ini (bukan URL dari public API)
    private function deleteLocalImage(?string $url): void
    {
        if ($url && str_contains($url, '/storage/cards/')) {
            Storage::disk('public')->delete(Str::after($url, '/storage/'));
        }
    }

    // GET /api/character-cards   (opsional filter: ?faction_id=1)
    public function index(Request $request)
    {
        $query = CharacterCard::with('faction:id,name')
            ->where('user_id', auth()->id());

        if ($request->filled('faction_id')) {
            $query->where('faction_id', $request->faction_id);
        }

        return response()->json($query->latest()->get());
    }

    // POST /api/character-cards   (JSON atau multipart/form-data)
    public function store(Request $request)
    {
        $data = $this->handleUpload($request, $request->validate($this->rules()));
        $data['user_id'] = auth()->id();

        $card = CharacterCard::create($data)->load('faction:id,name');

        return response()->json(['message' => 'Kartu dibuat', 'data' => $card], 201);
    }

    // GET /api/character-cards/{id}
    public function show($id)
    {
        return response()->json($this->findOwned($id)->load('faction:id,name'));
    }

    // PUT/PATCH /api/character-cards/{id}
    // Untuk upload file dari Flutter: kirim POST multipart + field _method=PUT
    public function update(Request $request, $id)
    {
        $card = $this->findOwned($id);
        $data = $this->handleUpload($request, $request->validate($this->rules()), $card);

        // URL foto diganti manual / dikosongkan -> hapus file upload lama
        if (! $request->hasFile('image') && ($data['image_url'] ?? null) !== $card->image_url) {
            $this->deleteLocalImage($card->image_url);
        }

        $card->update($data);

        return response()->json(['message' => 'Kartu diupdate', 'data' => $card->load('faction:id,name')]);
    }

    // DELETE /api/character-cards/{id}
    public function destroy($id)
    {
        $card = $this->findOwned($id);
        $this->deleteLocalImage($card->image_url);
        $card->delete();

        return response()->json(['message' => 'Kartu dihapus']);
    }
}
