{{-- resources/views/frontend/blog.blade.php --}}
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Blog — Health Tips & Clinic Updates</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: { sans: ["Inter", "ui-sans-serif", "system-ui"] },
          colors: {
            brand: {50:'#eff6ff',100:'#dbeafe',200:'#bfdbfe',300:'#93c5fd',400:'#60a5fa',500:'#3b82f6',600:'#2563eb',700:'#1d4ed8',800:'#1e40af',900:'#1e3a8a'}
          },
          boxShadow: {soft:'0 15px 30px -10px rgba(0,0,0,0.15)'}
        }
      }
    }
  </script>
</head>
<body class="bg-white text-slate-800 antialiased">

  {{-- Top bar (optional) --}}
  <div class="hidden md:flex items-center justify-between px-4 md:px-8 py-2 text-sm bg-slate-50 border-b border-slate-200">
    <p class="flex items-center gap-2"><span aria-hidden="true">📍</span> 12 Lakeview Rd, Dhaka</p>
    <a href="tel:+8801000000000" class="flex items-center gap-2 hover:text-brand-600 transition"><span aria-hidden="true">📞</span> +880 10-0000-0000</a>
  </div>

  {{-- Header --}}
  <header class="sticky top-0 z-40 bg-white/80 backdrop-blur border-b border-slate-200">
    <div class="max-w-7xl mx-auto px-4 md:px-8 py-3 flex items-center justify-between">
      <a href="{{ url('/') }}" class="flex items-center gap-3">
        <div class="h-10 w-10 rounded-xl bg-brand-600 text-white grid place-content-center font-semibold">SHP</div>
        <div>
          <p class="text-lg font-semibold leading-tight">Dr. Shabuddin Hossain Pavel</p>
          <p class="text-xs text-slate-500">MBBS, FMCH — Internal Medicine</p>
        </div>
      </a>
      <nav class="hidden md:flex gap-6 text-sm">
        <a class="hover:text-brand-600" href="{{ url('/') }}#home">Home</a>
        <a class="hover:text-brand-600" href="{{ url('/') }}#services">Services</a>
        <a class="hover:text-brand-600" href="{{ url('/') }}#contact">Contact</a>
        <a class="text-brand-700 font-medium" href="{{ url('/blog') }}">Blog</a>
      </nav>
      <a href="{{ url('/') }}#contact" class="hidden md:inline-flex items-center gap-2 bg-brand-600 hover:bg-brand-700 text-white text-sm font-medium px-4 py-2 rounded-xl shadow-soft transition">
        Book Appointment
      </a>
      <button id="menuBtn" class="md:hidden inline-flex items-center justify-center h-10 w-10 rounded-lg border border-slate-300">☰</button>
    </div>
    <div id="mobileMenu" class="md:hidden hidden border-t border-slate-200">
      <nav class="px-4 py-3 grid gap-2 text-sm">
        <a class="py-2" href="{{ url('/') }}#home">Home</a>
        <a class="py-2" href="{{ url('/') }}#services">Services</a>
        <a class="py-2" href="{{ url('/blog') }}">Blog</a>
        <a class="py-2" href="{{ url('/') }}#contact">Contact</a>
      </nav>
    </div>
  </header>

  {{-- Page hero --}}
  <section class="bg-gradient-to-br from-brand-50 to-white border-b border-slate-200">
    <div class="max-w-7xl mx-auto px-4 md:px-8 py-10 md:py-14">
      <span class="inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-brand-700 bg-brand-50 border border-brand-100 px-3 py-1 rounded-full">Clinic Blog</span>
      <h1 class="mt-4 text-3xl md:text-4xl font-extrabold">Health tips, clinic news & patient guides</h1>
      <p class="mt-3 text-slate-600 max-w-2xl">Simple, trustworthy advice to help you manage conditions like diabetes, high blood pressure, and thyroid issues — plus updates from our clinic.</p>
    </div>
  </section>

  {{-- Content --}}
  <main class="max-w-7xl mx-auto px-4 md:px-8 py-10 grid lg:grid-cols-12 gap-10">
    {{-- Featured post --}}
    <section class="lg:col-span-8">
      <article class="rounded-3xl overflow-hidden border border-slate-200 bg-white shadow-soft">
        <div class="md:flex">
          <a href="#" class="block md:w-1/2">
            <img class="h-56 md:h-full w-full object-cover" src="https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?q=80&w=1500&auto=format&fit=crop" alt="Featured Post">
          </a>
          <div class="p-6 md:w-1/2">
            <div class="flex items-center gap-2 text-xs text-brand-700 font-semibold">
              <span class="bg-brand-50 border border-brand-100 px-2 py-0.5 rounded">Hypertension</span>
              <span>•</span>
              <time datetime="2025-09-01" class="text-slate-500 font-normal">Sep 1, 2025</time>
            </div>
            <h2 class="mt-3 text-xl md:text-2xl font-bold">
              <a href="#" class="hover:text-brand-700">7 daily habits to keep your blood pressure in check</a>
            </h2>
            <p class="mt-3 text-slate-600">Small, consistent routines — like the DASH plate, 30-minute walks, and better sleep — make a big difference. Here’s how to start safely.</p>
            <div class="mt-5 flex items-center justify-between">
              <a href="#" class="inline-flex items-center gap-2 text-brand-700 font-medium hover:underline">Read more →</a>
              <p class="text-sm text-slate-500">5 min read</p>
            </div>
          </div>
        </div>
      </article>

      {{-- Post grid --}}
      <div class="mt-10 grid sm:grid-cols-2 gap-6">
        @php
          // Example data (replace with posts from DB)
          $posts = [
            ['title'=>'Beginner’s guide to thyroid tests','tag'=>'Thyroid','date'=>'Aug 22, 2025','img'=>'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?q=80&w=1200&auto=format&fit=crop','href'=>'#','read'=>'4 min'],
            ['title'=>'What to eat with type 2 diabetes','tag'=>'Diabetes','date'=>'Aug 12, 2025','img'=>'https://images.unsplash.com/photo-1556911220-e15b29be8c8f?q=80&w=1200&auto=format&fit=crop','href'=>'#','read'=>'6 min'],
            ['title'=>'Clinic update: new telemedicine hours','tag'=>'Updates','date'=>'Aug 05, 2025','img'=>'https://images.unsplash.com/photo-1586773860418-d37222d8fce3?q=80&w=1200&auto=format&fit=crop','href'=>'#','read'=>'2 min'],
            ['title'=>'Low-salt cooking swaps that taste great','tag'=>'Lifestyle','date'=>'Jul 28, 2025','img'=>'https://images.unsplash.com/photo-1505575967455-40e256f73376?q=80&w=1200&auto=format&fit=crop','href'=>'#','read'=>'3 min'],
          ];
        @endphp

        @foreach($posts as $p)
          <article class="group overflow-hidden rounded-2xl border border-slate-200 bg-white hover:shadow-soft transition">
            <a href="{{ $p['href'] }}" class="block aspect-[16/9] overflow-hidden">
              <img src="{{ $p['img'] }}" alt="{{ $p['title'] }}" class="h-full w-full object-cover group-hover:scale-[1.03] transition">
            </a>
            <div class="p-5">
              <div class="flex items-center gap-2 text-xs text-brand-700 font-semibold">
                <span class="bg-brand-50 border border-brand-100 px-2 py-0.5 rounded">{{ $p['tag'] }}</span>
                <span>•</span>
                <span class="text-slate-500 font-normal">{{ $p['date'] }}</span>
              </div>
              <h3 class="mt-2 text-lg font-semibold leading-snug">
                <a href="{{ $p['href'] }}" class="hover:text-brand-700">{{ $p['title'] }}</a>
              </h3>
              <div class="mt-4 flex items-center justify-between">
                <a href="{{ $p['href'] }}" class="text-sm text-brand-700 font-medium hover:underline">Read more →</a>
                <span class="text-xs text-slate-500">{{ $p['read'] }}</span>
              </div>
            </div>
          </article>
        @endforeach
      </div>

      {{-- Pagination (static example) --}}
      <nav class="mt-10 flex items-center justify-between" aria-label="Pagination">
        <a href="#" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-slate-300 hover:bg-slate-50 text-sm">← Newer</a>
        <div class="hidden sm:flex items-center gap-2 text-sm">
          <a href="#" class="px-3 py-1 rounded-lg border border-slate-300">1</a>
          <a href="#" class="px-3 py-1 rounded-lg border border-slate-300 bg-brand-600 text-white">2</a>
          <a href="#" class="px-3 py-1 rounded-lg border border-slate-300">3</a>
        </div>
        <a href="#" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-slate-300 hover:bg-slate-50 text-sm">Older →</a>
      </nav>
    </section>

    {{-- Sidebar --}}
    <aside class="lg:col-span-4 space-y-6">
      {{-- Search --}}
      <div class="p-5 rounded-2xl border border-slate-200 bg-white">
        <form action="#" method="GET" class="flex">
          <input type="search" name="q" placeholder="Search articles…" class="flex-1 rounded-l-xl border border-slate-300 px-3 py-2 text-sm focus:border-brand-500 focus:ring-brand-500">
          <button class="rounded-r-xl bg-brand-600 hover:bg-brand-700 text-white text-sm px-4">Search</button>
        </form>
      </div>

      {{-- Categories --}}
      <div class="p-5 rounded-2xl border border-slate-200 bg-white">
        <h4 class="font-semibold">Categories</h4>
        <ul class="mt-3 grid grid-cols-2 gap-2 text-sm">
          <li><a href="#" class="block rounded-lg border border-slate-200 px-3 py-2 hover:border-brand-400">Diabetes</a></li>
          <li><a href="#" class="block rounded-lg border border-slate-200 px-3 py-2 hover:border-brand-400">Hypertension</a></li>
          <li><a href="#" class="block rounded-lg border border-slate-200 px-3 py-2 hover:border-brand-400">Thyroid</a></li>
          <li><a href="#" class="block rounded-lg border border-slate-200 px-3 py-2 hover:border-brand-400">Lifestyle</a></li>
          <li><a href="#" class="block rounded-lg border border-slate-200 px-3 py-2 hover:border-brand-400">Nutrition</a></li>
          <li><a href="#" class="block rounded-lg border border-slate-200 px-3 py-2 hover:border-brand-400">Updates</a></li>
        </ul>
      </div>

      {{-- Recent posts --}}
      <div class="p-5 rounded-2xl border border-slate-200 bg-white">
        <h4 class="font-semibold">Recent posts</h4>
        <ul class="mt-3 space-y-3">
          <li class="flex gap-3">
            <img class="h-14 w-20 rounded-lg object-cover border border-slate-200" src="https://images.unsplash.com/photo-1512621776951-a57141f2eefd?q=80&w=600&auto=format&fit=crop" alt="">
            <div class="text-sm">
              <a href="#" class="font-medium hover:text-brand-700">DASH diet: easy starter plate</a>
              <p class="text-xs text-slate-500">Aug 18, 2025</p>
            </div>
          </li>
          <li class="flex gap-3">
            <img class="h-14 w-20 rounded-lg object-cover border border-slate-200" src="https://images.unsplash.com/photo-1562813733-b31f71025d54?q=80&w=600&auto=format&fit=crop" alt="">
            <div class="text-sm">
              <a href="#" class="font-medium hover:text-brand-700">How to measure BP at home</a>
              <p class="text-xs text-slate-500">Aug 10, 2025</p>
            </div>
          </li>
          <li class="flex gap-3">
            <img class="h-14 w-20 rounded-lg object-cover border border-slate-200" src="https://images.unsplash.com/photo-1580974938420-59cb8ab3b3a0?q=80&w=600&auto=format&fit=crop" alt="">
            <div class="text-sm">
              <a href="#" class="font-medium hover:text-brand-700">Understanding TSH, T3 & T4</a>
              <p class="text-xs text-slate-500">Aug 2, 2025</p>
            </div>
          </li>
        </ul>
      </div>

      {{-- Newsletter --}}
      <div class="p-5 rounded-2xl border border-slate-200 bg-white">
        <h4 class="font-semibold">Get health tips in your inbox</h4>
        <p class="mt-1 text-sm text-slate-600">Monthly, no spam.</p>
        <form class="mt-3 flex">
          <input type="email" placeholder="you@example.com" class="flex-1 rounded-l-xl border border-slate-300 px-3 py-2 text-sm focus:border-brand-500 focus:ring-brand-500">
          <button class="rounded-r-xl bg-brand-600 hover:bg-brand-700 text-white text-sm px-4">Subscribe</button>
        </form>
      </div>
    </aside>
  </main>

  {{-- Footer --}}
  <footer class="py-10">
    <div class="max-w-7xl mx-auto px-4 md:px-8 flex flex-col md:flex-row items-center md:items-start justify-between gap-6">
      <div class="flex items-center gap-3">
        <div class="h-10 w-10 rounded-xl bg-brand-600 text-white grid place-content-center font-semibold">SHP</div>
        <div>
          <p class="font-semibold">Dr. Shabuddin Hossain Pavel</p>
          <p class="text-sm text-slate-500">MBBS, FMCH — Internal Medicine</p>
        </div>
      </div>
      <div class="text-sm text-slate-600">
        <p>© <span id="year"></span> SHP Clinic. All rights reserved.</p>
      </div>
      <div class="text-sm">
        <a class="hover:text-brand-600" href="{{ url('/blog') }}">Blog</a>
        <span class="mx-2 text-slate-400">•</span>
        <a class="hover:text-brand-600" href="{{ url('/') }}#privacy">Privacy</a>
      </div>
    </div>
  </footer>

  <script>
    document.getElementById('year').textContent = new Date().getFullYear();
    const btn = document.getElementById('menuBtn');
    const mm = document.getElementById('mobileMenu');
    btn?.addEventListener('click', () => mm.classList.toggle('hidden'));
  </script>
</body>
</html>
