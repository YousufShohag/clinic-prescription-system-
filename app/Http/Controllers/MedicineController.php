<?php 

namespace App\Http\Controllers;

// use App\Models\Medicine;
// use App\Models\Category;
// use Illuminate\Http\Request;

// use Illuminate\Support\Str;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\LazyCollection;
use Illuminate\Support\Str;
use App\Models\Medicine;
use App\Models\Category;



use Carbon\Carbon;

class MedicineController extends Controller
{
    // public function index()
    // {
    //     $medicines = Medicine::with('category')->latest()->paginate(50);
    //     $categories = Category::all(); // add this
    //     return view('admin.medicines.index', compact('medicines','categories'));
    // }
public function index(Request $request)
{
    // Start query
    $query = Medicine::with('category');

    // 🔍 Search by name
    if ($request->filled('search')) {
        $query->where('name', 'like', '%' . $request->search . '%');
    }

    // 📂 Filter by category
    if ($request->filled('category')) {
        $query->where('category_id', $request->category);
    }

    // ✅ Filter by status (1 = Active, 0 = Inactive)
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    // ⏳ Filter by expiry
    if ($request->expiry === 'expired') {
        $query->whereDate('expiry_date', '<', Carbon::today());
    } elseif ($request->expiry === 'valid') {
        $query->whereDate('expiry_date', '>=', Carbon::today());
    }

    // Get paginated results
    $medicines = $query->orderBy('name')->paginate(10);

    // For category filter dropdown
    $categories = Category::orderBy('name')->get();

    return view('admin.medicines.index', compact('medicines', 'categories'));
}
    public function create()
    {
        $categories = Category::where('status', 1)->get();
        return view('admin.medicines.create', compact('categories'));
    }

    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'category_id' => 'required|exists:categories,id',
    //         'name' => 'required|string|max:255',
    //         'type' => 'required|string|max:100',
    //         'stock' => 'required|integer|min:0',
    //         'price' => 'required|numeric|min:0',
    //         'expiry_date' => 'required|date',
    //         'description' => 'nullable|string',
    //         'notes' => 'nullable|string',
    //         'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    //         'status' => 'required|boolean',
    //     ]);

    //     $imagePath = null;
    //     if ($request->hasFile('image')) {
    //         $imagePath = $request->file('image')->store('medicines', 'public');
    //     }

        

    //     Medicine::create($request->all() + ['image' => $imagePath]);

    //     return redirect()->route('medicines.index')->with('success', 'Medicine added successfully!');
    // }

