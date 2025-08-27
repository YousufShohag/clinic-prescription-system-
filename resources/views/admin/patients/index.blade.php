{{-- resources/views/patients/index.blade.php --}}
<x-app-layout>
    <div class="container mx-auto py-8">
        {{-- Header --}}
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold">Patients</h2>
            <a href="{{ route('patients.create') }}"
               class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">+ Add Patient</a>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto bg-white rounded shadow">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Doctor</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Notes</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Return Date</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prescriptions</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>

                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($patients as $patient)
                        @php $count = $patient->prescriptions_count ?? 0; @endphp
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-4 py-2">{{ $patient->name }}</td>
                            <td class="px-4 py-2">{{ $patient->doctor->name ?? '-' }}</td>
                            <td class="px-4 py-2">{{ $patient->phone ?? '-' }}</td>
                            <td class="px-4 py-2">{{ $patient->email ?? '-' }}</td>
                            <td class="px-4 py-2 max-w-[240px] truncate" title="{{ $patient->notes }}">{{ $patient->notes ?? '-' }}</td>
                            <td class="px-4 py-2 max-w-[240px] truncate" 
                                title="{{ $patient->next_return_date 
                                            ? \Carbon\Carbon::parse($patient->next_return_date)->format('d/m/Y') 
                                            : '' }}">
                                {{ $patient->next_return_date 
                                    ? \Carbon\Carbon::parse($patient->next_return_date)->format('d/m/Y') 
                                    : '-' }}
                            </td>

                            {{-- Clickable badge -> loads modal with prescriptions --}}
                            <td class="px-4 py-2">
                                <button
                                    type="button"
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                           {{ $count > 0 ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-700' }}
                                           hover:opacity-80 transition"
                                    data-url="{{ route('patients.prescriptions', $patient) }}"
                                    data-patient-name="{{ $patient->name }}"
                                    onclick="showPatientPrescriptions(this)"
                                    {{ $count === 0 ? 'disabled' : '' }}
                                    title="{{ $count === 0 ? 'No prescriptions' : 'View prescriptions' }}"
                                >
                                    {{ $count }}
                                </button>
                            </td>

                            <td class="px-4 py-2 flex flex-wrap gap-2">
                                <a href="{{ route('patients.edit', $patient->id) }}"
                                   class="bg-yellow-500 text-white px-2 py-1 rounded hover:bg-yellow-600 text-sm">Edit</a>
                                <form action="{{ route('patients.destroy', $patient->id) }}" method="POST"
                                      onsubmit="return confirm('Are you sure?');">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            class="bg-red-600 text-white px-2 py-1 rounded hover:bg-red-700 text-sm">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-4 py-10 text-center text-gray-500">No patients found.</td></tr>
                    @endforelse
                </tbody>
            </table>

            @if(method_exists($patients,'links'))
                <div class="p-3 border-t bg-gray-50">
                    {{ $patients->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- SweetAlert2 modal logic (SweetAlert2 is loaded in app.blade.php) --}}
    <script>
    async function showPatientPrescriptions(btn) {
        const url  = btn.getAttribute('data-url');        // exact JSON URL from Laravel route()
        const name = btn.getAttribute('data-patient-name');

        try {
            const res = await fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                credentials: 'same-origin' // keep session for auth-protected routes
            });

            if (!res.ok) {
                const body = await res.text();
                console.error('AJAX error', res.status, body);
                throw new Error(`HTTP ${res.status}`);
            }

            const payload = await res.json();
            const items = payload.data || [];

            if (!items.length) {
                return Swal.fire({
                    icon: 'info',
                    title: 'No prescriptions',
                    text: `No prescriptions found for ${name}.`,
                });
            }

            const rows = items.map(p => `
                <tr class="border-t">
                    <td class="py-2 pr-2">#${p.id}</td>
                    <td class="py-2 pr-2">${p.date ?? '-'}</td>
                    <td class="py-2 pr-2">${p.doctor ?? '—'}</td>
                    <td class="py-2 text-right">
                        <a href="${p.show_url}" class="text-blue-600 hover:underline" target="_blank" rel="noopener">View</a>
                    </td>
                </tr>
            `).join('');

            Swal.fire({
                title: `Prescriptions — ${name} (${items.length})`,
                html: `
                    <div class="text-left">
                        <div style="max-height:60vh;overflow:auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr>
                                        <th class="py-2 pr-2 text-gray-500 font-semibold text-xs uppercase">ID</th>
                                        <th class="py-2 pr-2 text-gray-500 font-semibold text-xs uppercase">Date</th>
                                        <th class="py-2 pr-2 text-gray-500 font-semibold text-xs uppercase">Doctor</th>
                                        <th class="py-2 text-gray-500 font-semibold text-xs uppercase text-right">Open</th>
                                    </tr>
                                </thead>
                                <tbody>${rows}</tbody>
                            </table>
                        </div>
                    </div>
                `,
                width: '48rem',
                showCloseButton: true,
                focusConfirm: false,
                confirmButtonText: 'Close',
            });
        } catch (e) {
            Swal.fire({ icon: 'error', title: 'Error', text: 'Could not load prescriptions.' });
        }
    }
    </script>
</x-app-layout>
