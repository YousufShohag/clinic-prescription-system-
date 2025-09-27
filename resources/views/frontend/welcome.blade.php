<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dr. Shabuddin Hossain Pavel — Portfolio</title>
  <meta name="description" content="Dr. Shabuddin Hossain Pavel is a board-certified physician specializing in internal medicine. Explore services, experience, testimonials, and book an appointment." />
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    // Tailwind config (optional brand colors)
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: { sans: ["Inter", "ui-sans-serif", "system-ui"] },
          colors: {
            brand: {
              50: '#eff6ff',
              100: '#dbeafe',
              200: '#bfdbfe',
              300: '#93c5fd',
              400: '#60a5fa',
              500: '#3b82f6',
              600: '#2563eb',
              700: '#1d4ed8',
              800: '#1e40af',
              900: '#1e3a8a'
            }
          },
          boxShadow: {
            soft: '0 10px 25px -10px rgba(0,0,0,0.15)'
          }
        }
      }
    }
  </script>
  <style>
    /* Fallback smooth-scroll */
    html { scroll-behavior: smooth; }
  </style>
</head>
<body class="bg-white text-slate-800 antialiased">
  <!-- Top Bar -->
  <div class="hidden md:flex items-center justify-between px-4 md:px-8 py-2 text-sm bg-slate-50 border-b border-slate-200">
    <p class="flex items-center gap-2"><span aria-hidden="true">📍</span> 12 Lakeview Rd, Dhaka</p>
    <a href="tel:+8801000000000" class="flex items-center gap-2 hover:text-brand-600 transition"><span aria-hidden="true">📞</span> +880 1729-548153</a>
  </div>

  <!-- Header / Nav -->
  <header class="sticky top-0 z-50 bg-white/80 backdrop-blur supports-[backdrop-filter]:bg-white/60 border-b border-slate-200">
    <div class="max-w-7xl mx-auto px-4 md:px-8 py-3 flex items-center justify-between">
      <a href="#home" class="flex items-center gap-3">
        <div class="h-10 w-10 rounded-xl bg-brand-600 text-white grid place-content-center font-semibold">SHP</div>
        <div>
          <h1 class="text-lg font-semibold leading-tight">Dr. Shabuddin Hossain Pavel</h1>
          <p class="text-xs text-slate-500">MBBS, (FMC, DU) — Internal Medicine</p>
        </div>
      </a>
      <nav class="hidden md:flex gap-6 text-sm">
        <a class="hover:text-brand-600" href="#home">Home</a>
        <a class="hover:text-brand-600" href="#about">About</a>
        <a class="hover:text-brand-600" href="#services">Services</a>
        <a class="hover:text-brand-600" href="#experience">Experience</a>
        <a class="hover:text-brand-600" href="#testimonials">Testimonials</a>
       <a class="hover:text-brand-600" href="{{ url('/blog') }}">Blog</a>
        <a class="hover:text-brand-600" href="#contact">Contact</a>
      </nav>
      <a href="#contact" class="hidden md:inline-flex items-center gap-2 bg-brand-600 hover:bg-brand-700 text-white text-sm font-medium px-4 py-2 rounded-xl shadow-soft transition">
        Book Appointment
      </a>
      <button id="menuBtn" class="md:hidden inline-flex items-center justify-center h-10 w-10 rounded-lg border border-slate-300">
        <span class="sr-only">Open menu</span>☰
      </button>
    </div>
    <div id="mobileMenu" class="md:hidden hidden border-t border-slate-200">
      <nav class="px-4 py-3 grid gap-2 text-sm">
        <a class="py-2" href="#home">Home</a>
        <a class="py-2" href="#about">About</a>
        <a class="py-2" href="#services">Services</a>
        <a class="py-2" href="#experience">Experience</a>
        <a class="py-2" href="#testimonials">Testimonials</a>
        <a class="py-2" href="#contact">Contact</a>
      </nav>
    </div>
  </header>

   {{-- Hero --}}
  <section id="home" class="relative overflow-hidden">
    <div aria-hidden="true" class="pointer-events-none absolute inset-0 bg-gradient-to-br from-brand-50 via-white to-slate-50"></div>
    <div class="max-w-7xl mx-auto px-4 md:px-8 py-16 md:py-24 grid md:grid-cols-2 gap-10 items-center relative">
      <div>
        <span class="inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-brand-700 bg-brand-50 border border-brand-100 px-3 py-1 rounded-full">Trusted Care • 4.9★ Rating</span>
        <h2 class="text-4xl md:text-5xl font-extrabold leading-tight mt-4">
          Feel heard. Get a clear plan. <span class="text-brand-700">Start feeling better</span> today.
        </h2>
        <p class="mt-5 text-slate-600 max-w-prose">I’m Dr. Shabuddin Hossain Pavel, a board-certified internist helping adults manage diabetes, blood pressure, thyroid issues and more—with gentle, step-by-step care tailored to you.</p>
        <div class="mt-8 flex flex-col sm:flex-row gap-3">
          <a href="#contact" class="inline-flex justify-center items-center px-6 py-3 rounded-2xl bg-brand-600 hover:bg-brand-700 text-white font-semibold shadow-soft focus:outline-none focus:ring-4 focus:ring-brand-200">Book an appointment</a>
          <a href="#services" class="inline-flex justify-center items-center px-6 py-3 rounded-2xl border border-slate-300 hover:bg-slate-50 font-semibold">See services & fees</a>
        </div>
        <div class="mt-8 grid grid-cols-2 sm:grid-cols-4 gap-3 text-center">
          <div class="p-4 rounded-xl bg-white border border-slate-200"><p class="text-2xl">⏱️</p><p class="text-xs text-slate-500">Average wait <strong>10 min</strong></p></div>
          <div class="p-4 rounded-xl bg-white border border-slate-200"><p class="text-2xl">🧑‍⚕️</p><p class="text-xs text-slate-500">Female-friendly clinic</p></div>
          <div class="p-4 rounded-xl bg-white border border-slate-200"><p class="text-2xl">🌐</p><p class="text-xs text-slate-500">Bangla • English</p></div>
          <div class="p-4 rounded-xl bg-white border border-slate-200"><p class="text-2xl">🔒</p><p class="text-xs text-slate-500">Private & confidential</p></div>
        </div>
      </div>
      <div class="relative">
        <div class="relative mx-auto max-w-sm">
          <img src="{{ asset('doctor2.jpg') }}" alt="Dr. Yousuf Patwari" style="width:100%;max-width:350px;border-radius:20px;box-shadow:0 8px 20px rgba(0,0,0,0.1);" />
          <div class="absolute -bottom-6 -left-6 hidden md:block p-4 rounded-2xl bg-white border border-slate-200 shadow-soft">
            <p class="text-sm font-semibold">Clinic Hours</p>
            <p class="text-xs text-slate-600 mt-1">Sun–Thu: 10:00–18:00</p>
            <p class="text-xs text-slate-600">Fri–Sat: Closed</p>
            </div>
          <div class="absolute -top-4 -right-4 hidden md:flex items-center gap-2 px-4 py-2 rounded-2xl bg-brand-600 text-white shadow-soft">
            <span>⭐️⭐️⭐️⭐️⭐️</span><span class="text-sm font-medium">200+ reviews</span>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- About -->
  <section id="about" class="py-16 md:py-24 bg-slate-50 border-y border-slate-200">
    <div class="max-w-5xl mx-auto px-4 md:px-8 grid md:grid-cols-3 gap-10 items-start">
      <div class="md:col-span-1">
        <h3 class="text-2xl font-bold">About</h3>
        <p class="mt-2 text-slate-600">Credentials & approach</p>
      </div>
      <div class="md:col-span-2 space-y-6">
        <p>I am Dr. Shabuddin Hossain Pavel,  committed to providing comprehensive and compassionate care to adults. I completed my MBBS at Faridpur Medical College under Dhaka University. My professional practice focuses on:</p>
        <ul class="grid sm:grid-cols-2 gap-3 text-sm">
          <li class="flex items-start gap-3"><span aria-hidden="true">✅</span> Board‑certified in Internal Medicine</li>
          <li class="flex items-start gap-3"><span aria-hidden="true">✅</span> Member, Bangladesh Medical Association</li>
          <li class="flex items-start gap-3"><span aria-hidden="true">✅</span> Evidence‑based, patient‑first care</li>
          <li class="flex items-start gap-3"><span aria-hidden="true">✅</span> Multilingual: English, Bangla</li>
        </ul>
      </div>
    </div>
  </section>

  <!-- Services -->
  <section id="services" class="py-16 md:py-24">
    <div class="max-w-7xl mx-auto px-4 md:px-8">
      <div class="max-w-2xl">
        <h3 class="text-2xl font-bold">Services</h3>
        <p class="mt-2 text-slate-600">Comprehensive internal medicine & preventive care</p>
      </div>
      <div class="mt-10 grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Card -->
        <div class="p-6 rounded-2xl border border-slate-200 hover:shadow-soft transition">
          <div class="text-3xl" aria-hidden="true">🩺</div>
          <h4 class="mt-3 font-semibold">General Consultation</h4>
          <p class="mt-1 text-sm text-slate-600">Diagnosis & treatment for acute and chronic conditions.</p>
        </div>
        <div class="p-6 rounded-2xl border border-slate-200 hover:shadow-soft transition">
          <div class="text-3xl" aria-hidden="true">🧪</div>
          <h4 class="mt-3 font-semibold">Health Check‑ups</h4>
          <p class="mt-1 text-sm text-slate-600">Annual exams, labs, and lifestyle counseling.</p>
        </div>
        <div class="p-6 rounded-2xl border border-slate-200 hover:shadow-soft transition">
          <div class="text-3xl" aria-hidden="true">💓</div>
          <h4 class="mt-3 font-semibold">Chronic Care</h4>
          <p class="mt-1 text-sm text-slate-600">Diabetes, hypertension, lipids, thyroid disorders.</p>
        </div>
        <div class="p-6 rounded-2xl border border-slate-200 hover:shadow-soft transition">
          <div class="text-3xl" aria-hidden="true">👩‍⚕️</div>
          <h4 class="mt-3 font-semibold">Women’s Health</h4>
          <p class="mt-1 text-sm text-slate-600">Preventive screening, anemia, thyroid, PCOS support.</p>
        </div>
        <div class="p-6 rounded-2xl border border-slate-200 hover:shadow-soft transition">
          <div class="text-3xl" aria-hidden="true">🧘</div>
          <h4 class="mt-3 font-semibold">Lifestyle Medicine</h4>
          <p class="mt-1 text-sm text-slate-600">Sleep, nutrition, movement & stress management.</p>
        </div>
        <div class="p-6 rounded-2xl border border-slate-200 hover:shadow-soft transition">
          <div class="text-3xl" aria-hidden="true">💻</div>
          <h4 class="mt-3 font-semibold">Telemedicine</h4>
          <p class="mt-1 text-sm text-slate-600">Secure video consultations for follow‑ups and care.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Experience / Qualifications -->
  <section id="experience" class="py-16 md:py-16 bg-slate-50 border-y border-slate-200">
    <div class="max-w-7xl mx-auto px-4 md:px-8">
      <div class="max-w-2xl">
        <h3 class="text-2xl font-bold">Experience</h3>
        <p class="mt-2 text-slate-600">Training, affiliations & publications</p>
      </div>
      <div class="mt-10 grid lg:grid-cols-2 gap-6">
        <div class="p-6 rounded-2xl bg-white border border-slate-200">
          <h4 class="font-semibold">Education & Training</h4>
          <ul class="mt-3 text-sm space-y-2">
            <li><strong>RMO </strong>— Hope Diagnostic Centre and Hospital, Brahmanbaria</li>
            <li><strong>MBBS</strong>, ABC Medical College — 2013</li>
            <li>Residency, Internal Medicine — City Hospital, 2014–2018</li>
          </ul>
        </div>
        <div class="p-6 rounded-2xl bg-white border border-slate-200">
          <h4 class="font-semibold">Affiliations & Awards</h4>
          <ul class="mt-3 text-sm space-y-2">
            <li>Bangladesh Medical Association — Member</li>
            <li>Dhaka Medical Society — Committee Member</li>
            <li>Patient Choice Award — 2022</li>
          </ul>
        </div>
        {{-- <div class="p-6 rounded-2xl bg-white border border-slate-200 lg:col-span-2">
          <h4 class="font-semibold">Publications</h4>
          <ul class="mt-3 text-sm space-y-2 list-disc pl-5">
            <li>Doe J, et al. “Hypertension management in South Asia.” <em>Journal of IM</em>, 2021.</li>
            <li>Doe J. “Integrated diabetes care in primary practice.” <em>Clin Med</em>, 2020.</li>
          </ul>
        </div> --}}
      </div>
    </div>
  </section>

  <!-- Testimonials -->
  <section id="testimonials" class="py-16 md:py-16">
    <div class="max-w-6xl mx-auto px-4 md:px-8">
      <div class="max-w-2xl">
        <h3 class="text-2xl font-bold">What patients say</h3>
        <p class="mt-2 text-slate-600">Real experiences from real patients</p>
      </div>
      <div class="mt-10 grid md:grid-cols-3 gap-6">
        <figure class="p-6 rounded-2xl border border-slate-200 bg-white">
          <blockquote class="text-slate-700">“I came to Dr. Pavel with uncontrolled diabetes. He listened patiently, explained step by step, and gave me confidence. Today my sugar is under control and I feel healthier than ever.”</blockquote>
          <figcaption class="mt-3 text-sm text-slate-500">— Patient from Brahmanbaria</figcaption>
        </figure>
        <figure class="p-6 rounded-2xl border border-slate-200 bg-white">
          <blockquote class="text-slate-700">“He is not only a good doctor, but also a kind listener. I always feel heard and cared for.”</blockquote>
          <figcaption class="mt-3 text-sm text-slate-500">— Patient from Brahmanbaria</figcaption>
        </figure>
        <figure class="p-6 rounded-2xl border border-slate-200 bg-white">
          <blockquote class="text-slate-700">“I was worried about my blood pressure for years. With Dr. Pavel’s guidance, I now have a clear plan and regular check-ups that keep me stable.”</blockquote>
          <figcaption class="mt-3 text-sm text-slate-500">— Patient from Dhaka</figcaption>
        </figure>
      </div>
    </div>
  </section>

  <!-- Contact / Appointment -->
  <section id="contact" class="py-16 md:py-24 bg-slate-50 border-y border-slate-200">
    <div class="max-w-6xl mx-auto px-4 md:px-8 grid lg:grid-cols-2 gap-10 items-start">
       {{-- !LEFT: Forms --}}
      <div>
        <h3 class="text-3xl font-bold text-brand-700">Book Your Appointment</h3>
        <p class="mt-3 text-slate-600">Quick and simple booking — we’ll confirm within 24 hours.</p>

        {{-- Flash success --}}
        @if (session('status'))
          <div class="mt-6 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-900">
            {{ session('status') }}
          </div>
        @endif

        {{-- Validation errors --}}
        @if ($errors->any())
          <div class="mt-6 rounded-2xl border border-rose-200 bg-rose-50 p-4 text-rose-900">
            <p class="font-semibold">Please fix the following:</p>
            <ul class="mt-2 list-disc pl-6 text-sm">
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        {{-- Mode switch --}}
        <div class="mt-6">
          <div class="inline-flex rounded-2xl border border-slate-200 bg-white p-1">
            <label class="flex items-center gap-2 px-4 py-2 text-sm font-medium">
              <input class="accent-brand-600" type="radio" name="mode" value="existing" checked>
              Follow Up Patient
            </label>
            <label class="flex items-center gap-2 px-4 py-2 text-sm font-medium">
              <input class="accent-brand-600" type="radio" name="mode" value="new">
              New Patient
            </label>
          </div>
          {{-- <p id="phone-hint" class="mt-5 text-sm text-slate-500 bg-red"></p> --}}
         <p id="phone-hint"
   class="hidden mt-5 text-sm font-semibold px-3 py-2 rounded-lg">