    public function store(Request $request)
{
    $request->validate([
        'category_id' => 'required|exists:categories,id',
        'name' => 'required|string|max:255',
        'generic'      => ['nullable','string','max:255'],
        'strength'     => ['nullable','string','max:255'],
        'manufacturer' => ['nullable','string','max:255'],
        'type' => 'required|string|max:100',
        'stock' => 'required|integer|min:0',
        'price' => 'required|numeric|min:0',
        'expiry_date' => 'required|date',
        'description' => 'nullable|string',
        'notes' => 'nullable|string',
        'status' => 'required|boolean',
        'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    ]);

    $medicine = new Medicine($request->except('image'));

    if ($request->hasFile('image')) {
        // Save image into storage/app/public/medicines
        $path = $request->file('image')->store('medicines', 'public');
        $medicine->image = $path; // e.g. medicines/filename.jpg
    }

    $medicine->save();

    return redirect()->route('medicines.index')->with('success', 'Medicine added successfully');
}


    public function edit(Medicine $medicine)
    {
        $categories = Category::where('status', 1)->get();
        return view('admin.medicines.edit', compact('medicine','categories'));
    }

    // public function update(Request $request, Medicine $medicine)
    // {
    //     $request->validate([
    //         'category_id' => 'required|exists:categories,id',
    //         'name' => 'required|string|max:255',
    //         'type' => 'required|string|max:100',
    //         'stock' => 'required|integer|min:0',
    //         'price' => 'required|numeric|min:0',
    //         'expiry_date' => 'required|date',
    //         'description' => 'nullable|string',
    //         'notes' => 'nullable|string',
    //         'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    //         'status' => 'required|boolean',
    //     ]);

    //     $imagePath = $medicine->image;
    //     if ($request->hasFile('image')) {
    //         $imagePath = $request->file('image')->store('medicines', 'public');
    //     }

    //     $medicine->update($request->all() + ['image' => $imagePath]);

    //     return redirect()->route('medicines.index')->with('success', 'Medicine updated successfully!');
    // }

    public function update(Request $request, Medicine $medicine)
{
    $request->validate([
        'category_id' => 'required|exists:categories,id',
        'name' => 'required|string|max:255',
        'type' => 'required|string|max:100',
        'stock' => 'required|integer|min:0',
        'price' => 'required|numeric|min:0',
        'expiry_date' => 'required|date',
        'description' => 'nullable|string',
        'notes' => 'nullable|string',
        'status' => 'required|boolean',
        'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    ]);

    $medicine->fill($request->except('image'));

    if ($request->hasFile('image')) {
        // (Optional) delete old image
        if ($medicine->image && \Storage::disk('public')->exists($medicine->image)) {
            \Storage::disk('public')->delete($medicine->image);
        }

        $path = $request->file('image')->store('medicines', 'public');
        $medicine->image = $path;
    }

    $medicine->save();

    return redirect()->route('medicines.index')->with('success', 'Medicine updated successfully');
}


    public function destroy(Medicine $medicine)
    {
        if ($medicine->image && file_exists(storage_path('app/public/'.$medicine->image))) {
            unlink(storage_path('app/public/'.$medicine->image));
        }
        $medicine->delete();
        return redirect()->route('medicines.index')->with('success', 'Medicine deleted successfully!');
    }

    public function history(Medicine $medicine)
{
    $logs = $medicine->stockHistories()->latest()->paginate(20);
    return view('admin.medicines.history', compact('medicine', 'logs'));
}

    // public function search(Request $request)
    // {
    //     $query = $request->get('q', '');
    //     $medicines = Medicine::where('name', 'LIKE', "%$query%")
    //         ->take(10)
    //         ->get();

    //     return response()->json($medicines);
    // }


public function importCsv(Request $request)
{
    $request->validate([
        'csv' => ['required', 'file', 'mimes:csv,txt', 'max:51200'],
    ]);

    @ini_set('memory_limit', '1024M');
    DB::disableQueryLog();

    $path = $request->file('csv')->getRealPath();

    $rows = LazyCollection::make(function () use ($path) {
        $handle = fopen($path, 'r');
        if ($handle === false) { yield from []; return; }

        $header = fgetcsv($handle);
        if ($header === false) { fclose($handle); yield from []; return; }

        $normalize = fn($h) => Str::of($h)->lower()->replace(' ', '_')->replace('-', '_')->trim()->toString();
        $headers   = array_map($normalize, $header);
        $index     = array_flip($headers);

        $required  = ['category_id','name','generic','strength','manufacturer','type'];
        foreach ($required as $r) {
            if (!array_key_exists($r, $index)) {
                fclose($handle);
                yield ['__error__' => "Missing required header: {$r}"];
                return;
            }
        }

        $rowNum = 1;
        while (($row = fgetcsv($handle)) !== false) {
            $rowNum++;
            if (count(array_filter($row, fn($v) => trim((string)$v) !== '')) === 0) continue;

            yield [
                'category_id'  => trim((string)($row[$index['category_id']]  ?? '')),
                'name'         => trim((string)($row[$index['name']]         ?? '')),
                'generic'      => trim((string)($row[$index['generic']]      ?? '')),
                'strength'     => trim((string)($row[$index['strength']]     ?? '')),
                'manufacturer' => trim((string)($row[$index['manufacturer']] ?? '')),
                'type'         => trim((string)($row[$index['type']]         ?? '')),
                '__row__'      => $rowNum,
            ];
        }

        fclose($handle);
    });

    $validCategoryIds = Category::pluck('id')->flip();

    $headerError = null;
    $defaultExpiry = Carbon::today()->addYear()->toDateString(); // ▶ expiry = next year
    $now = now();

    $rows->chunk(1000)->each(function ($chunk) use ($validCategoryIds, &$headerError, $defaultExpiry, $now) {
        if ($headerError !== null) return;

        foreach ($chunk as $r) {
            if (isset($r['__error__'])) { $headerError = $r['__error__']; return; }
        }
        if ($headerError !== null) return;

        $batch = [];
        foreach ($chunk as $r) {
            // Minimal validation
            if ($r['category_id'] === '' || $r['name'] === '' || $r['generic'] === '' || $r['type'] === '') {
                continue;
            }
            if (!ctype_digit($r['category_id'])) continue;

            $cid = (int) $r['category_id'];
            if (!$validCategoryIds->has($cid)) continue;

            $batch[] = [
                'category_id'  => $cid,
                'name'         => $r['name'],
                'generic'      => $r['generic'],
                'strength'     => $r['strength'] ?: null,
                'manufacturer' => $r['manufacturer'] ?: null,
                'type'         => $r['type'],

                // ▶ Defaults for other fields
                'stock'        => 0,
                'price'        => 0,
                'expiry_date'  => $defaultExpiry, // ▶ next year
                'description'  => null,
                'notes'        => null,
                'image'        => null,
                'status'       => 1,

                'created_at'   => $now,
                'updated_at'   => $now,
            ];
        }

        if (!empty($batch)) {
            // Upsert on (name, generic, strength)
            DB::table('medicines')->upsert(
                $batch,
                ['name','generic','strength'],
                [
                    'category_id','manufacturer','type',
                    'stock','price','expiry_date','description','notes','image','status',
                    'updated_at'
                ]
            );
        }
    });

    if ($headerError) {
        return back()->withErrors(['csv' => $headerError]);
    }

    return back()->with('import_summary', 'CSV import complete. Non-CSV fields set to defaults; expiry set to next year.');
}


// app/Http/Controllers/MedicineController.php
// public function ajaxSearch(Request $request)
// {
//     $term = trim((string)$request->get('term', '')); // Select2 uses 'term'
//     $page = max(1, (int)$request->get('page', 1));
//     $perPage = 30;

//     if ($term === '') {
//         return response()->json(['results' => [], 'pagination' => ['more' => false]]);
//     }

//     $like   = '%'.$term.'%';
//     $prefix = $term.'%';

//     $base = \App\Models\Medicine::query()
//         ->where('status', 1)
//         ->select('id','name','generic','strength','manufacturer','price')
//         ->where(function ($w) use ($like) {
//             $w->where('name','like',$like)
//               ->orWhere('generic','like',$like)
//               ->orWhere('manufacturer','like',$like)
//               ->orWhere('strength','like',$like);
//         })
//         ->orderByRaw('
//             (CASE 
//                WHEN name LIKE ? THEN 1
//                WHEN generic LIKE ? THEN 2
//                WHEN manufacturer LIKE ? THEN 3
//                ELSE 4
//             END), name asc
//         ', [$prefix, $prefix, $prefix]);

//     $results = $base->skip(($page-1)*$perPage)->take($perPage)->get();

//     return response()->json([
//         'results' => $results->map(fn($m) => [
//             'id'    => $m->id,
//             'text'  => trim($m->name . ' — ' . ($m->generic ?? '') . ($m->strength ? " ({$m->strength})" : '')),
//             // extra fields for richer UI
//             'name'         => $m->name,
//             'generic'      => $m->generic,
//             'strength'     => $m->strength,
//             'manufacturer' => $m->manufacturer,
//             'price'        => $m->price,
//         ]),
//         'pagination' => ['more' => $results->count() === $perPage],
//     ]);
// }

// public function ajaxSearch(\Illuminate\Http\Request $request)
// {
//     $term = trim((string)$request->get('term', ''));
//     $page = max(1, (int)$request->get('page', 1));
//     $perPage = 30;

//     if ($term === '') {
//         return response()->json(['results' => [], 'pagination' => ['more' => false]]);
//     }

//     $like   = '%'.$term.'%';
//     $prefix = $term.'%';

//     $base = \App\Models\Medicine::query()
//         ->where('status', 1)
//         ->select('id','name','generic','strength','manufacturer','price')
//         ->where(function ($w) use ($like) {
//             $w->where('name','like',$like)
//               ->orWhere('generic','like',$like)
//               ->orWhere('manufacturer','like',$like)
//               ->orWhere('strength','like',$like);
//         })
//         ->orderByRaw('
//             (CASE 
//                WHEN name LIKE ? THEN 1
//                WHEN generic LIKE ? THEN 2
//                WHEN manufacturer LIKE ? THEN 3
//                ELSE 4
//             END), name asc
//         ', [$prefix, $prefix, $prefix]);

//     $results = $base->skip(($page-1)*$perPage)->take($perPage)->get();

//     return response()->json([
//         'results' => $results->map(fn($m) => [
//             'id'    => $m->id,
//             'text'  => trim($m->name . ' — ' . ($m->generic ?? '') . ($m->strength ? " ({$m->strength})" : '')),
//             'name'         => $m->name,
//             'generic'      => $m->generic,
//             'strength'     => $m->strength,
//             'manufacturer' => $m->manufacturer,
//             'price'        => $m->price,
//         ]),
//         'pagination' => ['more' => $results->count() === $perPage],
//     ]);
// }

// in MedicineController
// public function ajaxSearch(\Illuminate\Http\Request $request)
// {
//     $term = trim((string)$request->get('term', ''));
//     $page = max(1, (int)$request->get('page', 1));
//     $perPage = 30;

//     if ($term === '') {
//         return response()->json(['results' => [], 'pagination' => ['more' => false]]);
//     }

//     $like   = '%'.$term.'%';
//     $prefix = $term.'%';

//     $query = \App\Models\Medicine::query()
//         ->where('status', 1)
//         ->select('id','name','generic','strength','manufacturer','price')
//         ->where(function ($w) use ($like) {
//             $w->where('name','like',$like)
//               ->orWhere('generic','like',$like)
//               ->orWhere('manufacturer','like',$like)
//               ->orWhere('strength','like',$like);
//         })
//         ->orderByRaw('
//             (CASE 
//                WHEN name LIKE ? THEN 1
//                WHEN generic LIKE ? THEN 2
//                WHEN manufacturer LIKE ? THEN 3
//                ELSE 4
//             END), name asc
//         ', [$prefix, $prefix, $prefix]);

//     $rows = $query->skip(($page - 1) * $perPage)->take($perPage)->get();

//     return response()->json([
//         'results' => $rows->map(fn($m) => [
//             'id'           => $m->id,
//             'text'         => trim($m->name . ' — ' . ($m->generic ?? '') . ($m->strength ? " ({$m->strength})" : '')),
//             'name'         => $m->name,
//             'generic'      => $m->generic,
//             'strength'     => $m->strength,
//             'manufacturer' => $m->manufacturer,
//             'price'        => $m->price,
//         ]),
//         'pagination' => ['more' => $rows->count() === $perPage],
//     ]);
// }

// public function ajaxSearch(\Illuminate\Http\Request $request)
// {
//     $term = trim((string)$request->get('term', ''));
//     $page = max(1, (int)$request->get('page', 1));
//     $perPage = 30;

//     if ($term === '') {
//         return response()->json(['results' => [], 'pagination' => ['more' => false]]);
//     }

//     // Lower-case once; MySQL LIKE is usually case-insensitive with utf8mb4_* collations,
//     // but we also add LOWER(...) to be safe across collations.
//     $needle = mb_strtolower($term);
//     $like   = '%'.$needle.'%';

//     $q = \App\Models\Medicine::query()
//         ->select('id','name','generic','strength','manufacturer','price')
//         // remove status filter while testing; add back if you’re sure you have status=1 rows
//         // ->where('status', 1)
//         ->where(function ($w) use ($like) {
//             $w->whereRaw('LOWER(name) LIKE ?', [$like])
//               ->orWhereRaw('LOWER(generic) LIKE ?', [$like])
//               ->orWhereRaw('LOWER(manufacturer) LIKE ?', [$like])
//               ->orWhereRaw('LOWER(strength) LIKE ?', [$like]);
//         })
//         ->orderBy('name');

//     $rows = $q->skip(($page - 1) * $perPage)->take($perPage)->get();

//     return response()->json([
//         'results' => $rows->map(fn($m) => [
//             'id'           => $m->id,
//             'text'         => trim($m->name . ' — ' . ($m->generic ?? '') . ($m->strength ? " ({$m->strength})" : '')),
//             'name'         => $m->name,
//             'generic'      => $m->generic,
//             'strength'     => $m->strength,
//             'manufacturer' => $m->manufacturer,
//             'price'        => $m->price,
//         ]),
//         'pagination' => ['more' => $rows->count() === $perPage],
//     ]);
// }
// public function ajaxSearch(Request $request)
// {
//     $term = trim((string) $request->get('term', ''));
//     $page = max(1, (int) $request->get('page', 1));
//     $perPage = 30;

//     if ($term === '') {
//         return response()->json([
//             'results'    => [],
//             'pagination' => ['more' => false],
//         ]);
//     }

//     // case-insensitive and robust across collations
//     $needle = mb_strtolower($term);
//     $like   = '%'.$needle.'%';

//     $query = Medicine::query()
//         ->select('id','name','generic','strength','manufacturer','price')
//         ->where(function ($w) use ($like) {
//             $w->whereRaw('LOWER(name) LIKE ?', [$like])
//               ->orWhereRaw('LOWER(generic) LIKE ?', [$like])
//               ->orWhereRaw('LOWER(manufacturer) LIKE ?', [$like])
//               ->orWhereRaw('LOWER(strength) LIKE ?', [$like]);
//         })
//         // add this back if you only want active rows:
//         ->where('status', 1)
//         ->orderBy('name');

//     $rows = $query->skip(($page - 1) * $perPage)->take($perPage)->get();

//     return response()->json([
//         'results' => $rows->map(fn ($m) => [
//             'id'           => $m->id,
//             'text'         => trim($m->name . ' — ' . ($m->generic ?? '') . ($m->strength ? " ({$m->strength})" : '')),
//             'name'         => $m->name,
//             'generic'      => $m->generic,
//             'strength'     => $m->strength,
//             'manufacturer' => $m->manufacturer,
//             'price'        => $m->price,
//         ]),
//         'pagination' => ['more' => $rows->count() === $perPage],
//     ]);
// }

// public function ajaxSearch(\Illuminate\Http\Request $request)
// {
//     $term = trim((string)$request->get('term', ''));
//     $page = max(1, (int)$request->get('page', 1));
//     $perPage = 30;

//     if ($term === '') {
//         return response()->json(['results' => [], 'pagination' => ['more' => false]]);
//     }

//     $needle = mb_strtolower($term);
//     $like   = '%'.$needle.'%';

//     $q = \App\Models\Medicine::query()
//         ->select('id','name','generic','strength','manufacturer','price')
//         ->where('status', 1)  // remove if you want all
//         ->where(function ($w) use ($like) {
//             $w->whereRaw('LOWER(name) LIKE ?', [$like])
//               ->orWhereRaw('LOWER(generic) LIKE ?', [$like])
//               ->orWhereRaw('LOWER(manufacturer) LIKE ?', [$like])
//               ->orWhereRaw('LOWER(strength) LIKE ?', [$like]);
//         })
//         ->orderBy('name');

//     $rows = $q->skip(($page - 1) * $perPage)->take($perPage)->get();

//     return response()->json([
//         'results' => $rows->map(fn($m) => [
//             'id'           => $m->id,
//             'text'         => trim($m->name . ' — ' . ($m->generic ?? '') . ($m->strength ? " ({$m->strength})" : '')),
//             'name'         => $m->name,
//             'generic'      => $m->generic,
//             'strength'     => $m->strength,
//             'manufacturer' => $m->manufacturer,
//             'price'        => $m->price,
//         ]),
//         'pagination' => ['more' => $rows->count() === $perPage],
//     ]);
// }
// app/Http/Controllers/MedicineController.php
// public function ajaxSearch(\Illuminate\Http\Request $request)
// {
//     $term = trim((string)$request->get('term', ''));
//     $page = max(1, (int)$request->get('page', 1));
//     $perPage = 30;

//     if ($term === '') {
//         return response()->json(['results' => [], 'pagination' => ['more' => false]]);
//     }

//     $needle = mb_strtolower($term);
//     $like   = '%'.$needle.'%';

//     $q = \App\Models\Medicine::query()
//         ->select('id','name','generic','strength','manufacturer','price')
//         ->where('status', 1) // remove if you want all rows
//         ->where(function ($w) use ($like) {
//             $w->whereRaw('LOWER(name) LIKE ?', [$like])
//               ->orWhereRaw('LOWER(generic) LIKE ?', [$like])
//               ->orWhereRaw('LOWER(manufacturer) LIKE ?', [$like])
//               ->orWhereRaw('LOWER(strength) LIKE ?', [$like]);
//         })
//         ->orderBy('name');

//     $rows = $q->skip(($page - 1) * $perPage)->take($perPage)->get();

//     return response()->json([
//         'results' => $rows->map(fn($m) => [
//             'id'           => $m->id,
//             'text'         => trim($m->name . ' — ' . ($m->generic ?? '') . ($m->strength ? " ({$m->strength})" : '')),
//             'name'         => $m->name,
//             'generic'      => $m->generic,
//             'strength'     => $m->strength,
//             'manufacturer' => $m->manufacturer,
//             'price'        => $m->price,
//         ]),
//         'pagination' => ['more' => $rows->count() === $perPage],
//     ]);
// }
// app/Http/Controllers/MedicineController.php


// public function ajaxSearch(Request $request)
// {
//     // Select2 v4 sends "term"; some integrations send "q". Accept both.
//     $term = trim((string)($request->get('term', $request->get('q', ''))));
//     $page = max(1, (int)$request->get('page', 1));
//     $perPage = 30;

//     // If nothing typed yet, return nothing (keeps dropdown clean)
//     if ($term === '') {
//         return response()->json(['results' => [], 'pagination' => ['more' => false]]);
//     }

//     // Case-insensitive LIKE across key columns
//     $needle = mb_strtolower($term);
//     $like   = '%'.$needle.'%';

//     $query = Medicine::query()
//         ->select('id','name','generic','strength','manufacturer','price')
//         // comment this out if you’re not sure rows are marked active
//         ->where('status', 1)
//         ->where(function($w) use ($like) {
//             $w->whereRaw('LOWER(name) LIKE ?', [$like])
//               ->orWhereRaw('LOWER(generic) LIKE ?', [$like])
//               ->orWhereRaw('LOWER(manufacturer) LIKE ?', [$like])
//               ->orWhereRaw('LOWER(strength) LIKE ?', [$like]);
//         })
//         ->orderBy('name');

//     $rows = $query->skip(($page-1)*$perPage)->take($perPage)->get();

//     // Fallback: if no DB match but the request wired correctly, return top items so you SEE something
//     if ($rows->isEmpty() && mb_strlen($term) >= 2) {
//         $rows = Medicine::select('id','name','generic','strength','manufacturer','price')
//             ->when(true, fn($q) => $q->where('status',1)) // keep same visibility
//             ->orderBy('name')
//             ->limit(10)
//             ->get();
//     }

//     return response()->json([
//         'results' => $rows->map(fn($m) => [
//             'id'           => $m->id,
//             'text'         => trim($m->name . ' — ' . ($m->generic ?? '') . ($m->strength ? " ({$m->strength})" : '')),
//             'name'         => $m->name,
//             'generic'      => $m->generic,
//             'strength'     => $m->strength,
//             'manufacturer' => $m->manufacturer,
//             'price'        => $m->price,
//         ]),
//         'pagination' => ['more' => $rows->count() === $perPage],
//     ]);
// }



public function search(Request $r)
    {
        $q    = trim($r->input('term', $r->input('q', '')));
        $page = max(1, (int) $r->input('page', 1));
        $per  = 20;

        $builder = Medicine::query();

        if ($q !== '') {
            $builder->where(function ($w) use ($q) {
                $w->where('name', 'like', "%{$q}%")
                  ->orWhere('generic', 'like', "%{$q}%")
                  ->orWhere('manufacturer', 'like', "%{$q}%")
                  ->orWhere('strength', 'like', "%{$q}%");
            });
        }

        $total   = $builder->count();
        $results = $builder->orderBy('name')
            ->skip(($page - 1) * $per)
            ->take($per)
            ->get(['id', 'name', 'generic', 'manufacturer', 'strength', 'price']);

        return response()->json([
            'results' => $results->map(fn ($m) => [
                'id'           => $m->id,
                'text'         => $m->name,
                'name'         => $m->name,
                'generic'      => $m->generic,
                'manufacturer' => $m->manufacturer,
                'strength'     => $m->strength,
                'price'        => $m->price,
            ]),
            'pagination' => [ 'more' => ($page * $per) < $total ],
        ]);
    }

}
