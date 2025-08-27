<x-app-layout>
<div class="container">
    <h2>Edit Doctor</h2>
    <form action="{{ route('doctors.update', $doctor->id)}}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('doctors.form', ['doctor' => $doctor])
        <button type="submit" class="btn btn-primary">Update</button>
    </form>
</div>
<x-app-layout>
