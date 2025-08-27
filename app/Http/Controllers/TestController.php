<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Test;
use App\Models\TestCategory;

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
            ->get();

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
    public function search(Request $request)
    {
        $query = $request->get('q', '');
        $tests = Test::where('name', 'LIKE', "%$query%")
            ->take(10)
            ->get();

        return response()->json($tests);
    }
}
