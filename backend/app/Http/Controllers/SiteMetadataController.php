<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\SiteMetadata;
use App\Services\Breadcrumbs;
use App\Traits\ConvertsToWebp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SiteMetadataController extends Controller
{
    use ConvertsToWebp;
    public function index()
    {
        Breadcrumbs::add('Website Master', '#');
        Breadcrumbs::add('Site Metadata');

        $property = Property::first();
        $groups = SiteMetadata::where('property_id', $property?->id)
            ->orderBy('group')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('group');

        $groupLabels = [
            'general' => ['label' => 'General Info', 'icon' => 'bx bx-info-circle'],
            'contact' => ['label' => 'Contact Details', 'icon' => 'bx bx-phone'],
            'social' => ['label' => 'Social Media', 'icon' => 'bx bx-share-alt'],
            'seo' => ['label' => 'SEO / Meta Tags', 'icon' => 'bx bx-search-alt'],
            'hero' => ['label' => 'Hero Section', 'icon' => 'bx bx-image'],
            'policies' => ['label' => 'Policies & Timings', 'icon' => 'bx bx-time'],
            'booking' => ['label' => 'Booking Settings', 'icon' => 'bx bx-calendar-check'],
        ];

        return view('master-data.site-metadata', compact('groups', 'groupLabels'));
    }

    public function update(Request $request)
    {
        $property = Property::first();
        $metadata = $request->input('meta', []);

        // Validate all image files
        $request->validate([
            'meta_file.*' => 'nullable|file|mimes:jpeg,jpg,png,gif,webp,svg|max:10240',
        ]);

        foreach ($metadata as $id => $value) {
            $record = SiteMetadata::find($id);
            if (!$record) continue;

            // Handle image uploads — auto-convert to WebP
            if ($record->type === 'image' && $request->hasFile("meta_file.{$id}")) {
                $this->deleteOldImage($record->value);
                $record->value = $this->storeAsWebp($request->file("meta_file.{$id}"), 'metadata');
                $record->save();
                continue;
            }

            $record->value = $value;
            $record->save();
        }

        return response()->json(['status' => true, 'message' => 'Metadata updated successfully']);
    }

    public function addField(Request $request)
    {
        $request->validate([
            'group' => 'required|string|max:50',
            'key' => 'required|string|max:100',
            'label' => 'required|string|max:150',
            'type' => 'required|in:text,textarea,email,tel,url,image,color,number,time',
        ]);

        $property = Property::first();

        $exists = SiteMetadata::where('property_id', $property?->id)
            ->where('group', $request->group)
            ->where('key', $request->key)
            ->exists();

        if ($exists) {
            return response()->json(['status' => false, 'message' => 'Key already exists in this group'], 422);
        }

        $maxOrder = SiteMetadata::where('property_id', $property?->id)
            ->where('group', $request->group)
            ->max('sort_order') ?? 0;

        SiteMetadata::create([
            'property_id' => $property?->id,
            'group' => $request->group,
            'key' => $request->key,
            'label' => $request->label,
            'value' => $request->value ?? '',
            'type' => $request->type,
            'sort_order' => $maxOrder + 1,
        ]);

        return response()->json(['status' => true, 'message' => 'Field added successfully']);
    }

    public function deleteField($id)
    {
        $record = SiteMetadata::find($id);
        if (!$record) return response()->json(['status' => false, 'message' => 'Not found'], 404);

        if ($record->type === 'image' && $record->value) {
            $path = str_replace('/storage/', '', $record->value);
            Storage::disk('public')->delete($path);
        }

        $record->delete();
        return response()->json(['status' => true, 'message' => 'Field deleted']);
    }
}
