<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BlogPostController extends Controller
{
    public function index(Request $request)
    {
        $this->authorizeAdmin();

        $sort = $request->input('sort', 'title');
        $direction = $request->input('direction') === 'desc' ? 'desc' : 'asc';
        $allowedSorts = ['title', 'category', 'is_published', 'published_at'];

        if (! in_array($sort, $allowedSorts, true)) {
            $sort = 'title';
        }

        $posts = BlogPost::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->input('search');

                $query->where(function ($searchQuery) use ($search) {
                    $searchQuery->where('title', 'like', "%{$search}%")
                        ->orWhere('category', 'like', "%{$search}%");
                });
            })
            ->orderBy($sort, $direction)
            ->paginate(15)
            ->withQueryString();

        return view('admin.blog-posts.index', compact('posts', 'sort', 'direction'));
    }

    public function create()
    {
        $this->authorizeAdmin();

        return view('admin.blog-posts.create');
    }

    public function store(Request $request)
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:100'],
            'excerpt' => ['required', 'string', 'max:500'],
            'body' => ['required', 'string'],
            'read_time' => ['required', 'string', 'max:20'],
            'is_published' => ['sometimes', 'boolean'],
            'published_at' => ['nullable', 'date'],
            'featured_image' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('featured_image')) {
            $validated['featured_image'] = $request->file('featured_image')->store('blog', 'public');
        }

        $validated['is_published'] = $request->boolean('is_published');

        BlogPost::create($validated);

        return redirect()
            ->route('admin.blog-posts.index')
            ->with('success', 'Blog post created.');
    }

    public function edit(BlogPost $blog_post)
    {
        $this->authorizeAdmin();

        return view('admin.blog-posts.edit', ['post' => $blog_post]);
    }

    public function update(Request $request, BlogPost $blog_post)
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:100'],
            'excerpt' => ['required', 'string', 'max:500'],
            'body' => ['required', 'string'],
            'read_time' => ['required', 'string', 'max:20'],
            'is_published' => ['sometimes', 'boolean'],
            'published_at' => ['nullable', 'date'],
            'featured_image' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('featured_image')) {
            if ($blog_post->featured_image) {
                Storage::disk('public')->delete($blog_post->featured_image);
            }

            $validated['featured_image'] = $request->file('featured_image')->store('blog', 'public');
        }

        $validated['is_published'] = $request->boolean('is_published');

        $blog_post->update($validated);

        return redirect()
            ->route('admin.blog-posts.index')
            ->with('success', 'Blog post updated.');
    }

    public function destroy(BlogPost $blog_post)
    {
        $this->authorizeAdmin();

        if ($blog_post->featured_image) {
            Storage::disk('public')->delete($blog_post->featured_image);
        }

        $blog_post->delete();

        return redirect()
            ->route('admin.blog-posts.index')
            ->with('success', 'Blog post deleted.');
    }

    private function authorizeAdmin(): void
    {
        abort_unless(auth()->user()?->is_admin, 403);
    }
}
