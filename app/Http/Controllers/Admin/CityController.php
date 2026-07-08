<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CityController extends Controller
{
    public function index(Request $request)
    {
        $this->authorizeAdmin();

        $sort = $request->input('sort', 'name');
        $direction = $request->input('direction') === 'desc' ? 'desc' : 'asc';
        $allowedSorts = ['name', 'address', 'routes_count'];

        if (! in_array($sort, $allowedSorts, true)) {
            $sort = 'name';
        }

        $cities = City::withCount('routes')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->input('search');

                $query->where(function ($searchQuery) use ($search) {
                    $searchQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('address', 'like', "%{$search}%");
                });
            })
            ->orderBy($sort, $direction)
            ->paginate(18)
            ->withQueryString();

        return view('admin.cities.index', compact('cities', 'sort', 'direction'));
    }

    public function store(Request $request)
    {
        $this->authorizeAdmin();

        City::create($this->validatedAttributes($request));

        return redirect()
            ->route('admin.cities.index')
            ->with('success', 'City created.');
    }

    public function update(Request $request, City $city)
    {
        $this->authorizeAdmin();

        $city->update($this->validatedAttributes($request, $city));

        $city->routes()->update(['city' => $city->name]);

        return redirect()
            ->route('admin.cities.index')
            ->with('success', 'City updated.');
    }

    public function destroy(City $city)
    {
        $this->authorizeAdmin();

        if ($city->routes()->exists()) {
            return redirect()
                ->route('admin.cities.index')
                ->with('error', 'This city has linked routes. Move or delete those routes before deleting the city.');
        }

        $city->delete();

        return redirect()
            ->route('admin.cities.index')
            ->with('success', 'City deleted.');
    }

    private function authorizeAdmin(): void
    {
        abort_unless(auth()->user()?->is_admin, 403);
    }

    /**
     * @return array<string, string>
     */
    private function validatedAttributes(Request $request, ?City $city = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('cities', 'name')->ignore($city)],
            'address' => ['required', 'string', 'max:255'],
        ]);
    }
}
