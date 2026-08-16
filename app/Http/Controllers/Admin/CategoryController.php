<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(Request $request): View
    {
        $categories = Category::query()
            ->withCount('products')
            ->with('parent')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $grouped = $categories->groupBy(fn (Category $category) => $category->parent_id);
        $flatten = function (?int $parentId, int $depth) use (&$flatten, $grouped) {
            return collect($grouped->get($parentId, collect()))->flatMap(function (Category $category) use ($flatten, $depth) {
                $category->setAttribute('depth', $depth);

                return collect([$category])->concat($flatten($category->id, $depth + 1));
            });
        };

        $tree = $flatten(null, 0);
        $seen = $tree->pluck('id');
        $tree = $tree->concat(
            $categories->reject(fn (Category $category) => $seen->contains($category->id))->each(fn (Category $category) => $category->setAttribute('depth', 0))
        );

        $query = trim((string) $request->get('q'));
        if ($query !== '') {
            $tree = $tree->filter(fn (Category $category) => str_contains(mb_strtolower($category->name.' '.$category->slug), mb_strtolower($query)));
        }

        $status = (string) $request->get('status');
        if ($status === 'active') {
            $tree = $tree->where('is_active', true);
        } elseif ($status === 'inactive') {
            $tree = $tree->where('is_active', false);
        }

        return view('admin.catalogue.categories.index', [
            'categories' => $tree->values(),
            'stats' => [
                'total' => $categories->count(),
                'active' => $categories->where('is_active', true)->count(),
                'inactive' => $categories->where('is_active', false)->count(),
                'with_products' => $categories->where('products_count', '>', 0)->count(),
            ],
        ]);
    }

    public function create(): View
    {
        return view('admin.catalogue.categories.form', [
            'category' => new Category(['is_active' => true]),
            'parents' => Category::query()->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Category::query()->create($this->validated($request));

        return redirect()->route('admin.catalogue.categories.index')->with('status', 'Category created successfully.');
    }

    public function edit(Category $category): View
    {
        return view('admin.catalogue.categories.form', [
            'category' => $category,
            'parents' => Category::query()->where('id', '!=', $category->id)->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $category->update($this->validated($request, $category));

        return back()->with('status', 'Category updated successfully.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        if ($category->products()->exists()) {
            return back()->withErrors(['delete' => 'Reassign products before deleting this category.']);
        }

        $category->delete();

        return back()->with('status', 'Category deleted.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request, ?Category $category = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:190'],
            'slug' => ['nullable', 'string', 'max:190', 'unique:categories,slug,'.($category?->id ?: 'NULL')],
            'description' => ['nullable', 'string'],
            'parent_id' => ['nullable', 'exists:categories,id'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
            'seo_title' => ['nullable', 'string', 'max:190'],
            'seo_description' => ['nullable', 'string', 'max:500'],
        ]);
        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);
        $data['is_active'] = $request->boolean('is_active');
        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['description'] = strip_tags((string) ($data['description'] ?? ''), '<p><br><strong><b><em><i><u><ul><ol><li><a>');

        return $data;
    }
}
