<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
             Dashboard
        </h2>
    </x-slot>

    <div class="py-10 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-10">

        <!-- 🔸 KPI Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white p-6 rounded-lg shadow text-center">
                <p class="text-sm text-gray-500">Total Patient</p>
                <h3 class="text-2xl font-bold text-indigo-600">{{ $totalPatient }}</h3>
            </div>
            <div class="bg-white p-6 rounded-lg shadow text-center">
                <p class="text-sm text-gray-500">Total Medicines</p>
                <h3 class="text-2xl font-bold text-green-600">{{ $totalMedicines }}</h3>
            </div>
            <div class="bg-white p-6 rounded-lg shadow text-center">
                <p class="text-sm text-gray-500">Total Prescription</p>
                <h3 class="text-2xl font-bold text-blue-600">{{ $totalPrescription }}</h3>
            </div>
            <div class="bg-white p-6 rounded-lg shadow text-center">
                <p class="text-sm text-gray-500">Total Invoices</p>
                <h3 class="text-2xl font-bold text-purple-600">{{ $totalInvoices }}</h3>
            </div>
        </div>

        <!-- 🔸 Sales Summary -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
            <div class="bg-white p-6 rounded-lg shadow text-center">
                <p class="text-sm text-gray-500">Today's Sales</p>
                <h3 class="text-xl font-bold text-green-600">${{ number_format($todaysSales, 2) }}</h3>
            </div>
            <div class="bg-white p-6 rounded-lg shadow text-center">
                <p class="text-sm text-gray-500">This Month</p>
                <h3 class="text-xl font-bold text-blue-600">${{ number_format($thisMonthSales, 2) }}</h3>
            </div>
            <div class="bg-white p-6 rounded-lg shadow text-center">
                <p class="text-sm text-gray-500">Last Month</p>
                <h3 class="text-xl font-bold text-purple-600">${{ number_format($lastMonthSales, 2) }}</h3>
            </div>
        </div>

        <!-- 🔸 Charts -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="font-semibold mb-3">📈 Sales Trend</h3>
                <canvas id="salesChart"></canvas>
            </div>
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="font-semibold mb-3">💊 Top Selling Medicines</h3>
                <canvas id="topMedicinesChart"></canvas>
            </div>
        </div>

        <!-- 🔸 Alerts -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Low Stock -->
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="font-semibold text-red-600 mb-3">⚠️ Low Stock Medicines</h3>
                <table class="w-full text-sm border">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="p-2 border">Medicine</th>
                            <th class="p-2 border">Stock</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($lowStockMedicines as $med)
                            <tr>
                                <td class="p-2 border">{{ $med->name }}</td>
                                <td class="p-2 border text-red-600 font-bold">{{ $med->stock }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="2" class="p-2 text-gray-500 text-center">✅ All in stock</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Expired -->
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="font-semibold text-red-700 mb-3">❌ Expired Medicines</h3>
                <table class="w-full text-sm border">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="p-2 border">Medicine</th>
                            <th class="p-2 border">Expiry Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($expiredMedicines as $med)
                            <tr>
                                <td class="p-2 border">{{ $med->name }}</td>
                                <td class="p-2 border text-red-600 font-bold">{{ \Carbon\Carbon::parse($med->expiry_date)->format('m/d/Y') }}</td>
                               
                            </tr>
                        @empty
                            <tr><td colspan="2" class="p-2 text-gray-500 text-center">✅ No expired medicines</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const salesCtx = document.getElementById('salesChart').getContext('2d');
        new Chart(salesCtx, {
            type: 'line',
            data: {
                labels: @json($salesDates), // e.g. ["Mon","Tue","Wed"]
                datasets: [{
                    label: 'Sales ($)',
                    data: @json($salesAmounts),
                    borderColor: '#4F46E5',
                    backgroundColor: 'rgba(99,102,241,0.2)',
                    fill: true
                }]
            }
        });

        const medCtx = document.getElementById('topMedicinesChart').getContext('2d');
        new Chart(medCtx, {
            type: 'pie',
            data: {
                labels: @json($topMedicines->pluck('name')),
                datasets: [{
                    data: @json($topMedicines->pluck('total_qty')),
                    backgroundColor: ['#EF4444','#F59E0B','#10B981','#3B82F6','#8B5CF6']
                }]
            }
        });
    </script>
</x-app-layout>
