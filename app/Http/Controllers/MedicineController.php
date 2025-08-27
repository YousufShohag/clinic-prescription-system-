<?php 

namespace App\Http\Controllers;

use App\Models\Medicine;
use App\Models\Category;
use Illuminate\Http\Request;

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

public function search(Request $request)
    {
        $query = $request->get('q', '');
        $medicines = Medicine::where('name', 'LIKE', "%$query%")
            ->take(10)
            ->get();

        return response()->json($medicines);
    }


}
