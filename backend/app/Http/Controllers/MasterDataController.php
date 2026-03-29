<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\FestivalOffer;
use App\Models\GalleryImage;
use App\Models\NearbyAttraction;
use App\Models\Property;
use App\Models\Testimonial;
use App\Models\TourPackage;
use App\Services\Breadcrumbs;
use App\Traits\ConvertsToWebp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;

class MasterDataController extends Controller
{
    use ConvertsToWebp;
    // ─── TOUR PACKAGES ──────────────────────────────────────────

    public function tourPackages()
    {
        Breadcrumbs::add('Website Master', '#');
        Breadcrumbs::add('Tour Packages');
        $properties = Property::all();
        return view('master-data.tour-packages', compact('properties'));
    }

    public function tourPackagesAjax(Request $request)
    {
        $query = TourPackage::query()->ordered();
        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('price_display', fn($r) => '₹' . number_format($r->price, 0))
            ->addColumn('image_display', fn($r) => $r->image ? '<img src="' . ($this->fullUrl($r->image)) . '" style="width:60px;height:40px;object-fit:cover;border-radius:4px;">' : '-')
            ->addColumn('status', fn($r) => $r->is_active ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>')
            ->addColumn('popular', fn($r) => $r->is_popular ? '<span class="badge bg-warning">Popular</span>' : '')
            ->addColumn('action', fn($r) => $this->actionButtons($r->id, 'tour-package'))
            ->rawColumns(['status', 'popular', 'action', 'image_display'])
            ->make(true);
    }

    public function tourPackageStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:150',
            'duration' => 'required|string|max:50',
            'price' => 'required|numeric|min:0',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif,svg|max:10240',
        ]);

        return $this->saveRecord(new TourPackage(), $request, [
            'property_id', 'name', 'description', 'duration', 'price_label',
            'group_size', 'places_covered', 'is_popular', 'is_active', 'sort_order',
        ], function ($model, $req) {
            $model->price_cents = (int)($req->price * 100);
            $model->slug = Str::slug($req->name);
            $this->handleIncludes($model, $req);
            $this->handleImage($model, $req);
        });
    }

    public function tourPackageShow($id)
    {
        $record = TourPackage::findOrFail($id);
        $record->image_url = $record->image ? $this->fullUrl($record->image) : null;
        return response()->json($record);
    }

    public function tourPackageUpdate(Request $request, $id)
    {
        $model = TourPackage::findOrFail($id);
        $request->validate([
            'name' => 'required|string|max:150',
            'duration' => 'required|string|max:50',
            'price' => 'required|numeric|min:0',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif,svg|max:10240',
        ]);

        return $this->saveRecord($model, $request, [
            'property_id', 'name', 'description', 'duration', 'price_label',
            'group_size', 'places_covered', 'is_popular', 'is_active', 'sort_order',
        ], function ($model, $req) {
            $model->price_cents = (int)($req->price * 100);
            $model->slug = Str::slug($req->name);
            $this->handleIncludes($model, $req);
            $this->handleImage($model, $req);
        });
    }

    public function tourPackageDestroy($id)
    {
        return $this->deleteRecord(TourPackage::class, $id);
    }

    // ─── FESTIVAL OFFERS ────────────────────────────────────────

    public function festivalOffers()
    {
        Breadcrumbs::add('Website Master', '#');
        Breadcrumbs::add('Festival Offers');
        $properties = Property::all();
        return view('master-data.festival-offers', compact('properties'));
    }

    public function festivalOffersAjax(Request $request)
    {
        $query = FestivalOffer::query()->ordered();
        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('price_display', fn($r) => '₹' . number_format($r->price, 0))
            ->addColumn('image_display', fn($r) => $r->image ? '<img src="' . ($this->fullUrl($r->image)) . '" style="width:60px;height:40px;object-fit:cover;border-radius:4px;">' : '-')
            ->addColumn('status', fn($r) => $r->is_active ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>')
            ->addColumn('action', fn($r) => $this->actionButtons($r->id, 'festival-offer'))
            ->rawColumns(['status', 'action', 'image_display'])
            ->make(true);
    }

    public function festivalOfferStore(Request $request)
    {
        $request->validate(['name' => 'required|string|max:150', 'festival_month' => 'required', 'price' => 'required|numeric|min:0']);
        return $this->saveRecord(new FestivalOffer(), $request, [
            'property_id', 'name', 'hindi_name', 'description', 'festival_month',
            'nights', 'highlight_badge', 'gradient_from', 'gradient_to', 'is_active', 'sort_order',
        ], function ($model, $req) {
            $model->price_cents = (int)($req->price * 100);
            $model->per_night_cents = $req->per_night ? (int)($req->per_night * 100) : null;
            $model->slug = Str::slug($req->name);
            $this->handleIncludes($model, $req);
            $this->handleImage($model, $req);
        });
    }

    public function festivalOfferShow($id)
    {
        $r = FestivalOffer::findOrFail($id);
        $r->image_url = $r->image ? $this->fullUrl($r->image) : null;
        return response()->json($r);
    }

    public function festivalOfferUpdate(Request $request, $id)
    {
        $model = FestivalOffer::findOrFail($id);
        $request->validate(['name' => 'required|string|max:150', 'festival_month' => 'required', 'price' => 'required|numeric|min:0']);
        return $this->saveRecord($model, $request, [
            'property_id', 'name', 'hindi_name', 'description', 'festival_month',
            'nights', 'highlight_badge', 'gradient_from', 'gradient_to', 'is_active', 'sort_order',
        ], function ($model, $req) {
            $model->price_cents = (int)($req->price * 100);
            $model->per_night_cents = $req->per_night ? (int)($req->per_night * 100) : null;
            $model->slug = Str::slug($req->name);
            $this->handleIncludes($model, $req);
            $this->handleImage($model, $req);
        });
    }

    public function festivalOfferDestroy($id) { return $this->deleteRecord(FestivalOffer::class, $id); }

    // ─── TESTIMONIALS ───────────────────────────────────────────

    public function testimonials()
    {
        Breadcrumbs::add('Website Master', '#');
        Breadcrumbs::add('Testimonials');
        return view('master-data.testimonials');
    }

    public function testimonialsAjax()
    {
        return DataTables::of(Testimonial::query()->ordered())
            ->addIndexColumn()
            ->addColumn('stars', fn($r) => str_repeat('⭐', $r->rating))
            ->addColumn('status', fn($r) => $r->is_active ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>')
            ->addColumn('featured', fn($r) => $r->is_featured ? '<span class="badge bg-warning">Featured</span>' : '')
            ->addColumn('action', fn($r) => $this->actionButtons($r->id, 'testimonial'))
            ->rawColumns(['status', 'featured', 'action'])
            ->make(true);
    }

    public function testimonialStore(Request $request)
    {
        $request->validate(['guest_name' => 'required|string|max:100', 'review_text' => 'required|string', 'rating' => 'required|integer|min:1|max:5']);
        return $this->saveRecord(new Testimonial(), $request, [
            'property_id', 'guest_name', 'guest_location', 'rating', 'review_text',
            'stay_date', 'source', 'is_featured', 'is_active', 'sort_order',
        ], function ($model, $req) {
            $this->handleImage($model, $req, 'avatar');
        });
    }

    public function testimonialShow($id) { return response()->json(Testimonial::findOrFail($id)); }

    public function testimonialUpdate(Request $request, $id)
    {
        $model = Testimonial::findOrFail($id);
        $request->validate(['guest_name' => 'required|string|max:100', 'review_text' => 'required|string', 'rating' => 'required|integer|min:1|max:5']);
        return $this->saveRecord($model, $request, [
            'property_id', 'guest_name', 'guest_location', 'rating', 'review_text',
            'stay_date', 'source', 'is_featured', 'is_active', 'sort_order',
        ], function ($model, $req) {
            $this->handleImage($model, $req, 'avatar');
        });
    }

    public function testimonialDestroy($id) { return $this->deleteRecord(Testimonial::class, $id); }

    // ─── BLOG POSTS ─────────────────────────────────────────────

    public function blogPosts()
    {
        Breadcrumbs::add('Website Master', '#');
        Breadcrumbs::add('Blog Posts');
        return view('master-data.blog-posts');
    }

    public function blogPostsAjax()
    {
        return DataTables::of(BlogPost::query()->ordered())
            ->addIndexColumn()
            ->addColumn('image_display', fn($r) => $r->image ? '<img src="' . ($this->fullUrl($r->image)) . '" style="width:60px;height:40px;object-fit:cover;border-radius:4px;">' : '-')
            ->addColumn('status', fn($r) => $r->is_published ? '<span class="badge bg-success">Published</span>' : '<span class="badge bg-secondary">Draft</span>')
            ->addColumn('action', fn($r) => $this->actionButtons($r->id, 'blog-post'))
            ->rawColumns(['status', 'action', 'image_display'])
            ->make(true);
    }

    public function blogPostStore(Request $request)
    {
        $request->validate(['title' => 'required|string|max:200', 'content' => 'required|string']);
        return $this->saveRecord(new BlogPost(), $request, [
            'property_id', 'title', 'subtitle', 'excerpt', 'content', 'icon',
            'read_time_min', 'author', 'is_published', 'sort_order',
        ], function ($model, $req) {
            $model->slug = Str::slug($req->title);
            if ($model->is_published && !$model->published_at) $model->published_at = now();
            $this->handleImage($model, $req);
        });
    }

    public function blogPostShow($id)
    {
        $r = BlogPost::findOrFail($id);
        $r->image_url = $r->image ? $this->fullUrl($r->image) : null;
        return response()->json($r);
    }

    public function blogPostUpdate(Request $request, $id)
    {
        $model = BlogPost::findOrFail($id);
        $request->validate(['title' => 'required|string|max:200', 'content' => 'required|string']);
        return $this->saveRecord($model, $request, [
            'property_id', 'title', 'subtitle', 'excerpt', 'content', 'icon',
            'read_time_min', 'author', 'is_published', 'sort_order',
        ], function ($model, $req) {
            $model->slug = Str::slug($req->title);
            if ($model->is_published && !$model->published_at) $model->published_at = now();
            $this->handleImage($model, $req);
        });
    }

    public function blogPostDestroy($id) { return $this->deleteRecord(BlogPost::class, $id); }

    // ─── GALLERY ────────────────────────────────────────────────

    public function gallery()
    {
        Breadcrumbs::add('Website Master', '#');
        Breadcrumbs::add('Gallery');
        return view('master-data.gallery');
    }

    public function galleryAjax()
    {
        return DataTables::of(GalleryImage::query()->ordered())
            ->addIndexColumn()
            ->addColumn('image_display', fn($r) => '<img src="' . ($this->fullUrl($r->image)) . '" style="width:80px;height:50px;object-fit:cover;border-radius:4px;">')
            ->addColumn('status', fn($r) => $r->is_active ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>')
            ->addColumn('action', fn($r) => $this->actionButtons($r->id, 'gallery'))
            ->rawColumns(['status', 'action', 'image_display'])
            ->make(true);
    }

    public function galleryStore(Request $request)
    {
        $request->validate(['title' => 'required|string|max:150', 'image_file' => 'required|image|mimes:jpeg,png,jpg,webp,gif,svg|max:10240']);
        return $this->saveRecord(new GalleryImage(), $request, [
            'property_id', 'title', 'caption', 'category', 'is_active', 'sort_order',
        ], function ($model, $req) {
            $this->handleImage($model, $req);
        });
    }

    public function galleryShow($id)
    {
        $r = GalleryImage::findOrFail($id);
        $r->image_url = $r->image ? $this->fullUrl($r->image) : null;
        return response()->json($r);
    }

    public function galleryUpdate(Request $request, $id)
    {
        $model = GalleryImage::findOrFail($id);
        $request->validate(['title' => 'required|string|max:150']);
        return $this->saveRecord($model, $request, [
            'property_id', 'title', 'caption', 'category', 'is_active', 'sort_order',
        ], function ($model, $req) {
            $this->handleImage($model, $req);
        });
    }

    public function galleryDestroy($id) { return $this->deleteRecord(GalleryImage::class, $id); }

    // ─── NEARBY ATTRACTIONS ─────────────────────────────────────

    public function nearbyAttractions()
    {
        Breadcrumbs::add('Website Master', '#');
        Breadcrumbs::add('Nearby Attractions');
        return view('master-data.nearby-attractions');
    }

    public function nearbyAttractionsAjax()
    {
        return DataTables::of(NearbyAttraction::query()->ordered())
            ->addIndexColumn()
            ->addColumn('image_display', fn($r) => $r->image ? '<img src="' . ($this->fullUrl($r->image)) . '" style="width:60px;height:40px;object-fit:cover;border-radius:4px;">' : '-')
            ->addColumn('status', fn($r) => $r->is_active ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>')
            ->addColumn('action', fn($r) => $this->actionButtons($r->id, 'attraction'))
            ->rawColumns(['status', 'action', 'image_display'])
            ->make(true);
    }

    public function nearbyAttractionStore(Request $request)
    {
        $request->validate(['name' => 'required|string|max:150']);
        return $this->saveRecord(new NearbyAttraction(), $request, [
            'property_id', 'name', 'description', 'distance', 'travel_time',
            'category', 'is_active', 'sort_order',
        ], function ($model, $req) {
            $this->handleHighlights($model, $req);
            $this->handleImage($model, $req);
        });
    }

    public function nearbyAttractionShow($id)
    {
        $r = NearbyAttraction::findOrFail($id);
        $r->image_url = $r->image ? $this->fullUrl($r->image) : null;
        return response()->json($r);
    }

    public function nearbyAttractionUpdate(Request $request, $id)
    {
        $model = NearbyAttraction::findOrFail($id);
        $request->validate(['name' => 'required|string|max:150']);
        return $this->saveRecord($model, $request, [
            'property_id', 'name', 'description', 'distance', 'travel_time',
            'category', 'is_active', 'sort_order',
        ], function ($model, $req) {
            $this->handleHighlights($model, $req);
            $this->handleImage($model, $req);
        });
    }

    public function nearbyAttractionDestroy($id) { return $this->deleteRecord(NearbyAttraction::class, $id); }

    // ─── SHARED HELPERS ─────────────────────────────────────────

    private function saveRecord($model, Request $request, array $fields, callable $extra = null)
    {
        DB::beginTransaction();
        try {
            foreach ($fields as $f) {
                if ($request->has($f)) {
                    $val = $request->input($f);
                    if (in_array($f, ['is_active', 'is_popular', 'is_featured', 'is_published'])) {
                        $val = filter_var($val, FILTER_VALIDATE_BOOLEAN);
                    }
                    $model->{$f} = $val;
                }
            }
            if ($extra) $extra($model, $request);
            $model->save();
            DB::commit();
            return response()->json(['status' => true, 'message' => 'Saved successfully', 'data' => $model]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    private function deleteRecord(string $class, $id)
    {
        $model = $class::find($id);
        if (!$model) return response()->json(['status' => false, 'message' => 'Not found'], 404);

        if ($model->image) {
            $path = str_replace('/storage/', '', $model->image);
            Storage::disk('public')->delete($path);
        }
        $model->delete();
        return response()->json(['status' => true, 'message' => 'Deleted successfully']);
    }

    private function handleImage($model, Request $request, string $field = 'image')
    {
        if ($request->hasFile('image_file')) {
            $this->deleteOldImage($model->{$field});
            $folder = Str::kebab(class_basename($model));
            $model->{$field} = $this->storeAsWebp($request->file('image_file'), $folder);
        }
    }

    private function handleIncludes($model, Request $request)
    {
        if ($request->filled('includes_text')) {
            $model->includes = array_filter(array_map('trim', explode("\n", $request->includes_text)));
        }
    }

    private function handleHighlights($model, Request $request)
    {
        if ($request->filled('highlights_text')) {
            $model->highlights = array_filter(array_map('trim', explode("\n", $request->highlights_text)));
        }
    }

    private function fullUrl(?string $path): string
    {
        if (!$path) return '';
        if (str_starts_with($path, 'http')) return $path;
        return url($path);
    }

    private function actionButtons($id, string $type): string
    {
        return '
            <div class="dropdown ms-auto">
                <a class="dropdown-toggle dropdown-toggle-nocaret" href="#" data-bs-toggle="dropdown">
                    <i class="bx bx-dots-horizontal-rounded text-option"></i>
                </a>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item edit-' . $type . '" href="#" data-id="' . $id . '">Edit</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a href="javascript:void(0);" class="dropdown-item text-danger delete-' . $type . '" data-id="' . $id . '">Delete</a></li>
                </ul>
            </div>
        ';
    }
}
