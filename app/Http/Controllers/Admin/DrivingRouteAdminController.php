<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\DrivingRoute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class DrivingRouteAdminController extends Controller
{
    public function index()
    {
        $this->authorizeAdmin();

        $routes = DrivingRoute::withCount(['points', 'purchases'])
            ->with('cityModel')
            ->latest()
            ->paginate(12);

        return view('admin.driving-routes.index', compact('routes'));
    }

    public function create()
    {
        $this->authorizeAdmin();

        return view('admin.driving-routes.create', [
            'route' => new DrivingRoute(['is_active' => true]),
            'points' => collect(),
            'cities' => City::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $this->authorizeAdmin();

        $attributes = $this->validatedAttributes($request);

        DB::transaction(function () use ($request, $attributes) {
            if ($request->hasFile('preview_pdf')) {
                $attributes['preview_pdf_path'] = $request
                    ->file('preview_pdf')
                    ->store('route-previews', 'public');
            }

            $route = DrivingRoute::create($attributes);
            $this->syncPoints($route, $request->input('points', []));
        });

        return redirect()
            ->route('admin.driving-routes.index')
            ->with('success', 'Route created.');
    }

    public function show(DrivingRoute $drivingRoute)
    {
        $this->authorizeAdmin();

        return redirect()->route('admin.driving-routes.edit', $drivingRoute);
    }

    public function edit(DrivingRoute $drivingRoute)
    {
        $this->authorizeAdmin();

        return view('admin.driving-routes.edit', [
            'route' => $drivingRoute,
            'points' => $drivingRoute->points,
            'cities' => City::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, DrivingRoute $drivingRoute)
    {
        $this->authorizeAdmin();

        $attributes = $this->validatedAttributes($request);

        DB::transaction(function () use ($request, $drivingRoute, $attributes) {
            if ($request->hasFile('preview_pdf')) {
                if ($drivingRoute->preview_pdf_path) {
                    Storage::disk('public')->delete($drivingRoute->preview_pdf_path);
                }

                $attributes['preview_pdf_path'] = $request
                    ->file('preview_pdf')
                    ->store('route-previews', 'public');
            }

            $drivingRoute->update($attributes);
            $this->syncPoints($drivingRoute, $request->input('points', []));
        });

        return redirect()
            ->route('admin.driving-routes.edit', $drivingRoute)
            ->with('success', 'Route updated.');
    }

    public function destroy(DrivingRoute $drivingRoute)
    {
        $this->authorizeAdmin();

        if ($drivingRoute->preview_pdf_path) {
            Storage::disk('public')->delete($drivingRoute->preview_pdf_path);
        }

        $drivingRoute->delete();

        return redirect()
            ->route('admin.driving-routes.index')
            ->with('success', 'Route deleted.');
    }

    private function authorizeAdmin(): void
    {
        abort_unless(auth()->user()?->is_admin, 403);
    }

    /**
     * @return array<string, mixed>
     */
    private function validatedAttributes(Request $request): array
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'city_id' => ['required', 'integer', 'exists:cities,id'],
            'province' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'route_duration_minutes' => ['nullable', 'integer', 'min:1'],
            'route_length_km' => ['nullable', 'numeric', 'min:0'],
            'start_label' => ['required', 'string', 'max:255'],
            'start_lat' => ['nullable', 'numeric', 'between:-90,90'],
            'start_lng' => ['nullable', 'numeric', 'between:-180,180'],
            'destination_label' => ['required', 'string', 'max:255'],
            'end_lat' => ['nullable', 'numeric', 'between:-90,90'],
            'end_lng' => ['nullable', 'numeric', 'between:-180,180'],
            'price' => ['required', 'numeric', 'min:0'],
            'access_limit' => ['required', 'integer', 'min:1', 'max:1000'],
            'is_active' => ['sometimes', 'boolean'],
            'preview_pdf' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
            'points' => ['nullable', 'array'],
            'points.*.sort_order' => ['required', 'integer', 'min:1'],
            'points.*.maneuver' => ['required', Rule::in(['continue', 'turn_left', 'turn_right'])],
            'points.*.instruction' => ['nullable', 'string', 'max:500'],
            'points.*.lat' => ['nullable', 'numeric', 'between:-90,90'],
            'points.*.lng' => ['nullable', 'numeric', 'between:-180,180'],
            'points.*.distance_km' => ['nullable', 'numeric', 'min:0'],
            'points.*.duration' => ['nullable', 'string', 'max:50'],
        ], [
            'preview_pdf.uploaded' => 'The preview pdf failed to upload. Please ensure the file is under 2MB.',
        ]);

        $city = City::findOrFail($validated['city_id']);

        return [
            'title' => $validated['title'],
            'city_id' => $city->id,
            'city' => $city->name,
            'province' => $validated['province'],
            'description' => $validated['description'] ?? null,
            'route_duration_minutes' => $validated['route_duration_minutes'] ?? null,
            'route_length_km' => $validated['route_length_km'] ?? null,
            'start_label' => $validated['start_label'],
            'start_lat' => $validated['start_lat'] ?? null,
            'start_lng' => $validated['start_lng'] ?? null,
            'destination_label' => $validated['destination_label'],
            'end_lat' => $validated['end_lat'] ?? null,
            'end_lng' => $validated['end_lng'] ?? null,
            'price' => $validated['price'],
            'access_limit' => $validated['access_limit'],
            'is_active' => $request->boolean('is_active'),
        ];
    }

    /**
     * @param array<int, array<string, mixed>> $points
     */
    private function syncPoints(DrivingRoute $route, array $points): void
    {
        $route->points()->delete();

        foreach ($points as $index => $point) {
            $route->points()->create([
                'sort_order' => $point['sort_order'] ?? $index + 1,
                'maneuver' => $point['maneuver'] ?? 'continue',
                'instruction' => $point['instruction'] ?? null,
                'lat' => $point['lat'] ?? null,
                'lng' => $point['lng'] ?? null,
                'distance_km' => $point['distance_km'] ?? null,
                'duration' => $point['duration'] ?? null,
            ]);
        }
    }
}
