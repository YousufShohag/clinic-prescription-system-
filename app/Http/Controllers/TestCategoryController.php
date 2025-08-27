<?php

namespace App\Http\Controllers;

use App\Models\TestCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TestCategoryController extends Controller
{
    // Optional: protect with auth
    public function __construct()
    {
        // $this->middleware('auth'); // uncomment if needed
    }

    public function index()
    {
        $categories = TestCategory::latest()->paginate(10);
        return view('admin.tests.test-categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.tests.test-categories.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required','string','max:120','unique:test_categories,name'],
            'slug' => ['nullable','string','max:140','unique:test_categories,slug'],
            'description' => ['nullable','string','max:5000'],
            'is_active' => ['nullable','boolean'],
        ]);
        if (empty($data['slug'])) $data['slug'] = Str::slug($data['name']);
        $data['is_active'] = $request->boolean('is_active');

        TestCategory::create($data);
        return redirect()->route('test-categories.index')->with('status','Category created');
    }

    public function edit(TestCategory $test_category)
    {
        return view('admin.tests.test-categories.edit', ['category' => $test_category]);
    }

    public function update(Request $request, TestCategory $test_category)
    {
        $data = $request->validate([
            'name' => ['required','string','max:120','unique:test_categories,name,'.$test_category->id],
            'slug' => ['nullable','string','max:140','unique:test_categories,slug,'.$test_category->id],
            'description' => ['nullable','string','max:5000'],
            'is_active' => ['nullable','boolean'],
        ]);
        if (empty($data['slug'])) $data['slug'] = Str::slug($data['name']);
        $data['is_active'] = $request->boolean('is_active');

        $test_category->update($data);
        return redirect()->route('test-categories.index')->with('status','Category updated');
    }

    public function destroy(TestCategory $test_category)
    {
        $test_category->delete();
        return redirect()->route('test-categories.index')->with('status','Category deleted');
    }

    // If you want show page, add a view and keep this method:
    public function show(TestCategory $test_category)
    {
        return view('test-categories.show', ['category' => $test_category]);
    }
}
