<div class="mb-3">
    <label>Name</label>
    <input type="text" name="name" class="form-control" value="{{ old('name', $doctor->name ?? '') }}" required>
</div>

<div class="mb-3">
    <label>Specialization</label>
    <input type="text" name="specialization" class="form-control" value="{{ old('specialization', $doctor->specialization ?? '') }}" required>
</div>

<div class="mb-3">
    <label>Degree</label>
    <input type="text" name="degree" class="form-control" value="{{ old('degree', $doctor->degree ?? '') }}" required>
</div>

<div class="mb-3">
    <label>BMA Registration Number</label>
    <input type="text" name="bma_registration_number" class="form-control" value="{{ old('bma_registration_number', $doctor->bma_registration_number ?? '') }}" required>
</div>

<div class="mb-3">
    <label>Chamber</label>
    <input type="text" name="chamber" class="form-control" value="{{ old('chamber', $doctor->chamber ?? '') }}" required>
</div>

<div class="mb-3">
    <label>Email</label>
    <input type="email" name="email" class="form-control" value="{{ old('email', $doctor->email ?? '') }}" required>
</div>

<div class="mb-3">
    <label>Phone</label>
    <input type="text" name="phone" class="form-control" value="{{ old('phone', $doctor->phone ?? '') }}" required>
</div>

<div class="mb-3">
    <label>Consultation Fee</label>
    <input type="number" name="consultation_fee" step="0.01" class="form-control" value="{{ old('consultation_fee', $doctor->consultation_fee ?? '') }}" required>
</div>

<div class="mb-3">
    <label>Available Time (optional)</label>
    <input type="text" name="available_time" class="form-control" value="{{ old('available_time', $doctor->available_time ?? '') }}">
</div>

<div class="mb-3">
    <label>Notes (optional)</label>
    <textarea name="notes" class="form-control">{{ old('notes', $doctor->notes ?? '') }}</textarea>
</div>
 <div class="mb-3">
        <label>Doctor Image (optional)</label>
        <input type="file" name="image" class="form-control">
        @if(isset($doctor) && $doctor->image)
            <img src="{{ asset($doctor->image) }}" alt="Doctor Image" width="80" class="mt-2">
        @endif
</div>