</p>
        </div>

        {{-- Existing patient form --}}
        <form id="form-existing" class="mt-8 grid gap-4 bg-white p-6 rounded-2xl shadow-soft border border-slate-200"
              method="POST" action="{{ route('public.appointment.existing') }}" novalidate>
          @csrf
          <h4 class="text-lg font-semibold">Follow Up Patient</h4>

          <div class="grid sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium mb-1" for="ex_phone">Phone <span class="text-rose-600">*</span></label>
              <input id="ex_phone" name="phone" type="tel" inputmode="tel" autocomplete="tel"
                     value="{{ old('phone') }}" required
                     class="w-full rounded-xl border-slate-300 focus:border-brand-500 focus:ring-brand-500" placeholder="e.g. 01XXXXXXXXX">
            </div>
            <div>
              <label class="block text-sm font-medium mb-1" for="ex_dob">Date of birth</label>
              <input id="ex_dob" name="dob" type="date" value="{{ old('dob') }}"
                     class="w-full rounded-xl border-slate-300 focus:border-brand-500 focus:ring-brand-500">
            </div>
          </div>

          <div class="grid sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium mb-1" for="ex_patient_id">Patient ID</label>
              <input id="ex_patient_id" name="patient_id" type="number" inputmode="numeric" value="{{ old('patient_id') }}"
                     class="w-full rounded-xl border-slate-300 focus:border-brand-500 focus:ring-brand-500" placeholder="Optional">
            </div>
            {{-- TODO: This is for Show for all doctors --- Start Here--}}
            {{-- <div>
              <label for="ex_doctor" class="block text-sm font-medium text-slate-700 mb-1">
                Doctor <span class="text-rose-600">*</span>
              </label>
              <div class="relative">
                <select id="ex_doctor" name="doctor_id" required
                        class="w-full appearance-none rounded-xl border border-slate-300 bg-white py-2 px-3 pr-10 text-sm shadow-sm
                              focus:border-brand-500 focus:ring-brand-500 focus:outline-none">
                  <option value="">-- Please choose your doctor --</option>
                  @foreach($doctors as $d)
                    <option value="{{ $d->id }}" @selected(old('doctor_id') == $d->id)>
                      {{ $d->name }}
                    </option>
                  @endforeach
                </select>
                <!-- Custom dropdown arrow -->
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400">
                  <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2"
                      viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                  </svg>
                </div>
              </div>
              @error('doctor_id')
                <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
              @enderror
            </div> --}}
          {{-- TODO: This is for Show for One doctors --- Start  Here--}}
            {{-- <div>
              <label for="ex_doctor" class="block text-sm font-medium text-slate-700 mb-1">
                Doctor <span class="text-rose-600">*</span>
              </label>
              <div class="relative">
                <select id="ex_doctor" name="doctor_id" required disabled
                        class="w-full appearance-none rounded-xl border border-slate-300 bg-white py-2 px-3 pr-10 text-sm shadow-sm
                              focus:border-brand-500 focus:ring-brand-500 focus:outline-none">
                  <option value="">-- Please choose your doctor --</option>
                  @foreach($doctors as $d)
                    <option value="{{ $d->id }}"
                            @if(old('doctor_id', 3) == $d->id) selected @endif>
                      {{ $d->name }}
                    </option>
                  @endforeach
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400">
                  <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                  </svg>
                </div>
              </div>
              @error('doctor_id')
                <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
              @enderror
            </div> --}}
          {{-- TODO: This is for Show for One Doctor doctors --- End Here--}}
            
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1">Doctor</label>
              <p class="w-full rounded-xl border border-slate-200 bg-gray-50 py-2 px-3 text-sm shadow-sm">
                {{ $doctors->firstWhere('id', 3)->name }}
              </p>
              <input type="hidden" name="doctor_id" value="3">
            </div>
          </div>
          <div class="grid sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium mb-1" for="ex_date">Preferred date <span class="text-rose-600">*</span></label>
              <input id="ex_date" name="date" type="date" value="{{ old('date') }}" required
                     class="w-full rounded-xl border-slate-300 focus:border-brand-500 focus:ring-brand-500">
            </div>
            <div>
              <label class="block text-sm font-medium mb-1" for="ex_time">Preferred time <span class="text-rose-600">*</span></label>
              <input id="ex_time" name="start_time" type="time" value="{{ old('start_time') }}" required
                     class="w-full rounded-xl border-slate-300 focus:border-brand-500 focus:ring-brand-500">
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium mb-1" for="ex_notes">Notes</label>
            <textarea id="ex_notes" name="notes" rows="4"
                      class="w-full rounded-xl border-slate-300 focus:border-brand-500 focus:ring-brand-500"
                      placeholder="Reason for visit, symptoms, etc.">{{ old('notes') }}</textarea>
          </div>

          <button type="submit"
                  class="inline-flex justify-center items-center px-5 py-3 rounded-2xl bg-brand-600 hover:bg-brand-700 text-white font-semibold shadow-soft">
            Book as Follow up Patient
          </button>
          <p class="text-xs text-slate-500">Tip: Enter your phone, then either your DOB or your Patient ID to verify.</p>
        </form>

        {{-- New patient form --}}
        <form id="form-new" class="mt-8 grid gap-4 bg-white p-6 rounded-2xl shadow-soft border border-slate-200"
              method="POST" action="{{ route('public.appointment.new') }}" style="display:none" novalidate>
          @csrf
          <h4 class="text-lg font-semibold">New Patient</h4>

          <div class="grid sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium mb-1" for="np_name">Full name <span class="text-rose-600">*</span></label>
              <input id="np_name" name="name" type="text" value="{{ old('name') }}" required
                     class="w-full rounded-xl border-slate-300 focus:border-brand-500 focus:ring-brand-500" placeholder="Your name">
            </div>
            <div>
              <label class="block text-sm font-medium mb-1" for="np_phone">Phone <span class="text-rose-600">*</span></label>
              <input id="np_phone" name="phone" type="tel" inputmode="tel" autocomplete="tel" value="{{ old('phone') }}" required
                     class="w-full rounded-xl border-slate-300 focus:border-brand-500 focus:ring-brand-500" placeholder="e.g. 01XXXXXXXXX">
            </div>
          </div>

          <div class="grid sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium mb-1" for="np_email">Email</label>
              <input id="np_email" name="email" type="email" value="{{ old('email') }}"
                     class="w-full rounded-xl border-slate-300 focus:border-brand-500 focus:ring-brand-500" placeholder="you@example.com">
            </div>
            <div>
              <label class="block text-sm font-medium mb-1" for="np_dob">Date of birth</label>
              <input id="np_dob" name="dob" type="date" value="{{ old('dob') }}"
                     class="w-full rounded-xl border-slate-300 focus:border-brand-500 focus:ring-brand-500">
            </div>
          </div>

          <div class="grid sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1" for="np_gender">Gender</label>
              <select id="np_gender" name="gender"
                      class="w-full appearance-none rounded-xl border border-slate-300 bg-white py-2 px-3 pr-10 text-sm shadow-sm
                              focus:border-brand-500 focus:ring-brand-500 focus:outline-none">
                <option value="">-- choose --</option>
                <option value="male" @selected(old('gender')=='male')>Male</option>
                <option value="female" @selected(old('gender')=='female')>Female</option>
                <option value="other" @selected(old('gender')=='other')>Other</option>
              </select>
            </div>
            {{-- TODO: All Doctor Show -- Start Here --}}
            {{-- <div>
              <label class="block text-sm font-medium mb-1" for="np_doctor">Doctor <span class="text-rose-600">*</span></label>
              <select id="np_doctor" name="doctor_id" required
                      class="w-full rounded-xl border-slate-300 focus:border-brand-500 focus:ring-brand-500">
                <option value="">-- choose --</option>
                @foreach($doctors as $d)
                  <option value="{{ $d->id }}" @selected(old('doctor_id')==$d->id)>{{ $d->name }}</option>
                @endforeach
              </select>
            </div> --}}
            {{-- TODO: All Doctor Show -- End Here --}}
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1">Doctor</label>
              <p class="w-full rounded-xl border border-slate-200 bg-gray-50 py-2 px-3 text-sm shadow-sm">
                {{ $doctors->firstWhere('id', 3)->name }}
              </p>
              <input type="hidden" name="doctor_id" value="3">
            </div>
          </div>

          <div class="grid sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium mb-1" for="np_date">Preferred date <span class="text-rose-600">*</span></label>
              <input id="np_date" name="date" type="date" value="{{ old('date') }}" required
                     class="w-full rounded-xl border-slate-300 focus:border-brand-500 focus:ring-brand-500">
            </div>
            <div>
              <label class="block text-sm font-medium mb-1" for="np_time">Preferred time <span class="text-rose-600">*</span></label>
              <input id="np_time" name="start_time" type="time" value="{{ old('start_time') }}" required
                     class="w-full rounded-xl border-slate-300 focus:border-brand-500 focus:ring-brand-500">
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium mb-1" for="np_notes">Notes</label>
            <textarea id="np_notes" name="notes" rows="4"
                      class="w-full rounded-xl border-slate-300 focus:border-brand-500 focus:ring-brand-500"
                      placeholder="Reason for visit, symptoms, etc.">{{ old('notes') }}</textarea>
          </div>

          <button type="submit"
                  class="inline-flex justify-center items-center px-5 py-3 rounded-2xl bg-brand-600 hover:bg-brand-700 text-white font-semibold shadow-soft">
            Book as New Patient
          </button>
        </form>

        <p class="mt-3 text-xs text-slate-500">Emergency? Call 999 or visit the nearest emergency department.</p>
      </div>

      <div class="space-y-6">
        <div class="p-6 rounded-2xl bg-white border border-slate-200">
          <h4 class="font-semibold">Clinic Hours</h4>
          <dl class="mt-3 grid grid-cols-2 gap-2 text-sm">
            <div class="flex justify-between border-b py-2"><dt>Sun</dt><dd>10:00–18:00</dd></div>
            <div class="flex justify-between border-b py-2"><dt>Mon</dt><dd>10:00–18:00</dd></div>
            <div class="flex justify-between border-b py-2"><dt>Tue</dt><dd>10:00–18:00</dd></div>
            <div class="flex justify-between border-b py-2"><dt>Wed</dt><dd>10:00–18:00</dd></div>
            <div class="flex justify-between border-b py-2"><dt>Thu</dt><dd>10:00–18:00</dd></div>
            <div class="flex justify-between border-b py-2"><dt>Fri</dt><dd>Closed</dd></div>
            <div class="flex justify-between py-2"><dt>Sat</dt><dd>Closed</dd></div>
          </dl>
        </div>
        <div class="p-6 rounded-2xl bg-white border border-slate-200">
          <h4 class="font-semibold">Location</h4>
          <p class="text-sm text-slate-600 mt-1">12 Lakeview Rd, Gulshan, Dhaka 1212</p>
          <div class="mt-4 aspect-[16/9] w-full overflow-hidden rounded-xl border border-slate-200">
            <!-- Embed map (replace src with your own Google Maps embed link) -->
            <iframe title="Clinic map" class="w-full h-full" style="border:0" loading="lazy" allowfullscreen referrerpolicy="no-referrer-when-downgrade" src="https://maps.google.com/maps?q=Dhaka%20Gulshan&t=&z=13&ie=UTF8&iwloc=&output=embed"></iframe>
          </div>
          <div class="mt-4 flex flex-wrap gap-3 text-sm">
            <a href="tel:+8801000000000" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-slate-300 hover:bg-slate-50">📞 Call</a>
            <a href="mailto:clinic@example.com" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-slate-300 hover:bg-slate-50">✉️ Email</a>
            <a href="#" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-brand-600 hover:bg-brand-700 text-white">🧭 Get Directions</a>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer class="py-10">
    <div class="max-w-7xl mx-auto px-4 md:px-8 flex flex-col md:flex-row items-center md:items-start justify-between gap-6">
      <div class="flex items-center gap-3">
        <div class="h-10 w-10 rounded-xl bg-brand-600 text-white grid place-content-center font-semibold">JD</div>
        <div>
          <p class="font-semibold">Dr. Shabuddin Hossain Pavel</p>
          <p class="text-sm text-slate-500">MBBS, FMCH — Internal Medicine</p>
        </div>
      </div>
      <div class="text-sm text-slate-600">
        <p>© <span id="year"></span> Dr. Shabuddin Hossain Pavel. All rights reserved.</p>
      </div>
      <div class="text-sm">
        <a class="hover:text-brand-600" href="#privacy">Privacy</a>
        <span class="mx-2 text-slate-400">•</span>
        <a class="hover:text-brand-600" href="#terms">Terms</a>
      </div>
    </div>
  </footer>

  {{-- <script>
    // Mobile nav
    const btn = document.getElementById('menuBtn');
    const menu = document.getElementById('mobileMenu');
    btn?.addEventListener('click', () => menu.classList.toggle('hidden'));
    // Year
    document.getElementById('year').textContent = new Date().getFullYear();
    // Fake form handler (replace with real endpoint)
    document.querySelector('form')?.addEventListener('submit', (e) => {
      e.preventDefault();
      alert('Thanks! We\'ll contact you shortly to confirm your appointment.');
      e.target.reset();
    });
  </script> --}}
  
