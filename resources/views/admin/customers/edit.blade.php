<x-app-layout>
    <div class="max-w-3xl mx-auto p-6 bg-white shadow rounded">
        <h2 class="text-xl font-semibold mb-4">Edit Customer</h2>
        <form method="POST" action="{{ route('customers.update', $customer->id) }}">
            @csrf @method('PUT')
            @include('admin.customers.form', ['customer' => $customer])
            <button type="submit" class="mt-4 px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">Update</button>
        </form>
    </div>
</x-app-layout>
