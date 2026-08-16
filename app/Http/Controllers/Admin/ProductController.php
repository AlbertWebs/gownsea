<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Support\Money;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $query = Product::query()->with('category')->withCount('inquiries');

        if ($search = $request->string('q')->toString()) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        $query->when($request->filled('category_id'), fn ($q) => $q->where('category_id', $request->integer('category_id')))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->when($request->filled('visibility'), fn ($q) => $q->where('visibility', $request->string('visibility')))
            ->when($request->filled('featured'), fn ($q) => $q->where('featured', $request->boolean('featured')))
            ->when($request->filled('availability'), fn ($q) => $q->where('availability', $request->string('availability')));

        $sort = $request->string('sort', 'updated_at')->toString();
        $dir = $request->string('dir', 'desc')->toString() === 'asc' ? 'asc' : 'desc';
        if (! in_array($sort, ['name', 'updated_at', 'price_amount', 'status'], true)) {
            $sort = 'updated_at';
        }

        return view('admin.catalogue.products.index', [
            'products' => $query->orderBy($sort, $dir)->paginate(20)->withQueryString(),
            'categories' => Category::query()->orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.catalogue.products.form', [
            'product' => new Product(['status' => 'draft', 'visibility' => 'public', 'availability' => 'in_stock']),
            'categories' => Category::query()->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $product = Product::query()->create($this->validated($request));
        $this->syncImages($request, $product);
        AuditLog::record($request->user(), 'product.created', $product, [], $product->toArray());

        return redirect()->route('admin.catalogue.products.edit', $product)->with('status', 'Product created successfully.');
    }

    public function show(Product $product): View
    {
        $product->load(['category', 'images', 'inquiries' => fn ($q) => $q->latest()->limit(8)]);

        return view('admin.catalogue.products.show', [
            'product' => $product,
            'inquiryCount' => $product->inquiries()->count(),
            'leadCount' => $product->leads()->count(),
            'salesCount' => $product->saleItems()->count(),
            'revenue' => (int) $product->saleItems()->sum('line_total'),
        ]);
    }

    public function edit(Product $product): View
    {
        $product->load('images');

        return view('admin.catalogue.products.form', [
            'product' => $product,
            'categories' => Category::query()->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $old = $product->toArray();
        $product->update($this->validated($request, $product));
        $this->syncImages($request, $product);
        AuditLog::record($request->user(), 'product.updated', $product, $old, $product->fresh()->toArray());

        return back()->with('status', 'Product updated successfully.');
    }

    public function destroy(Request $request, Product $product): RedirectResponse
    {
        $product->delete();
        AuditLog::record($request->user(), 'product.deleted', $product);

        return redirect()->route('admin.catalogue.products.index')->with('status', 'Product archived.');
    }

    public function duplicate(Product $product): RedirectResponse
    {
        $copy = $product->replicate();
        $copy->slug = $product->slug.'-copy-'.Str::random(4);
        $copy->sku = $product->sku ? $product->sku.'-COPY' : null;
        $copy->name = $product->name.' (Copy)';
        $copy->status = 'draft';
        $copy->save();

        foreach ($product->images as $image) {
            $copy->images()->create($image->only(['path', 'sort_order']));
        }

        return redirect()->route('admin.catalogue.products.edit', $copy)->with('status', 'Product duplicated.');
    }

    public function toggle(Request $request, Product $product, string $field): RedirectResponse
    {
        if (! in_array($field, ['featured', 'status'], true)) {
            abort(404);
        }

        if ($field === 'featured') {
            $product->update(['featured' => ! $product->featured]);
        } else {
            $product->update(['status' => $product->status === 'published' ? 'draft' : 'published']);
        }

        return back()->with('status', 'Product updated successfully.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request, ?Product $product = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:190'],
            'slug' => ['nullable', 'string', 'max:190', 'unique:products,slug,'.($product?->id ?: 'NULL')],
            'sku' => ['nullable', 'string', 'max:80', 'unique:products,sku,'.($product?->id ?: 'NULL')],
            'category_id' => ['nullable', 'exists:categories,id'],
            'short_description' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
            'price_label' => ['nullable', 'string', 'max:80'],
            'price_amount' => ['nullable', 'integer', 'min:0'],
            'sale_price_amount' => ['nullable', 'integer', 'min:0'],
            'stock_quantity' => ['nullable', 'integer', 'min:0'],
            'availability' => ['required', 'in:in_stock,made_to_order,out_of_stock'],
            'featured' => ['sometimes', 'boolean'],
            'status' => ['required', 'in:draft,published,archived'],
            'visibility' => ['required', 'in:public,hidden'],
            'cta' => ['nullable', 'string', 'max:80'],
            'location' => ['nullable', 'string', 'max:120'],
            'url_path' => ['nullable', 'string', 'max:190'],
            'brand' => ['nullable', 'string', 'max:80'],
            'min_order_qty' => ['nullable', 'integer', 'min:1'],
            'fit_note' => ['nullable', 'string'],
            'seo_title' => ['nullable', 'string', 'max:190'],
            'seo_description' => ['nullable', 'string', 'max:500'],
            'seo_keywords' => ['nullable', 'string', 'max:255'],
            'details_text' => ['nullable', 'string'],
            'option_labels' => ['nullable', 'array'],
            'option_labels.*' => ['nullable', 'string', 'max:80'],
            'option_values' => ['nullable', 'array'],
            'option_values.*' => ['nullable', 'string', 'max:500'],
            'size_labels' => ['nullable', 'array'],
            'size_labels.*' => ['nullable', 'string', 'max:40'],
            'size_guides' => ['nullable', 'array'],
            'size_guides.*' => ['nullable', 'string', 'max:190'],
            'tags_text' => ['nullable', 'string', 'max:255'],
            'is_hire' => ['sometimes', 'boolean'],
            'image' => ['nullable', 'image', 'max:4096'],
        ]);

        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);
        $data['featured'] = $request->boolean('featured');
        $data['is_hire'] = $request->boolean('is_hire');
        $data['description'] = strip_tags((string) ($data['description'] ?? ''), '<p><br><strong><b><em><i><u><ul><ol><li><a><h2><h3><blockquote>');
        $data['details'] = collect(preg_split('/\r\n|\r|\n/', (string) $request->input('details_text')))
            ->map(fn ($line) => trim($line))
            ->filter()
            ->values()
            ->all();
        $data['min_order_qty'] = $data['min_order_qty'] ?? 1;
        $data['tags'] = collect(explode(',', (string) $request->input('tags_text')))
            ->map(fn ($tag) => trim($tag))
            ->filter()
            ->values()
            ->all();
        $options = [];
        foreach ($request->input('option_labels', []) as $index => $label) {
            $label = trim((string) $label);
            $values = collect(explode(',', (string) ($request->input('option_values.'.$index) ?? '')))
                ->map(fn ($value) => trim($value))
                ->filter()
                ->values()
                ->all();
            if ($label !== '' && $values !== []) {
                $options[$label] = $values;
            }
        }
        $data['options'] = $options;
        $data['size_guide'] = collect($request->input('size_labels', []))
            ->map(function ($size, $index) use ($request) {
                $size = trim((string) $size);
                $guide = trim((string) ($request->input('size_guides.'.$index) ?? ''));

                return $size === '' ? null : ['size' => $size, 'guide' => $guide];
            })
            ->filter()
            ->values()
            ->all();

        unset($data['details_text'], $data['image'], $data['option_labels'], $data['option_values'], $data['size_labels'], $data['size_guides'], $data['tags_text']);

        if (! $data['price_amount'] && ! empty($data['price_label'])) {
            $data['price_amount'] = Money::parseLabel($data['price_label']);
        }

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $name = 'admin-'.Str::random(8).'.'.$file->getClientOriginalExtension();
            $file->move(public_path('images/products'), $name);
            $data['image'] = '/images/products/'.$name;
        }

        return $data;
    }

    private function syncImages(Request $request, Product $product): void
    {
        if (! $request->hasFile('gallery')) {
            return;
        }

        $sort = (int) $product->images()->max('sort_order');
        foreach ($request->file('gallery', []) as $file) {
            if (! $file->isValid()) {
                continue;
            }
            $sort++;
            $name = 'gallery-'.Str::random(8).'.'.$file->getClientOriginalExtension();
            $file->move(public_path('images/products'), $name);
            $product->images()->create([
                'path' => '/images/products/'.$name,
                'sort_order' => $sort,
            ]);
        }
    }
}
