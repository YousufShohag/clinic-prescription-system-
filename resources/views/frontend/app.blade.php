{{-- resources/frontend/layouts/app.blade.php --}}
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
      --muted:#fff;/* slate-400 */
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
    .contact-form button{
        animation:fadeUp .6s .55s forwards;opacity:0;transform:translateY(20px);
    }
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

    @media (max-width: 768px) {
    /* About collapses into 1 column */
    .about-container {
      grid-template-columns: 1fr !important;
      gap: 24px !important;
    }

    /* Appointment section stacks */
    .contact-grid {
      grid-template-columns: 1fr !important;
    }

    /* Hero spacing */
    .hero {
      gap: 34px;
    }

    /* Buttons and pills wrap nicely */
    .btn {
      width: 100%;
      justify-content: center;
    }
    .pill {
      flex: 1 1 auto;
      text-align: center;
    }
  }

  @media (max-width: 480px) {
    /* Avatar responsive */
    .avatar {
      width: 90vw !important;
      border-radius: 16px;
    }

    /* Footer links touch-friendly */
    .footer-links ul li {
      margin-bottom: 8px;
    }

    /* Form inputs full-width */
    .contact-form input,
    .contact-form textarea {
      font-size: 16px; /* avoids iOS zoom */
    }
  }

  /* Smooth dropdown transition */
  .menu {
    transition: all 0.3s ease;
  }

  .contact-form select,
  .contact-form input,
  .contact-form textarea {
    width: 100%;
    padding: 10px;
    border: 1px solid #1f2937;
    border-radius: 6px;
    background: #0b1220cc;
    color: #e5e7eb;
    font-size: 1rem;
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
        <a href="/">Home</a>
        <a href="#about">About</a>
        <a href="#services">Services</a>
        <a href="/blog">Blog</a>
        <a class="btn" href="#appointment">Make Appointment</a>
      </nav>
    </div>
  </header>

  {{-- MAIN CONTENT --}}
  <main class="wrap">
    @yield('content')
  </main>

  {{-- FOOTER --}}
  @include('frontend.footer')


  @stack('scripts')
</body>
</html>
