<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>DoctorCare - Home</title>
  @vite('resources/css/app.css')
  <style>
    /* smooth scroll + avoid anchor being hidden under fixed header */
    html { scroll-behavior: smooth; scroll-padding-top: 88px; }
  </style>
</head>
<body class="bg-gray-50 text-gray-800">

  <!-- fixed header with topbar and responsive nav -->
  <header class="fixed top-0 left-0 w-full z-50">
    <!-- Top bar -->
    <div class="bg-blue-600 text-white text-sm">
      <div class="max-w-7xl mx-auto px-4 py-1 flex justify-between items-center">
        <div class="flex items-center gap-4">
          <span class="hidden sm:inline">📞 +1 665-891-4556</span>
          <span class="hidden sm:inline">|</span>
          <span class="hidden sm:inline">✉ info@doctorcare.com</span>
        </div>
        <div class="text-sm space-x-4">
          <a href="#" class="hover:underline">Login</a>
          <a href="#" class="hover:underline">Register</a>
        </div>
      </div>
    </div>

    <!-- Main nav -->
    <div class="bg-white shadow">
      <div class="max-w-7xl mx-auto px-4 py-4 flex items-center justify-between">
        <a href="/" class="text-2xl font-bold text-blue-600">DoctorCare</a>

        <!-- Desktop menu -->
        <nav class="hidden md:flex items-center space-x-6" id="desktop-menu">
          <a href="#home" class="nav-link text-gray-700 hover:text-blue-600">Home</a>
          <a href="#about" class="nav-link text-gray-700 hover:text-blue-600">About</a>
          <a href="#services" class="nav-link text-gray-700 hover:text-blue-600">Services</a>
          <a href="#doctors" class="nav-link text-gray-700 hover:text-blue-600">Doctors</a>
          <a href="#specialties" class="nav-link text-gray-700 hover:text-blue-600">Specialties</a>
          <a href="#blog" class="nav-link text-gray-700 hover:text-blue-600">Blog</a>
          <a href="#contact" class="nav-link text-gray-700 hover:text-blue-600">Contact</a>
        </nav>

        <!-- Mobile hamburger -->
        <div class="md:hidden flex items-center">
          <button id="nav-toggle" aria-controls="mobile-menu" aria-expanded="false"
                  class="p-2 rounded-md border hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-200">
            <!-- hamburger icon -->
            <svg id="hamburger" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                 viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 6h16M4 12h16M4 18h16" />
            </svg>
            <!-- close icon (hidden by default) -->
            <svg id="xicon" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 hidden" fill="none"
                 viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
      </div>

      <!-- Mobile menu: initially hidden -->
      <div id="mobile-menu" class="md:hidden hidden bg-white border-t">
        <nav class="px-4 py-4 space-y-2">
          <a href="#home" class="block py-2 nav-link text-gray-700 hover:text-blue-600">Home</a>
          <a href="#about" class="block py-2 nav-link text-gray-700 hover:text-blue-600">About</a>
          <a href="#services" class="block py-2 nav-link text-gray-700 hover:text-blue-600">Services</a>
          <a href="#doctors" class="block py-2 nav-link text-gray-700 hover:text-blue-600">Doctors</a>
          <a href="#specialties" class="block py-2 nav-link text-gray-700 hover:text-blue-600">Specialties</a>
          <a href="#blog" class="block py-2 nav-link text-gray-700 hover:text-blue-600">Blog</a>
          <a href="#contact" class="block py-2 nav-link text-gray-700 hover:text-blue-600">Contact</a>
        </nav>
      </div>
    </div>
  </header>

  <!-- main content -->
  <!-- add top padding so fixed header doesn't cover content -->
  <main class="pt-[110px]"> <!-- 110px to cover topbar + navbar; adjust if you change header height -->
    <!-- HERO -->
    <section id="home" class="bg-blue-50 py-20">
      <div class="max-w-7xl mx-auto px-4 text-center">
        <h1 class="text-4xl md:text-5xl font-bold mb-4">Search Doctor, Make an Appointment</h1>
        <p class="text-lg text-gray-700 mb-8">Discover the best doctors, clinics & hospitals nearby.</p>

        <form class="grid grid-cols-1 md:grid-cols-4 gap-4 max-w-4xl mx-auto">
          <select class="border rounded-lg p-3">
            <option>Location</option>
            <option>Chicago</option>
            <option>Los Angeles</option>
            <option>New York</option>
          </select>
          <select class="border rounded-lg p-3">
            <option>Gender</option>
            <option>Male</option>
            <option>Female</option>
            <option>Other</option>
          </select>
          <select class="border rounded-lg p-3">
            <option>Specialty</option>
            <option>Cardiologist</option>
            <option>Dentist</option>
            <option>Neurology</option>
          </select>
          <button class="bg-blue-600 text-white font-semibold px-6 py-3 rounded-lg hover:bg-blue-700">
            Search
          </button>
        </form>
      </div>
    </section>

    <!-- Quick Services -->
    <section id="services" class="py-16 bg-white">
      <div class="max-w-7xl mx-auto px-4 grid md:grid-cols-3 gap-8 text-center">
        <div class="p-8 border rounded-lg hover:shadow-lg transition">
          <h3 class="text-xl font-semibold mb-3">Visit a Doctor</h3>
          <p class="text-gray-600">Find the best specialists and book instantly.</p>
        </div>
        <div class="p-8 border rounded-lg hover:shadow-lg transition">
          <h3 class="text-xl font-semibold mb-3">Find a Pharmacy</h3>
          <p class="text-gray-600">Locate trusted pharmacies with ease.</p>
        </div>
        <div class="p-8 border rounded-lg hover:shadow-lg transition">
          <h3 class="text-xl font-semibold mb-3">Find a Lab</h3>
          <p class="text-gray-600">Get accurate diagnostics in modern labs.</p>
        </div>
      </div>
    </section>

    <!-- Doctors -->
    <section id="doctors" class="py-16 bg-gray-50">
      <div class="max-w-7xl mx-auto px-4">
        <h2 class="text-3xl font-bold text-center mb-12">Our Best Doctors</h2>
        <div class="grid md:grid-cols-3 lg:grid-cols-4 gap-8">
          @foreach(range(1,4) as $i)
          <div class="bg-white rounded-lg shadow overflow-hidden">
            <img src="https://via.placeholder.com/400x300" alt="Doctor" class="w-full h-48 object-cover">
            <div class="p-6 text-center">
              <h3 class="text-xl font-semibold">Dr. John Doe</h3>
              <p class="text-blue-600">Cardiologist</p>
              <button class="mt-4 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                Book Appointment
              </button>
            </div>
          </div>
          @endforeach
        </div>
      </div>
    </section>

    <!-- Clinic Features -->
    <section class="py-16 bg-white">
      <div class="max-w-7xl mx-auto px-4">
        <h2 class="text-3xl font-bold text-center mb-12">Available Features in Our Clinic</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
          <div><div class="text-4xl mb-2">🏥</div><h4 class="font-semibold">Medical</h4></div>
          <div><div class="text-4xl mb-2">🔬</div><h4 class="font-semibold">Laboratory</h4></div>
          <div><div class="text-4xl mb-2">💉</div><h4 class="font-semibold">Operation</h4></div>
          <div><div class="text-4xl mb-2">🛏</div><h4 class="font-semibold">ICU</h4></div>
        </div>
      </div>
    </section>

    <!-- Specialties -->
    <section id="specialties" class="py-16 bg-gray-50">
      <div class="max-w-7xl mx-auto px-4">
        <h2 class="text-3xl font-bold text-center mb-12">Our Specialties</h2>
        <div class="grid md:grid-cols-3 lg:grid-cols-4 gap-8">
          <div class="bg-white p-6 rounded-lg shadow text-center">
            <h4 class="text-xl font-semibold">Cardiology</h4>
            <p class="mt-2 text-3xl text-blue-600">5 Doctors</p>
          </div>
          <div class="bg-white p-6 rounded-lg shadow text-center">
            <h4 class="text-xl font-semibold">Neurology</h4>
            <p class="mt-2 text-3xl text-blue-600">3 Doctors</p>
          </div>
          <div class="bg-white p-6 rounded-lg shadow text-center">
            <h4 class="text-xl font-semibold">Dental</h4>
            <p class="mt-2 text-3xl text-blue-600">4 Doctors</p>
          </div>
          <div class="bg-white p-6 rounded-lg shadow text-center">
            <h4 class="text-xl font-semibold">Urology</h4>
            <p class="mt-2 text-3xl text-blue-600">2 Doctors</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Blog -->
    <section id="blog" class="py-16 bg-white">
      <div class="max-w-7xl mx-auto px-4">
        <h2 class="text-3xl font-bold text-center mb-12">Blogs & News</h2>
        <div class="grid md:grid-cols-3 gap-8">
          @foreach(range(1,3) as $i)
          <div class="bg-gray-50 rounded-lg shadow overflow-hidden">
            <img src="https://via.placeholder.com/400x250" alt="Blog" class="w-full h-48 object-cover">
            <div class="p-6">
              <p class="text-sm text-gray-500 mb-2">July 18, 2023</p>
              <h3 class="text-xl font-semibold mb-3">Making Your Clinic Visit Painless</h3>
              <a href="#" class="text-blue-600 hover:underline">Read More</a>
            </div>
          </div>
          @endforeach
        </div>
      </div>
    </section>

    <!-- Newsletter + Contact -->
    <section id="contact" class="py-16 bg-blue-600 text-white text-center">
      <div class="max-w-7xl mx-auto px-4">
        <h2 class="text-3xl font-bold mb-6">Subscribe & Stay Updated</h2>
        <form class="max-w-md mx-auto flex flex-col md:flex-row gap-4">
          <input type="email" placeholder="Enter your email" class="p-3 rounded-lg text-gray-800 flex-1" required>
          <button class="bg-white text-blue-600 font-semibold px-6 py-3 rounded-lg hover:bg-gray-100">Subscribe</button>
        </form>
        <div class="mt-10">
          <p>📍 123 Medical Street, Dhaka, Bangladesh</p>
          <p>📞 +880 1234-567890 | ✉ info@doctorcare.com</p>
        </div>
      </div>
    </section>

    <footer class="bg-gray-900 text-gray-300 text-center py-6">
      <p>&copy; {{ date('Y') }} DoctorCare. All rights reserved.</p>
    </footer>
  </main>

  <!-- small JS for mobile menu + active link highlight -->
  <script>
    (function(){
      const btn = document.getElementById('nav-toggle');
      const mobileMenu = document.getElementById('mobile-menu');
      const hamburger = document.getElementById('hamburger');
      const xicon = document.getElementById('xicon');

      btn.addEventListener('click', () => {
        const isHidden = mobileMenu.classList.toggle('hidden');
        // toggle icons
        hamburger.classList.toggle('hidden');
        xicon.classList.toggle('hidden');
        // update aria
        const expanded = btn.getAttribute('aria-expanded') === 'true';
        btn.setAttribute('aria-expanded', (!expanded).toString());
      });

      // Optional: highlight clicked links (desktop & mobile)
      document.querySelectorAll('.nav-link').forEach(el => {
        el.addEventListener('click', (e) => {
          document.querySelectorAll('.nav-link').forEach(x => x.classList.remove('text-blue-600','font-semibold'));
          e.currentTarget.classList.add('text-blue-600','font-semibold');
          // close mobile menu after click
          if (!mobileMenu.classList.contains('hidden')) {
            mobileMenu.classList.add('hidden');
            hamburger.classList.remove('hidden');
            xicon.classList.add('hidden');
            btn.setAttribute('aria-expanded','false');
          }
        });
      });
    })();
  </script>
</body>
</html>
