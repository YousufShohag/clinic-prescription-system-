<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Test;
use App\Models\TestCategory;

// This is for import Test
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\TestsImport;

class TestController extends Controller
{
    /**
     * Display a list of tests (only active).
     */
    public function index()
    {
        // Load with category relation
        $tests = Test::with('category')
            ->where('status', 'active')
            ->orderBy('name')               // optional: stable ordering
            ->paginate(25)                  // ← show 25 per page
            ->withQueryString();            // keep current query params on links
         

        return view('admin.tests.index', compact('tests'));




    }

    /**
     * Show the create test form.
     */
    public function create()
    {
        $categories = TestCategory::where('is_active', true)->orderBy('name')->get();
        return view('admin.tests.create', compact('categories'));
    }

    /**
     * Store a new test.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'             => 'required|string|max:255',
            'price'            => 'required|numeric|min:0',
            'note'             => 'nullable|string',
            'status'           => 'required|in:active,inactive',
            'test_category_id' => 'required|exists:test_categories,id',
        ]);

        Test::create($data);

        return redirect()->route('tests.index')->with('success', 'Test created successfully');
    }

    /**
     * Show a single test.
     */
    public function show(Test $test)
    {
        return view('tests.show', compact('test'));
    }

    /**
     * Quick search API.
     */
    // public function search(Request $request)
    // {
    //     $query = $request->get('q', '');
    //     $tests = Test::where('name', 'LIKE', "%$query%")
    //         ->take(10)
    //         ->get();

    //     return response()->json($tests);
    // }

    public function search(Request $r)
    {
        $q    = trim($r->input('term', $r->input('q', '')));
        $page = max(1, (int) $r->input('page', 1));
        $per  = 20;

        $builder = Test::query();
        if ($q !== '') {
            $builder->where(function ($w) use ($q) {
                $w->where('name', 'like', "%{$q}%")
                  ->orWhere('note', 'like', "%{$q}%");
            });
        }

        $total   = $builder->count();
        $results = $builder->orderBy('name')
            ->skip(($page - 1) * $per)
            ->take($per)
            ->get(['id', 'name', 'price', 'note']);

        return response()->json([
            'results' => $results->map(fn ($t) => [
                'id'    => $t->id,
                'text'  => $t->name,
                'name'  => $t->name,
                'price' => $t->price,
                'note'  => $t->note,
            ]),
            'pagination' => [ 'more' => ($page * $per) < $total ],
        ]);
    }

    public function import(Request $request)
        {
            $request->validate([
                'file' => 'required|file|mimes:xlsx,csv,xls',
            ]);

            Excel::import(new TestsImport, $request->file('file'));

            return redirect()->route('tests.index')->with('success', 'Tests imported successfully!');
        }

}
