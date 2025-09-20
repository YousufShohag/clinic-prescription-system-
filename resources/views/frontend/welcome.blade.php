{{-- resources/frontend/wellcome.blade.php --}}
@extends('frontend.app')

@section('title', 'Welcome')

@section('content')
 
    <!-- HERO -->
    <section class="hero section reveal in" id="top">
        <!-- Left text card -->
        <div class="card">
            <h1 class="headline">
            Welcome to <span class="accent-gradient">Dr.Shabuddin Hossain Pavel's Clinic</span>
            </h1>
            <p class="sub">
            Providing compassionate healthcare, timely consultation, and reliable treatment for you and your family.
            </p>

            <div style="display:flex;gap:12px;flex-wrap:wrap;margin-top:16px">
            <a class="btn" href="#appointment">Book an Appointment</a>
            <a class="pill">📞 +880 123 456 789</a>
            </div>

            <div class="pills" style="margin-top:14px">
            <span class="pill">General Medicine</span>
            <span class="pill">Health Checkups</span>
            <span class="pill">Chronic Care</span>
            <span class="pill">Family Wellness</span>
            </div>
        </div>

        <!-- Right image card -->
        <div class="card tilt" style="text-align:center">
            <img class="avatar" src="{{ asset('doctor4.jpg') }}" alt="Doctor profile photo"/>
            <p class="sub" style="margin-top:10px">
            Caring for your health, one appointment at a time
            </p>
        </div>
    </section>

    <!-- ABOUT -->
    <section id="about" class="section about reveal">
        <div class="about-container" style="display:grid;grid-template-columns:1fr 1fr;gap:40px;align-items:center;">
            
            <!-- Doctor Image -->
            <div class="about-image" style="text-align:center;">
            <img src="{{ asset('doctor2.jpg') }}" alt="Dr. Yousuf Patwari" style="width:100%;max-width:350px;border-radius:20px;box-shadow:0 8px 20px rgba(0,0,0,0.1);" />
            </div>

            <!-- Doctor Bio -->
            <div class="about-content">
            <h2>About Dr. Shabuddin Hossain Pavel</h2>
            <p>
                With a deep passion for patient care, I specialize in providing
                <strong>comprehensive family medicine</strong> and preventive health solutions.
                My focus is on building long-term wellness plans that help patients
                live healthier, happier lives.
            </p>
            <p>
                From routine checkups to chronic disease management, I combine
                modern medical practices with a compassionate approach
                to ensure every patient feels heard and cared for.
            </p>

            <!-- Highlighted Stats -->
            <div class="stats" style="display:flex;gap:20px;margin-top:20px;flex-wrap:wrap;">
                <div class="stat" style="flex:1;min-width:120px;text-align:center;">
                <h3 style="font-size:1.5rem;color:black;">10+</h3>
                <p>Years Experience</p>
                </div>
                <div class="stat" style="flex:1;min-width:120px;text-align:center;">
                <h3 style="font-size:1.5rem;color:black;">5k+</h3>
                <p>Patients Treated</p>
                </div>
                <div class="stat" style="flex:1;min-width:120px;text-align:center;">
                <h3 style="font-size:1.5rem;color:black;">100%</h3>
                <p>Patient Care</p>
                </div>
            </div>
            </div>
        </div>
    </section>

    <!-- SERVICES -->
    <section id="services" class="section reveal">
    <div class="section-header" style="text-align:center;margin-bottom:40px;">
        <h2>Start Feeling Your Best</h2>
        <p class="sub">Explore Our Wellness & Medical Services</p>
        <a href="#appointment" class="btn" style="margin-top:12px;">Make An Appointment</a>
    </div>

    <div class="grid">
        <!-- Service 1 -->
        <article class="card service tilt">
        <span class="shine"></span>
        <img src="{{ asset('angioplasty.jpg') }}" alt="Angioplasty Service" />
        <h3>Angioplasty</h3>
        <p>Minimally invasive treatment to restore blood flow.</p>
        <div class="meta">25+ Doctors</div>
        </article>

        <!-- Service 2 -->
        <article class="card service tilt">
        <span class="shine"></span>
        <img src="{{ asset('cardiology.jpg') }}" alt="Cardiology Service" />
        <h3>Cardiology</h3>
        <p>Comprehensive heart care for all ages.</p>
        <div class="meta">25+ Doctors</div>
        </article>

        <!-- Service 3 -->
        <article class="card service tilt">
        <span class="shine"></span>
        <img src="{{ asset('dental.jpg') }}" alt="Dental Service" />
        <h3>Dental Care</h3>
        <p>Quality dental treatments with modern equipment.</p>
        <div class="meta">25+ Doctors</div>
        </article>

        <!-- Service 4 -->
        <article class="card service tilt">
        <span class="shine"></span>
        <img src="{{ asset('endocrinology.jpg') }}" alt="Endocrinology Service" />
        <h3>Endocrinology</h3>
        <p>Diagnosis and treatment of hormone-related conditions.</p>
        <div class="meta">25+ Doctors</div>
        </article>

        <!-- Service 5 -->
        <article class="card service tilt">
        <span class="shine"></span>
        <img src="{{ asset('eye-care.jpg') }}" alt="Eye Care Service" />
        <h3>Eye Care</h3>
        <p>From regular check-ups to advanced eye surgeries.</p>
        <div class="meta">25+ Doctors</div>
        </article>

        <!-- Service 6 -->
        <article class="card service tilt">
        <span class="shine"></span>
        <img src="{{ asset('neurology.jpg') }}" alt="Neurology Service" />
        <h3>Neurology</h3>
        <p>Specialized care for brain, nerves, and spine.</p>
        <div class="meta">25+ Doctors</div>
        </article>

        <!-- Service 7 -->
        <article class="card service tilt">
        <span class="shine"></span>
        <img src="{{ asset('orthopedics.jpg') }}" alt="Orthopedics Service" />
        <h3>Orthopedics</h3>
        <p>Advanced bone, joint, and muscle care.</p>
        <div class="meta">25+ Doctors</div>
        </article>

        <!-- Service 8 -->
        <article class="card service tilt">
        <span class="shine"></span>
        <img src="{{ asset('rmi.jpg') }}" alt="RMI Service" />
        <h3>RMI</h3>
        <p>Modern diagnostic imaging and medical analysis.</p>
        <div class="meta">25+ Doctors</div>
        </article>
    </div>
    </section>


    <!-- CONTACT / APPOINTMENT -->
    <section id="appointment" class="section contact reveal">
    <h2>Book an Appointment</h2>
    <div class="contact-grid" style="display:grid;grid-template-columns:1fr 1fr;gap:30px;align-items:start;">

        <!-- Doctor Availability -->
        <div class="card availability" style="background:var(--primary);color:#fff;position:relative;border-radius:20px;padding:30px;">
        <div style="position:absolute;right:-25px;top:-25px;width:70px;height:70px;border-radius:50%;background:var(--secondary);display:flex;align-items:center;justify-content:center;border:3px solid #fff;">
            <img src="{{ asset('clock.svg') }}" alt="clock" width="36" height="36" />
        </div>
        <h3 style="margin-bottom:16px;">Open Hours</h3>
        <ul style="list-style:none;padding:0;margin:0;">
            <li style="display:flex;justify-content:space-between;padding:6px 0;border-bottom:1px dashed rgba(255,255,255,0.3);">
            <span>Monday</span><strong>09:30 - 07:30</strong>
            </li>
            <li style="display:flex;justify-content:space-between;padding:6px 0;border-bottom:1px dashed rgba(255,255,255,0.3);">
            <span>Tuesday</span><strong>09:30 - 07:30</strong>
            </li>
            <li style="display:flex;justify-content:space-between;padding:6px 0;border-bottom:1px dashed rgba(255,255,255,0.3);">
            <span>Wednesday</span><strong>09:30 - 07:30</strong>
            </li>
            <li style="display:flex;justify-content:space-between;padding:6px 0;border-bottom:1px dashed rgba(255,255,255,0.3);">
            <span>Thursday</span><strong>09:30 - 07:30</strong>
            </li>
            <li style="display:flex;justify-content:space-between;padding:6px 0;border-bottom:1px dashed rgba(255,255,255,0.3);">
            <span>Friday</span><strong>09:30 - 07:30</strong>
            </li>
            <li style="display:flex;justify-content:space-between;padding:6px 0;">
            <span>Saturday</span><strong>09:30 - 07:30</strong>
            </li>
        </ul>
        <p class="text-white text-xs mt-4 italic opacity-80">
        * Hours may vary on government/public holidays.  
        Please call before visiting to confirm availability.
        </p>
        </div>

        <!-- Appointment Form -->
        <div class="card appointment-form">
        @if(session('status'))
            <div style="padding:10px;border:1px solid var(--border);border-radius:10px;background:#15b8a61a;margin-bottom:12px">{{ session('status') }}</div>
        @endif
        @if ($errors->any())
            <div style="padding:10px;border:1px solid var(--border);border-radius:10px;background:#7f1d1d22;margin-bottom:12px">
            <ul style="margin:0;padding-left:16px">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif

        <form method="POST" action="" class="contact-form">
            @csrf
            <div class="form-group">
                <label for="location">Select Location</label>
                <select id="location" name="location" required>
                    <option value="">-- Choose a location --</option>
                    <option value="dhaka" {{ old('location')=='dhaka' ? 'selected' : '' }}>Dhaka Clinic</option>
                    <option value="chittagong" {{ old('location')=='chittagong' ? 'selected' : '' }}>Chittagong Clinic</option>
                    <option value="sylhet" {{ old('location')=='sylhet' ? 'selected' : '' }}>Sylhet Clinic</option>
                </select>
            </div>
            <div class="form-group">
            <label for="name">Full Name</label>
            <input id="name" name="name" type="text" required value="{{ old('name') }}">
            </div>
            <div class="form-group">
            <label for="phone">Phone</label>
            <input id="phone" name="phone" type="text" required value="{{ old('phone') }}">
            </div>
            <div class="form-group">
            <label for="date">Preferred Date</label>
            <input id="date" name="date" type="date" required value="{{ old('date') }}">
            </div>
            <div class="form-group">
            <label for="message">Your Concern</label>
            <textarea id="message" name="message" rows="4" required>{{ old('message') }}</textarea>
            </div>
            <button class="btn" type="submit">Book Appointment</button>
        </form>
        </div>
    </div>
    </section>
