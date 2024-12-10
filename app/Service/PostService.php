<?php

namespace App\Service;
use Illuminate\Support\Str;

use App\Models\Post;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;


class PostService
{
    public function store($data)
    {
        DB::beginTransaction();

        // Tag IDsni ajratib olish
        $tagIds = $data['tag_ids'] ?? null;
        unset($data['tag_ids']);

        // Fayllarni saqlash va URLni olish
        if (isset($data['preview_image'])) {
            $data['preview_image'] = Storage::disk('public')->put('images', $data['preview_image']);
        }

        if (isset($data['main_image'])) {
            $data['main_image'] = Storage::disk('public')->put('images', $data['main_image']);
        }
        $data['slug'] = Str::slug($data['title'], '-');

        // Postni yaratish yoki topish
        $post = Post::create($data);

        // Taglarni qo'shish
        if ($tagIds) {
            $post->tags()->attach($tagIds);
        }

        DB::commit();
    return $post;
}

    public function update($data, $post)
    {
        try {
            DB::beginTransaction();
            if (isset($data['tag_ids'])) {
                $tagIds = $data['tag_ids'];
                unset($data['tag_ids']);
            }
            if (isset($data['preview_image'])) {
                $data['preview_image'] = Storage::disk('public')->put('/images', $data['preview_image']);
            }
            if (isset($data['main_image'])) {
                $data['main_image'] = Storage::disk('public')->put('/images', $data['main_image']);
            }

            $post->update($data);

            if (isset($tagIds)) {
                $post->tags()->sync($tagIds);
            }
            DB::commit();
        } catch (Exception) {
            DB::rollBack();
            abort(500);
        }

        return $post;
    }
}
