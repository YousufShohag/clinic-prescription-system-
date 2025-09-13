{{-- resources/views/portfolio.blade.php --}}
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>Yousuf Patwari Shohag</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="color-scheme" content="dark light" />
  <style>
    :root{
      --bg-1:#0f172a; /* slate-900 */
      --bg-2:#020617; /* slate-950 */
      --ink:#e5e7eb;  /* slate-200 */
      --muted:#9ca3af;/* slate-400 */
      --card:#0b1220cc; /* glass */
      --border:#1f2937; /* slate-800 */
      --brand:#22d3ee; /* cyan-400 */
      --brand-2:#a78bfa;/* violet-400 */
      --accent:#34d399;/* emerald-400 */
      --shadow:0 10px 30px rgba(0,0,0,.35);
      --radius:18px;
    }
    *{box-sizing:border-box}
    html{scroll-behavior:smooth}
    body{
      margin:0;
      font-family:Inter,ui-sans-serif,system-ui,-apple-system,"Segoe UI",Roboto,Arial;
      color:var(--ink);
      background-color:#006D6F;
      min-height:100svh; overflow-x:hidden
    }

    /* Animated gradient glow backdrop */
    .glow{position:fixed;inset:auto -20% -20% -20%;height:60vh;pointer-events:none;filter:blur(60px);opacity:.6;z-index:-1;background:conic-gradient(from 0deg, var(--brand),var(--brand-2),var(--accent),var(--brand));animation:spin 14s linear infinite}
    @keyframes spin{to{transform:rotate(1turn)}}

    .wrap{max-width:1100px;margin:0 auto;padding:clamp(14px,2vw,22px)}
    img{max-width:100%;height:auto;display:block}

    /* Header */
    header{position:sticky;top:0;backdrop-filter:saturate(140%) blur(10px);-webkit-backdrop-filter:saturate(140%) blur(10px);border-bottom:1px solid var(--border);z-index:50;background:linear-gradient( to bottom, rgba(2,6,23,.8), rgba(2,6,23,.35) );}
    .nav{display:flex;align-items:center;justify-content:space-between;gap:16px;position:relative}
    .brand{font-weight:800;letter-spacing:.2px;background:linear-gradient(90deg,var(--brand),var(--brand-2));-webkit-background-clip:text;background-clip:text;color:transparent}
    .menu{
        display:flex;
        gap:14px;
        justify-content:center; /* centers links */
        gap:20px;
        width:100%;
    }

    .menu a{color:var(--muted);text-decoration:none;padding:10px 12px;border-radius:999px;transition:all .25s}
    .menu a:hover,.menu a.active{color:var(--ink);background:#ffffff14}
    .btn{display:inline-flex;align-items:center;gap:10px;padding:10px 14px;border-radius:999px;text-decoration:none;color:#0b1220;background:linear-gradient(90deg,var(--brand),var(--accent));font-weight:600;box-shadow:var(--shadow);}

    /* Mobile nav */
    .menu-toggle{
      display:none;width:44px;height:40px;border:1px solid var(--border);
      border-radius:12px;background:#0b122040;cursor:pointer;
      align-items:center;justify-content:center;gap:4px
    }
    .menu-toggle span{display:block;width:18px;height:2px;background:var(--ink);border-radius:2px}

    @media (max-width:820px){
      .menu-toggle{display:inline-flex}
      .menu{
        position:absolute; right:16px; top:66px;
        display:none; flex-direction:column; gap:10px;
        background:var(--card); border:1px solid var(--border);
        padding:12px; border-radius:14px; box-shadow:var(--shadow);
        min-width:200px;
      }
      .menu.open{display:flex}
      .menu a{display:block}
    }

    /* Hero */
    .hero{display:grid;grid-template-columns:1.2fr .8fr;gap:24px;align-items:center;margin-top:22px}
    @media (max-width:960px){ .hero{grid-template-columns:1fr} }
    .card{background:var(--card);border:1px solid var(--border);border-radius:var(--radius);padding:22px;box-shadow:var(--shadow)}
    .headline{font-size:clamp(26px,4vw,44px);line-height:1.12;margin:0 0 8px 0}
    .accent-gradient{background:linear-gradient(90deg,var(--brand),var(--brand-2));-webkit-background-clip:text;background-clip:text;color:transparent}
    .sub{color:var(--muted);margin:8px 0 16px}

    /* Typewriter caret */
    .type{border-right:3px solid var(--brand);white-space:nowrap;overflow:hidden}

    /* Avatar scales on phones */
    .avatar{
      width:min(240px, 60vw);
      aspect-ratio:1/1;border-radius:20px;object-fit:cover;display:inline-block;
      box-shadow:var(--shadow);border:1px solid #ffffff22
    }

    /* Tech pills */
    .pills{display:flex;flex-wrap:wrap;gap:8px}
    .pill{display:inline-flex;align-items:center;gap:6px;border:1px solid var(--border);border-radius:999px;padding:8px 12px;font-size:12px;background:#0b122040;white-space:nowrap}

    /* Sections and projects */
    .section{margin-top:34px}
    .section h2{margin:0 0 12px}
    .grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:16px}
    .project{position:relative;overflow:hidden}
    .project img{width:100%;height:180px;object-fit:cover;border-radius:14px}
    @media (max-width:640px){.project img{height:140px}}
    .project h3{margin:12px 0 6px}
    .project p{color:var(--muted);margin:0 0 10px}
    .links{display:flex;flex-wrap:wrap;gap:8px;align-items:center}
    .project .links a{color:var(--ink)}

    /* Hover tilt + shine */
    .tilt{transform-style:preserve-3d;transform:perspective(1000px) rotateX(0) rotateY(0);transition:transform .15s ease}
    .tilt:hover{transform:perspective(1000px) rotateX(1.2deg) rotateY(-1.2deg)}
    .shine{position:absolute;inset:0;background:linear-gradient(120deg,transparent 30%, rgba(255,255,255,.18) 50%, transparent 70%);transform:translateX(-120%);transition:transform .6s ease}
    .project:hover .shine{transform:translateX(120%)}

    /* About & timeline */
    .about p{margin:0 0 10px}
    .timeline{display:grid;gap:12px;margin-top:8px}
    .t{display:grid;grid-template-columns:120px 1fr;gap:12px}
    @media (max-width:640px){.t{grid-template-columns:1fr}}
    .t time{color:var(--muted)}

    /* Contact (animated) */
    .contact .card a{color:var(--brand)}
    .contact-form{display:grid;gap:16px}
    .contact-form input,.contact-form textarea{
      width:100%;padding:12px;border-radius:12px;border:1px solid var(--border);
      background:#0b122040;color:var(--ink);transition:border-color .3s,box-shadow .3s
    }
    .contact-form input:focus,.contact-form textarea:focus{outline:none;border-color:var(--brand);box-shadow:0 0 0 3px #22d3ee44}
    .contact-form .form-group{opacity:0;transform:translateY(20px);animation:fadeUp .6s forwards}
    .contact-form .form-group:nth-child(1){animation-delay:.1s}
    .contact-form .form-group:nth-child(2){animation-delay:.25s}
    .contact-form .form-group:nth-child(3){animation-delay:.4s}
    .contact-form button{animation:fadeUp .6s .55s forwards;opacity:0;transform:translateY(20px)}
    @keyframes fadeUp{to{opacity:1;transform:none}}

    /* Scroll reveal */
    .reveal{opacity:0;transform:translateY(16px);filter:saturate(.6);}
    .reveal.in{opacity:1;transform:none;filter:none;transition: all .7s cubic-bezier(.2,.8,.2,1)}

    /* Floating orbs */
    .orb{position:absolute;width:24px;height:24px;border-radius:999px;background:radial-gradient(circle at 30% 30%, #fff, transparent 60%), radial-gradient(circle at 70% 70%, #fff2, transparent 60%), linear-gradient(90deg,var(--brand-2),var(--brand));opacity:.24;box-shadow:0 0 50px rgba(255,255,255,.15);animation:float 14s ease-in-out infinite;pointer-events:none}
    .o1{top:12%;left:6%;animation-delay:0s}
    .o2{top:70%;left:12%;animation-delay:2s}
    .o3{top:26%;right:10%;animation-delay:4s}
    @keyframes float{0%,100%{transform:translateY(0)}50%{transform:translateY(-20px)}}

    /* Progress bar */
    .progress{position:fixed;top:0;left:0;height:3px;background:linear-gradient(90deg,var(--brand),var(--brand-2));width:0%;z-index:60}

    footer{color:var(--muted);font-size:clamp(12px,1.8vw,14px);text-align:center;padding:34px 0}

    /* Reduced motion */
    @media (prefers-reduced-motion: reduce){
      *{animation:none!important;transition:none!important}
      html{scroll-behavior:auto}
    }
  </style>
</head>
<body>
  <!-- scroll progress -->
  <div class="progress" id="progress"></div>

  <!-- global animated glow -->
  <div class="glow" aria-hidden="true"></div>
  <div class="orb o1" aria-hidden="true"></div>
  <div class="orb o2" aria-hidden="true"></div>
  <div class="orb o3" aria-hidden="true"></div>

  <header class="align-items-center">
    <div class="wrap nav">
      {{-- <a href=""><div class="brand">Yousuf Patwari Shohag</div></a> --}}

      <!-- Mobile menu button -->
      <button class="menu-toggle" aria-controls="menu" aria-expanded="false" aria-label="Toggle navigation">
        <span></span><span></span><span></span>
      </button>

      <nav class="menu" id="menu">
        <a href="#top">Home</a>
        <a href="#about">About</a>
        <a href="#services">Services</a>
        <a href="#contact">Blog</a>
        <a class="btn" href="#appointment">Make Appointment</a>
      </nav>
    </div>
  </header>

  <main class="wrap">
    <!-- HERO -->
    <section class="hero section reveal in" id="top">
        <!-- Left text card -->
        <div class="card">
            <h1 class="headline">
            Welcome to <span class="accent-gradient">Dr. Yousuf Patwari’s Clinic</span>
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
            <img class="avatar" src="{{ asset('doctor.png') }}" alt="Doctor profile photo"/>
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
            <img src="{{ asset('doctor.png') }}" alt="Dr. Yousuf Patwari" style="width:100%;max-width:350px;border-radius:20px;box-shadow:0 8px 20px rgba(0,0,0,0.1);" />
            </div>

            <!-- Doctor Bio -->
            <div class="about-content">
            <h2>About Dr. Yousuf Patwari</h2>
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
                <h3 style="font-size:1.5rem;color:#2b7a78;">10+</h3>
                <p>Years Experience</p>
                </div>
                <div class="stat" style="flex:1;min-width:120px;text-align:center;">
                <h3 style="font-size:1.5rem;color:#2b7a78;">5k+</h3>
                <p>Patients Treated</p>
                </div>
                <div class="stat" style="flex:1;min-width:120px;text-align:center;">
                <h3 style="font-size:1.5rem;color:#2b7a78;">100%</h3>
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
            <label for="name">Full Name</label>
            <input id="name" name="name" type="text" required value="{{ old('name') }}">
            </div>
            <div class="form-group">
            <label for="email">Email</label>
            <input id="email" name="email" type="email" required value="{{ old('email') }}">
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


    <!-- FOOTER -->
    <footer style="background:#0d1b2a;color:#fff;padding:40px 20px;margin-top:60px;border-radius:30px;">
        <div class="footer-container" style="max-width:1200px;margin:auto;display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:30px;">
            
            <!-- Clinic Info -->
            <div class="footer-about">
            <h3 style="margin-bottom:12px;">Dr. Yousuf Patwari Clinic</h3>
            <p style="line-height:1.6;">
                Providing compassionate healthcare and wellness services 
                with a focus on family medicine, preventive care, and patient trust.
            </p>
            </div>

            <!-- Quick Links -->
            <div class="footer-links">
            <h4 style="margin-bottom:12px;">Quick Links</h4>
            <ul style="list-style:none;padding:0;margin:0;line-height:1.8;">
                <li><a href="#top" style="color:#fff;text-decoration:none;">Home</a></li>
                <li><a href="#about" style="color:#fff;text-decoration:none;">About</a></li>
                <li><a href="#services" style="color:#fff;text-decoration:none;">Services</a></li>
                <li><a href="#contact" style="color:#fff;text-decoration:none;">Appointment</a></li>
            </ul>
            </div>

            <!-- Contact Info -->
            <div class="footer-contact">
            <h4 style="margin-bottom:12px;">Contact</h4>
            <p>📍 123 Health Street, Dhaka, Bangladesh</p>
            <p>📞 +880 123 456 789</p>
            <p>✉️ <a href="mailto:yousufshohag90@gmail.com" style="color:#fff;">yousufshohag90@gmail.com</a></p>
            </div>

            <!-- Social -->
            <div class="footer-social">
            <h4 style="margin-bottom:12px;">Follow Us</h4>
            <div style="display:flex;gap:12px;">
                <a href="#" style="color:#fff;font-size:20px;">🌐</a>
                <a href="#" style="color:#fff;font-size:20px;">📘</a>
                <a href="#" style="color:#fff;font-size:20px;">🐦</a>
                <a href="#" style="color:#fff;font-size:20px;">📸</a>
            </div>
            </div>

        </div>

        <!-- Bottom Bar -->
        <div style="text-align:center;margin-top:30px;padding-top:20px;border-top:1px solid rgba(255,255,255,0.2);">
            © {{ date('Y') }} Dr. Yousuf Patwari — All rights reserved.
        </div>
    </footer>

  </main>

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
</body>
</html>
