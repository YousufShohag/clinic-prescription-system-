{{-- resources/views/portfolio.blade.php --}}
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>Yousuf Patwari Shohag — Portfolio</title>
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
      background:
        radial-gradient(1200px 600px at 10% -10%,#1f2937,transparent),
        radial-gradient(1200px 600px at 90% 10%,#111827,transparent),
        linear-gradient(180deg,var(--bg-1),var(--bg-2));
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
    .menu{display:flex;gap:14px}
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

  <header>
    <div class="wrap nav">
      {{-- <a href=""><div class="brand">Yousuf Patwari Shohag</div></a> --}}

      <!-- Mobile menu button -->
      <button class="menu-toggle" aria-controls="menu" aria-expanded="false" aria-label="Toggle navigation">
        <span></span><span></span><span></span>
      </button>

      <nav class="menu" id="menu">
        <a href="#projects">Projects</a>
        <a href="#about">About</a>
        <a href="#contact">Contact</a>
        <a class="btn" href="/">{{ config('app.name') }}</a>
      </nav>
    </div>
  </header>

  <main class="wrap">
    <!-- HERO -->
    <section class="hero section reveal in" id="top">
      <div class="card">
        <h1 class="headline">Hi, I’m <span class="accent-gradient">Yousuf Patwari Shohag</span> — <span id="type" class="type">Laravel developer</span></h1>
        <p class="sub">I build clean, fast web apps with Laravel, Livewire, and Vue.</p>
        <div style="display:flex;gap:12px;flex-wrap:wrap">
          <a class="btn" href="#projects">View Projects</a>
          <a class="pill" href="mailto:yousufshohag90@gmail.com">yousufshohag90@gmail.com</a>
        </div>
        <div class="pills" style="margin-top:14px">
          <span class="pill">Laravel</span><span class="pill">Livewire</span><span class="pill">Vue</span>
          <span class="pill">MySQL</span><span class="pill">Tailwind</span>
        </div>
      </div>
      <div class="card tilt" style="text-align:center">
        <img class="avatar" src="{{ asset('image.png') }}" alt="Avatar"/>
        <p class="sub" style="margin-top:10px">Available for freelance/remote work</p>
      </div>
    </section>

    <!-- PROJECTS -->
    <section id="projects" class="section reveal">
      <h2>Projects</h2>
      <div class="grid">
        <!-- Project 1 -->
        <article class="card project tilt">
          <span class="shine"></span>
          <img src="{{ asset('Screenshot_2.png') }}" alt="Payroll Starter screenshot" />
          <h3>Payroll Starter</h3>
          <p>Payroll/HR core built with Laravel + Livewire.</p>
          <div class="links">
            <a href="{{ route('login') }}" target="_blank" rel="noopener">Live</a> ·
            <a href="https://github.com/YousufShohag" target="_blank" rel="noopener">GitHub</a>
          </div>
        </article>

        <!-- Project 2 -->
        <article class="card project tilt">
          <span class="shine"></span>
          <img src="{{ asset('happydoctor.png') }}" alt="Doctor's House app screenshot" />
          <h3>Doctor's House</h3>
          <p>Simple CMS to manage patients and prescriptions.</p>
          <div class="links">
            <a href="{{ route('login') }}" target="_blank" rel="noopener">Live</a> ·
            <a href="https://github.com/YousufShohag" target="_blank" rel="noopener">GitHub</a>
          </div>
        </article>

        <!-- Project 3 -->
        <article class="card project tilt">
          <span class="shine"></span>
          <img src="{{ asset('Screenshot_3.png') }}" alt="Task Manager screenshot" />
          <h3>Pharmecy Manager</h3>
          <p>Manage -Medicine,Customer, and do billing easily.</p>
          <div class="links">
            <a href="{{ route('login') }}" target="_blank" rel="noopener">Live</a> ·
            <a href="https://github.com/YousufShohag" target="_blank" rel="noopener">GitHub</a>
          </div>
        </article>
      </div>
    </section>

    <!-- ABOUT -->
    <section id="about" class="section about reveal">
      <h2>About</h2>
      <div class="card">
        <p>I’m a full-stack developer focused on Laravel. I care about clean code, DX, and performance.</p>
        <div class="timeline">
          <div class="t"><time>2025</time><div>Built <em>Doctor's House</em> for small clinics with fast e-prescriptions.</div></div>
          <div class="t"><time>2024</time><div>Launched payroll starter using Livewire + Alpine for SMBs.</div></div>
          <div class="t"><time>2023</time><div>Freelanced dashboards, APIs, and admin panels for local businesses.</div></div>
        </div>
      </div>
    </section>

    <!-- CONTACT (animated) -->
    <section id="contact" class="section contact reveal">
      <h2>Contact</h2>
      <div class="card">
        @if(session('status'))
          <div style="padding:10px;border:1px solid var(--border);border-radius:10px;background:#15b8a61a;margin-bottom:12px">{{ session('status') }}</div>
        @endif
        @if ($errors->any())
          <div style="padding:10px;border:1px solid var(--border);border-radius:10px;background:#7f1d1d22;margin-bottom:12px">
            <ul style="margin:0;padding-left:16px">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
          </div>
        @endif

        {{-- <form method="POST" action="{{ route('contact.send') }}" class="contact-form">
          @csrf
          <div class="form-group">
            <label for="name">Name</label>
            <input id="name" name="name" type="text" required value="{{ old('name') }}">
          </div>
          <div class="form-group">
            <label for="email">Email</label>
            <input id="email" name="email" type="email" required value="{{ old('email') }}">
          </div>
          <div class="form-group">
            <label for="message">Message</label>
            <textarea id="message" name="message" rows="5" required>{{ old('message') }}</textarea>
          </div>
          <button class="btn" type="submit">Send message</button>
        </form> --}}

        <p style="margin-top:12px">Or email <a href="mailto:yousufshohag90@gmail.com">yousufshohag90@gmail.com</a></p>
      </div>
    </section>

    <footer>© {{ date('Y') }} Yousuf Patwari Shohag — All rights reserved.</footer>
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