<script>
  // Toggle forms
  document.querySelectorAll('input[name="mode"]').forEach(r => {
    r.addEventListener('change', () => {
      const isExisting = document.querySelector('input[name="mode"]:checked').value === 'existing';
      document.getElementById('form-existing').style.display = isExisting ? '' : 'none';
      document.getElementById('form-new').style.display      = isExisting ? 'none' : '';
      // Hide hint when switching forms
      hideHint();
    });
  });

  // Elements
  const exPhone = document.getElementById('ex_phone');
  const exDob   = document.getElementById('ex_dob');
  const exPid   = document.getElementById('ex_patient_id');
  const hint    = document.getElementById('phone-hint');
  let t;

  // Helpers
  function hideHint() {
    if (!hint) return;
    hint.textContent = '';
    hint.className = 'hidden mt-5 text-sm font-semibold px-3 py-2 rounded-lg';
  }

  function showHint(message, type = 'error') {
    if (!hint) return;
    if (!message) { hideHint(); return; }

    // base classes
    hint.className = 'mt-5 text-sm font-semibold px-3 py-2 rounded-lg border';

    if (type === 'success') {
      hint.classList.add('text-green-700','bg-green-100','border-green-300');
    } else {
      hint.classList.add('text-red-700','bg-red-100','border-red-300');
    }
    hint.textContent = message;
  }

  async function checkPatient() {
    clearTimeout(t);

    const phone = exPhone?.value.trim() ?? '';
    const dob   = exDob?.value ?? '';
    const pid   = exPid?.value ?? '';

    // If cleared or too short → hide hint and stop
    if (!phone || phone.length < 6) {
      hideHint();
      return;
    }

    t = setTimeout(async () => {
      try {
        const u = new URL(@json(route('public.patients.check')), window.location.origin);
        u.searchParams.set('phone', phone);
        if (dob) u.searchParams.set('dob', dob);
        if (pid) u.searchParams.set('patient_id', pid);

        const r = await fetch(u, { credentials: 'same-origin' });
        if (!r.ok) { hideHint(); return; }

        const data = await r.json();
        if (data?.exists) {
          showHint(`✅ Found: ${data.patient?.name ?? 'match found'}`, 'success');
        } else {
          showHint('⚠️ No matching patient found.', 'error');
        }
      } catch {
        hideHint();
      }
    }, 400);
  }

  // Wire events
  exPhone?.addEventListener('input', checkPatient);
  exDob?.addEventListener('change', checkPatient);
  exPid?.addEventListener('input', checkPatient);
