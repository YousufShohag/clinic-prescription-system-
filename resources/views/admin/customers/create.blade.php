<x-app-layout>
    <div class="max-w-3xl mx-auto p-6 bg-white shadow rounded">
        <h2 class="text-xl font-semibold mb-4">Add Customer</h2>
        <form method="POST" action="{{ route('customers.store') }}">
            @csrf
            @include('admin.customers.form', ['customer' => new \App\Models\Customer()])
            <button type="submit" class="mt-4 px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">Save</button>
        </form>
    </div>
</x-app-layout>
