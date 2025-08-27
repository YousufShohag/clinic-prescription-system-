<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
        <label class="block">Name</label>
        <input type="text" name="name" value="{{ old('name', $customer->name) }}" class="w-full border rounded px-2 py-1">
    </div>
    <div>
        <label class="block">Phone 1</label>
        <input type="text" name="phone1" value="{{ old('phone1', $customer->phone1) }}" class="w-full border rounded px-2 py-1">
    </div>
    <div>
        <label class="block">Phone 2</label>
        <input type="text" name="phone2" value="{{ old('phone2', $customer->phone2) }}" class="w-full border rounded px-2 py-1">
    </div>
    <div>
        <label class="block">Email</label>
        <input type="email" name="email" value="{{ old('email', $customer->email) }}" class="w-full border rounded px-2 py-1">
    </div>
    <div class="md:col-span-2">
        <label class="block">Address</label>
        <input type="text" name="address" value="{{ old('address', $customer->address) }}" class="w-full border rounded px-2 py-1">
    </div>
    <div class="md:col-span-2">
        <label class="block">Notes</label>
        <textarea name="notes" class="w-full border rounded px-2 py-1">{{ old('notes', $customer->notes) }}</textarea>
    </div>
    <div>
        <label class="block">Status</label>
        <select name="status" class="w-full border rounded px-2 py-1">
            <option value="1" {{ old('status', $customer->status) == 1 ? 'selected' : '' }}>Active</option>
            <option value="0" {{ old('status', $customer->status) == 0 ? 'selected' : '' }}>Inactive</option>
        </select>
    </div>
</div>