@endsection

@push('scripts')
  <script>
    // Mobile menu toggle
    const toggle = document.querySelector('.menu-toggle');
    const menu = document.getElementById('menu');
    if (toggle && menu) {
      toggle.addEventListener('click', () => {
        const open = menu.classList.toggle('open');
        toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
      });
      menu.querySelectorAll('a').forEach(a => a.addEventListener('click', () => {
        menu.classList.remove('open'); toggle.setAttribute('aria-expanded','false');
      }));
    }

    // Active nav on scroll
    const sections = [...document.querySelectorAll('section[id]')];
    const menuLinks = [...document.querySelectorAll('#menu a')];
    const byId = id => document.querySelector('#menu a[href="#'+id+'"]');
    const obs = new IntersectionObserver(entries => {
      entries.forEach(e => {
        if (e.isIntersecting) {
          menuLinks.forEach(a=>a.classList.remove('active'));
          const link = byId(e.target.id); if(link) link.classList.add('active');
        }
      });
    },{rootMargin:"-40% 0px -50% 0px", threshold:0});
    sections.forEach(s=>obs.observe(s));

    // Scroll reveal
    const rev = new IntersectionObserver(es=>es.forEach(({target,isIntersecting})=>{
      if(isIntersecting){target.classList.add('in');rev.unobserve(target)}
    }),{threshold:.12});
    document.querySelectorAll('.reveal').forEach(n=>rev.observe(n));

    // Typewriter loop
    const roles = ['Laravel developer','Full-stack engineer','API & dashboard builder'];
    const el = document.getElementById('type');
    let i=0, j=roles[0].length, erasing=false;
    function type(){
      const t = roles[i];
      el.textContent = t.slice(0,j);
      if(!erasing && j===t.length){ setTimeout(()=>erasing=true, 1200); }
      else if(erasing && j===0){ erasing=false; i=(i+1)%roles.length; }
      j += erasing? -1 : 1;
      setTimeout(type, erasing? 40 : 70);
    }
    setTimeout(type, 900);

    // Tilt effect on mouse move
    document.querySelectorAll('.tilt').forEach(card=>{
      card.addEventListener('mousemove', e=>{
        const r = card.getBoundingClientRect();
        const x = e.clientX - r.left, y = e.clientY - r.top;
        const rx = ((y/r.height)-.5)*-6; // tilt X
        const ry = ((x/r.width)-.5)*6;   // tilt Y
        card.style.transform = `perspective(1000px) rotateX(${rx}deg) rotateY(${ry}deg)`;
      });
      card.addEventListener('mouseleave', ()=>{ card.style.transform = 'perspective(1000px) rotateX(0) rotateY(0)'; });
    });

    // Scroll progress
    const bar = document.getElementById('progress');
    function onScroll(){
      const s = document.documentElement.scrollTop || document.body.scrollTop;
      const h = document.documentElement.scrollHeight - document.documentElement.clientHeight;
      bar.style.width = (s/h*100) + '%';
    }
    document.addEventListener('scroll', onScroll, {passive:true});
    onScroll();
  </script>
@endpush