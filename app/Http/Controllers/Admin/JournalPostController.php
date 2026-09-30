<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JournalPost;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Contracts\View\View;

class JournalPostController extends Controller
{
    public function index(Request $request): View
    {
        $query = JournalPost::query()->latest('updated_at');
        if ($request->filled('q')) {
            $term = '%'.$request->string('q')->toString().'%';
            $query->where(fn ($query) => $query->where('title', 'like', $term)->orWhere('slug', 'like', $term));
        }
        if (in_array($request->query('status'), ['draft', 'published'], true)) {
            $query->where('status', $request->query('status'));
        }

        return view('admin.journal.index', [
            'posts' => $query->paginate(20)->withQueryString(),
            'counts' => [
                'all' => JournalPost::count(),
                'published' => JournalPost::published()->count(),
                'draft' => JournalPost::where('status', 'draft')->count(),
            ],
        ]);
    }

    public function create(): View
    {
        return view('admin.journal.form', ['post' => new JournalPost(['status' => 'draft'])]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['author_id'] = $request->user()->id;
        $post = JournalPost::create($data);

        return redirect()->route('admin.journal.edit', $post)->with('status', 'Article created.');
    }

    public function edit(JournalPost $journalPost): View
    {
        return view('admin.journal.form', ['post' => $journalPost]);
    }

    public function update(Request $request, JournalPost $journalPost): RedirectResponse
    {
        $journalPost->update($this->validated($request, $journalPost));

        return back()->with('status', 'Article updated.');
    }

    public function destroy(JournalPost $journalPost): RedirectResponse
    {
        $this->deleteImage($journalPost->image);
        $journalPost->delete();

        return redirect()->route('admin.journal.index')->with('status', 'Article deleted.');
    }

    /** @return array<string, mixed> */
    private function validated(Request $request, ?JournalPost $post = null): array
    {
        $request->merge(['slug' => Str::slug((string) ($request->input('slug') ?: $request->input('title')))]);
        $data = $request->validate([
            'title' => ['required', 'string', 'max:190'],
            'slug' => ['required', 'string', 'max:190', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', 'unique:journal_posts,slug,'.($post?->id ?? 'NULL')],
            'category' => ['nullable', 'string', 'max:100'],
            'excerpt' => ['required', 'string', 'max:600'],
            'body' => ['nullable', 'string', 'max:50000'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'status' => ['required', 'in:draft,published'],
            'published_at' => ['nullable', 'date'],
            'seo_title' => ['nullable', 'string', 'max:190'],
            'seo_description' => ['nullable', 'string', 'max:500'],
            'remove_image' => ['nullable', 'boolean'],
        ]);

        $data['category'] = $data['category'] ?: 'General';
        $data['body'] = $this->sanitizeHtml($data['body'] ?? '');
        $data['published_at'] = $data['published_at'] ?: ($data['status'] === 'published' ? now()->toDateString() : null);

        if ($request->boolean('remove_image') && ! $request->hasFile('image')) {
            $this->deleteImage($post?->image);
            $data['image'] = null;
        } elseif ($request->hasFile('image')) {
            $file = $request->file('image');
            $directory = public_path('images/blogs');
            if (! is_dir($directory)) {
                mkdir($directory, 0755, true);
            }
            $name = 'article-'.Str::random(12).'.'.strtolower($file->getClientOriginalExtension());
            $file->move($directory, $name);
            $this->deleteImage($post?->image);
            $data['image'] = '/images/blogs/'.$name;
        } else {
            unset($data['image']);
        }

        unset($data['remove_image']);
        return $data;
    }

    private function sanitizeHtml(string $html): string
    {
        $html = strip_tags($html, '<p><br><h2><h3><strong><b><em><i><u><ul><ol><li><blockquote><a>');
        return preg_replace_callback('~<(\/?)(p|br|h2|h3|strong|b|em|i|u|ul|ol|li|blockquote|a)\b([^>]*)>~i', function (array $match) {
            $closing = $match[1] === '/';
            $tag = strtolower($match[2]);
            if ($closing) {
                return '</'.$tag.'>';
            }
            if ($tag !== 'a') {
                return '<'.$tag.'>';
            }

            if (preg_match('/\bhref\s*=\s*(["\'])(.*?)\1/i', $match[3], $href) !== 1) {
                return '<a>';
            }
            $url = trim(html_entity_decode($href[2], ENT_QUOTES | ENT_HTML5));
            if (preg_match('/^(?:https?:|mailto:|tel:|\/|#)/i', $url) !== 1 || str_starts_with($url, '//')) {
                return '<a>';
            }
            return '<a href="'.e($url).'">';
        }, $html) ?? $html;
    }

    private function deleteImage(?string $url): void
    {
        if (! $url || ! str_starts_with($url, '/images/blogs/article-')) {
            return;
        }
        $filename = basename($url);
        $path = public_path('images/blogs/'.$filename);
        if (is_file($path)) {
            unlink($path);
        }
    }
}