</script>


{{-- <script>
  // Toggle forms
  document.querySelectorAll('input[name="mode"]').forEach(r => {
    r.addEventListener('change', () => {
      const isExisting = document.querySelector('input[name="mode"]:checked').value === 'existing';
      document.getElementById('form-existing').style.display = isExisting ? '' : 'none';
      document.getElementById('form-new').style.display      = isExisting ? 'none' : '';
    });
  });

  // Optional: live phone check for existing patients
  const exPhone = document.getElementById('ex_phone');
const exDob   = document.getElementById('ex_dob');
const exPid   = document.getElementById('ex_patient_id');
const hint    = document.getElementById('phone-hint');
let t;

function checkPatient() {



  const hint = document.getElementById('phone-hint');

function showHint(message, type = 'error') {
  if (!message) {
    hint.classList.add('hidden');
    return;
  }

  hint.textContent = message;
  hint.className = 'mt-5 text-sm font-semibold px-3 py-2 rounded-lg'; // reset

  if (type === 'error') {
    hint.classList.add('text-red-700','bg-red-100','border','border-red-300');
  } else {
    hint.classList.add('text-green-700','bg-green-100','border','border-green-300');
  }

  hint.classList.remove('hidden');
}



  clearTimeout(t);
  const phone = exPhone.value.trim();
  const dob   = exDob.value;
  const pid   = exPid.value;

  if (phone.length < 6) {
    hint.textContent = '';
    return;
  }

  t = setTimeout(async () => {
    try {
      const u = new URL('{{ route('public.patients.check') }}', window.location.origin);
      u.searchParams.set('phone', phone);
      if (dob) u.searchParams.set('dob', dob);
      if (pid) u.searchParams.set('patient_id', pid);

      const r = await fetch(u, {credentials:'same-origin'});
      if (!r.ok) return;
      const data = await r.json();

      hint.textContent = ''; // clear first
if (data.exists) {
  showHint(`✅ Found: ${data.patient?.name}`, 'success');
} else {
  showHint('⚠️ No matching patient found.', 'error');
}
    } catch {}
  }, 400);
}

exPhone?.addEventListener('input', checkPatient);
exDob?.addEventListener('change', checkPatient);
exPid?.addEventListener('input', checkPatient);

</script> --}}


</body>
</html>
