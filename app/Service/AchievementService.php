<?php

namespace App\Service;

use App\Models\Achievement;
use App\Utils\CloudinaryClient;
use Exception;
use Illuminate\Http\UploadedFile;
use Log;
use Request;

class AchievementService
{

    private $cloudinary_folder = 'achievements';

    public function all(
        array $columns = ['name', 'description', 'date_at'],
        string|null $search = null,
        int $limit = 8,
        bool $is_has_limit = false,
    ) {
        if (!$is_has_limit) {
            return Achievement::all($columns);
        }
        return Achievement::when($search, function ($query, $search) {
            $search = strtolower($search);
            return $query->whereRaw('LOWER(name) LIKE ?', ["%$search%"]);
        })
            ->paginate($limit);
    }

    public function detail(string $id)
    {
        return Achievement::where('id', $id)->first();
    }

    public function save(
        array $data,
    ) {
        return Achievement::create([
            'name' => $data['name'],
            'description' => $data['description'],
            'date_at' => $data['date_at'] ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function edit(
        Achievement $achievement,
        array $data,
    )
    {
        return $achievement->update([
            'name' => $data['name'],
            'description' => $data['description'],
            'date_at' => $data['date_at'] ?? null,
            'updated_at' => now(),
        ]);
    }

    public function delete(Achievement $achievement)
    {
        return $achievement->delete();
    }

}