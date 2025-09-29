{{-- Smart Navigation (no flicker) --}}
<style>[x-cloak]{display:none!important}</style>

<nav x-data="{ open:false }"
     class="sticky top-0 z-50 bg-white/90 backdrop-blur border-b border-gray-200">
  <!-- <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8"> -->
  <div class="mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex h-16 items-center justify-between">

      {{-- LEFT: Logo + primary --}}
      <div class="flex items-center gap-6">
        {{-- Logo --}}
      <a href="{{ route('dashboard') }}" class="flex items-center shrink-0">
        <img src="{{ asset('logo.png') }}" alt="{{ config('app.name', 'Logo') }}"
            class="block h-12 sm:h-14 md:h-16 lg:h-20 w-auto">
      </a>

        {{-- Desktop primaries --}}
        <div class="hidden md:flex items-center gap-2">
          <x-nav-link :href="route('prescriptions.index')" :active="request()->routeIs('prescriptions.*')">
            Prescriptions
          </x-nav-link>

          <x-nav-link :href="route('patients.index')" :active="request()->routeIs('patients.*')">
            Patients
          </x-nav-link>

          <x-nav-link :href="route('appointments.index')" :active="request()->routeIs('appointments.*')">
            Appointments
          </x-nav-link>

          {{-- Calendar (prefer calendars.index, fallback to appointments#index) --}}
          @if (Route::has('calendars.index'))
            <x-nav-link :href="route('calendars.index')" :active="request()->routeIs('calendars.*')">
              Calendar
            </x-nav-link>
          @else
            <x-nav-link :href="route('appointments.index') . '#calendar'" :active="request()->routeIs('appointments.*')">
              Calendar
            </x-nav-link>
          @endif
        </div>

        {{-- Desktop grouped menus --}}
        <div class="hidden md:flex items-center gap-2">

          {{-- Clinical --}}
          <div x-data="{ m:false }" class="relative">
            <button type="button"
                    @click="m=!m" @click.outside="m=false" @keydown.escape="m=false"
                    :aria-expanded="m"
                    class="inline-flex items-center px-2 py-2 text-sm text-gray-600 hover:text-gray-900">
              Clinical
              <svg class="ml-1 h-4 w-4 transition-transform"
                   :class="m?'rotate-180':''" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.25a.75.75 0 01-1.06 0L5.21 8.27a.75.75 0 01.02-1.06z" clip-rule="evenodd"/>
              </svg>
            </button>
            <div x-cloak x-show="m" x-transition
                 class="absolute mt-2 w-52 rounded-md bg-white shadow ring-1 ring-black/5">
              <div class="py-1">
                <a href="{{ route('prescriptions.index') }}" class="block px-4 py-2 text-sm hover:bg-gray-50">Prescriptions</a>
                <a href="{{ route('patients.index') }}" class="block px-4 py-2 text-sm hover:bg-gray-50">Patients</a>
                <a href="{{ route('appointments.index') }}" class="block px-4 py-2 text-sm hover:bg-gray-50">Appointments</a>
                @if (Route::has('calendars.index'))
                  <a href="{{ route('calendars.index') }}" class="block px-4 py-2 text-sm hover:bg-gray-50">Calendar</a>
                @else
                  <a href="{{ route('appointments.index') }}#calendar" class="block px-4 py-2 text-sm hover:bg-gray-50">Calendar</a>
                @endif
              </div>
            </div>
          </div>

          {{-- Pharmacy --}}
          <div x-data="{ m:false }" class="relative">
            <button type="button"
                    @click="m=!m" @click.outside="m=false" @keydown.escape="m=false"
                    :aria-expanded="m"
                    class="inline-flex items-center px-2 py-2 text-sm text-gray-600 hover:text-gray-900">
              Pharmacy
              <svg class="ml-1 h-4 w-4 transition-transform"
                   :class="m?'rotate-180':''" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.25a.75.75 0 01-1.06 0L5.21 8.27a.75.75 0 01.02-1.06z" clip-rule="evenodd"/>
              </svg>
            </button>
            <div x-cloak x-show="m" x-transition
                 class="absolute mt-2 w-56 rounded-md bg-white shadow ring-1 ring-black/5">
              <div class="py-1">
                <a href="{{ route('customers.index') }}" class="block px-4 py-2 text-sm hover:bg-gray-50">Customers</a>
                <a href="{{ route('invoices.index') }}" class="block px-4 py-2 text-sm hover:bg-gray-50">Invoices</a>
                <a href="{{ route('invoices.due') }}" class="block px-4 py-2 text-sm hover:bg-gray-50">Due Invoices</a>
              </div>
            </div>
          </div>

          {{-- Reports --}}
          <div x-data="{ m:false }" class="relative">
            <button type="button"
                    @click="m=!m" @click.outside="m=false" @keydown.escape="m=false"
                    :aria-expanded="m"
                    class="inline-flex items-center px-2 py-2 text-sm text-gray-600 hover:text-gray-900">
              Reports
              <svg class="ml-1 h-4 w-4 transition-transform"
                   :class="m?'rotate-180':''" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.25a.75.75 0 01-1.06 0L5.21 8.27a.75.75 0 01.02-1.06z" clip-rule="evenodd"/>
              </svg>
            </button>
            <div x-cloak x-show="m" x-transition
                 class="absolute mt-2 w-56 rounded-md bg-white shadow ring-1 ring-black/5">
              <div class="py-1">
                <a href="{{ route('reports.doctorPatients') }}" class="block px-4 py-2 text-sm hover:bg-gray-50">Doctor → Patients</a>
              </div>
            </div>
          </div>

          {{-- Setup --}}
          <div x-data="{ m:false }" class="relative">
            <button type="button"
                    @click="m=!m" @click.outside="m=false" @keydown.escape="m=false"
                    :aria-expanded="m"
                    class="inline-flex items-center px-2 py-2 text-sm text-gray-600 hover:text-gray-900">
              Setup
              <svg class="ml-1 h-4 w-4 transition-transform"
                   :class="m?'rotate-180':''" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.25a.75.75 0 01-1.06 0L5.21 8.27a.75.75 0 01.02-1.06z" clip-rule="evenodd"/>
              </svg>
            </button>
            <div x-cloak x-show="m" x-transition
                 class="absolute mt-2 w-64 rounded-md bg-white shadow ring-1 ring-black/5">
              <div class="py-1">
                <a href="{{ route('medicines.index') }}" class="block px-4 py-2 text-sm hover:bg-gray-50">Medicines</a>
                <a href="{{ route('categories.index') }}" class="block px-4 py-2 text-sm hover:bg-gray-50">Medicine Categories</a>
                <a href="{{ route('tests.index') }}" class="block px-4 py-2 text-sm hover:bg-gray-50">Tests</a>
                <a href="{{ route('test-categories.index') }}" class="block px-4 py-2 text-sm hover:bg-gray-50">Test Categories</a>
                <a href="{{ route('doctors.index') }}" class="block px-4 py-2 text-sm hover:bg-gray-50">Doctors</a>
              </div>
            </div>
          </div>
        </div>
      </div>

      {{-- RIGHT: search + quick add + user --}}
      <div class="hidden md:flex items-center gap-3">
        {{-- Global search to Patients --}}
        <form method="GET" action="{{ route('patients.index') }}" class="relative">
          <input name="q" type="text" placeholder="Search patient / phone…"
                 class="w-56 rounded-md border-gray-300 pl-3 pr-8 py-1.5 text-sm focus:ring-indigo-500 focus:border-indigo-500">
          <button class="absolute right-1 top-1.5 text-gray-500" aria-label="Search">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <circle cx="11" cy="11" r="7" stroke-width="2"></circle>
              <path d="M20 20l-3.5-3.5" stroke-width="2"></path>
            </svg>
          </button>
        </form>

        {{-- Quick Add --}}
        <div x-data="{ m:false }" class="relative">
          <button type="button"
                  @click="m=!m" @click.outside="m=false" @keydown.escape="m=false"
                  class="inline-flex items-center rounded-md bg-indigo-600 text-white px-3 py-1.5 text-sm hover:bg-indigo-700">
            + New
          </button>
          <div x-cloak x-show="m" x-transition
               class="absolute right-0 mt-2 w-56 rounded-md bg-white shadow ring-1 ring-black/5">
            <div class="py-1">
              <a href="{{ route('prescriptions.create') }}" class="block px-4 py-2 text-sm hover:bg-gray-50">Prescription</a>
              <a href="{{ route('appointments.create') }}" class="block px-4 py-2 text-sm hover:bg-gray-50">Appointment</a>
              <a href="{{ route('patients.create') }}" class="block px-4 py-2 text-sm hover:bg-gray-50">Patient</a>
              <a href="{{ route('invoices.create') }}" class="block px-4 py-2 text-sm hover:bg-gray-50">Invoice</a>
            </div>
          </div>
        </div>

        {{-- User (Jetstream dropdown) --}}
        <x-dropdown align="right" width="48">
          <x-slot name="trigger">
            <button class="inline-flex items-center px-3 py-2 text-sm rounded-md text-gray-600 hover:text-gray-900">
              <div>{{ Auth::user()->name }}</div>
              <svg class="ml-1 h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.25a.75.75 0 01-1.06 0L5.21 8.27a.75.75 0 01.02-1.06z" clip-rule="evenodd"/>
              </svg>
            </button>
          </x-slot>
          <x-slot name="content">
            <x-dropdown-link :href="route('profile.edit')">Profile</x-dropdown-link>
            <form method="POST" action="{{ route('logout') }}">
              @csrf
              <x-dropdown-link :href="route('logout')"
                onclick="event.preventDefault(); this.closest('form').submit();">
                Log Out
              </x-dropdown-link>
            </form>
          </x-slot>
        </x-dropdown>
      </div>

      {{-- Mobile burger --}}
      <div class="md:hidden">
        <button @click="open=!open"
                class="inline-flex items-center p-2 rounded-md text-gray-500 hover:bg-gray-100"
                :aria-expanded="open" aria-controls="mobileMenu">
          <svg class="h-6 w-6" fill="none" stroke="currentColor">
            <path :class="{'hidden': open, 'inline-flex': !open}" class="inline-flex"
                  stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M4 6h16M4 12h16M4 18h16"/>
            <path :class="{'hidden': !open, 'inline-flex': open}" class="hidden"
                  stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>
      </div>
    </div>
  </div>

  {{-- Mobile menu --}}
  <div id="mobileMenu" x-cloak x-show="open" x-transition
       class="md:hidden border-t border-gray-200 bg-white">
    <div class="px-4 pt-3 pb-4 space-y-1">
      <x-responsive-nav-link :href="route('prescriptions.index')" :active="request()->routeIs('prescriptions.*')">
        Prescriptions
      </x-responsive-nav-link>
      <x-responsive-nav-link :href="route('patients.index')" :active="request()->routeIs('patients.*')">
        Patients
      </x-responsive-nav-link>
      <x-responsive-nav-link :href="route('appointments.index')" :active="request()->routeIs('appointments.*')">
        Appointments
      </x-responsive-nav-link>
      @if (Route::has('calendars.index'))
        <x-responsive-nav-link :href="route('calendars.index')" :active="request()->routeIs('calendars.*')">
          Calendar
        </x-responsive-nav-link>
      @else
        <x-responsive-nav-link :href="route('appointments.index') . '#calendar'" :active="request()->routeIs('appointments.*')">
          Calendar
        </x-responsive-nav-link>
      @endif

      {{-- Collapsible groups --}}
      <div x-data="{ m:false }" class="pt-2">
        <button @click="m=!m" class="w-full text-left px-2 py-2 text-sm text-gray-600 hover:text-gray-900">Pharmacy</button>
        <div x-cloak x-show="m" class="pl-4 space-y-1">
          <a href="{{ route('customers.index') }}" class="block px-2 py-1 text-sm text-gray-600 hover:text-gray-900">Customers</a>
          <a href="{{ route('invoices.index') }}" class="block px-2 py-1 text-sm text-gray-600 hover:text-gray-900">Invoices</a>
          <a href="{{ route('invoices.due') }}" class="block px-2 py-1 text-sm text-gray-600 hover:text-gray-900">Due Invoices</a>
        </div>
      </div>

      <div x-data="{ m:false }">
        <button @click="m=!m" class="w-full text-left px-2 py-2 text-sm text-gray-600 hover:text-gray-900">Reports</button>
        <div x-cloak x-show="m" class="pl-4 space-y-1">
          <a href="{{ route('reports.doctorPatients') }}" class="block px-2 py-1 text-sm text-gray-600 hover:text-gray-900">Doctor → Patients</a>
        </div>
      </div>

      <div x-data="{ m:false }">
        <button @click="m=!m" class="w-full text-left px-2 py-2 text-sm text-gray-600 hover:text-gray-900">Setup</button>
        <div x-cloak x-show="m" class="pl-4 space-y-1">
          <a href="{{ route('medicines.index') }}" class="block px-2 py-1 text-sm text-gray-600 hover:text-gray-900">Medicines</a>
          <a href="{{ route('categories.index') }}" class="block px-2 py-1 text-sm text-gray-600 hover:text-gray-900">Medicine Categories</a>
          <a href="{{ route('tests.index') }}" class="block px-2 py-1 text-sm text-gray-600 hover:text-gray-900">Tests</a>
          <a href="{{ route('test-categories.index') }}" class="block px-2 py-1 text-sm text-gray-600 hover:text-gray-900">Test Categories</a>
          <a href="{{ route('doctors.index') }}" class="block px-2 py-1 text-sm text-gray-600 hover:text-gray-900">Doctors</a>
        </div>
      </div>

      {{-- Profile (mobile) --}}
      <div class="border-t border-gray-200 pt-3">
        <div class="px-2 text-sm">
          <div class="font-medium text-gray-800">{{ Auth::user()->name }}</div>
          <div class="text-gray-500">{{ Auth::user()->email }}</div>
        </div>
        <div class="mt-2 space-y-1">
          <x-responsive-nav-link :href="route('profile.edit')">Profile</x-responsive-nav-link>
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <x-responsive-nav-link :href="route('logout')"
              onclick="event.preventDefault(); this.closest('form').submit();">
              Log Out
            </x-responsive-nav-link>
          </form>
        </div>
      </div>
    </div>
  </div>
</nav>
