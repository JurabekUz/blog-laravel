<?php

// namespace App\Http\Controllers\Admin\Post;

// use App\Models\Category;
// use App\Models\Tag;
// use Illuminate\Contracts\View\Factory as ViewFactory;

// class CreateController extends BaseController
// {
//     public function __invoke(ViewFactory $view_factory)
//     {
//         $categories = Category::all();
//         $tags       = Tag::all();

//         return $view_factory->make('admin.post.create', ['categories' => $categories, 'tags' => $tags]);
//     }
// }


namespace App\Http\Controllers\Admin\Post;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CreateController extends Controller
{
    public function __invoke()
    {
        $categories = Category::all();
        $tags = Tag::all();

        return view('admin.post.create', ['categories' => $categories, 'tags' => $tags]);
    }

    public function store(Request $request)
    {
        // Formani validatsiya qilish
        dd($request->all());
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'tags' => 'array|exists:tags,id',  // Tags uchun, agar mavjud bo'lsa
            'preview_image' => 'nullable|string',  // Preview image URL
            'magit remote set-url originin_image' => 'nullable|string',     // Main image URL
        ]);

        // Yangi post yaratish
        $post = Post::create([
            'title' => $validated['title'],
            'slug' => Str::slug($validated['title']),
            'content' => $validated['content'],
            'category_id' => $validated['category_id'],
            'preview_image' => $validated['preview_image'] ?? null,
            'main_image' => $validated['main_image'] ?? null,
        ]);

        // Agar taglar mavjud bo'lsa, ularni postga bog'lash
        if (isset($validated['tags'])) {
            $post->tags()->attach($validated['tags']);
        }

        // Post muvaffaqiyatli yaratildi
        return redirect()->route('admin.posts.index')->with('success', 'Post yaratildi!');
    }
}
