{{-- resources/frontend/blog.blade.php --}}
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>Doctor Blog - Dr. Yousuf Patwari</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link rel="stylesheet" href="{{ asset('css/app.css') }}">
  <style>
    body{background:#006D6F;color:#e5e7eb;margin:0;font-family:Inter,ui-sans-serif,system-ui,-apple-system,"Segoe UI",Roboto,Arial;}
    .wrap{max-width:1100px;margin:0 auto;padding:clamp(14px,2vw,22px);}
    header,footer{background:#0d1b2a;color:#fff;}
    .hero{text-align:center;padding:60px 20px;}
    .hero h1{font-size:clamp(28px,4vw,44px);margin:0 0 12px;}
    .hero p{color:#9ca3af;max-width:600px;margin:auto;}
    .grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:20px;margin-top:40px;}
    .post{background:#0b1220cc;border:1px solid #1f2937;border-radius:18px;overflow:hidden;box-shadow:0 10px 30px rgba(0,0,0,.35);transition:.3s;}
    .post:hover{transform:translateY(-6px);}
    .post img{width:100%;height:180px;object-fit:cover;}
    .post-content{padding:18px;}
    .post-content h3{margin:0 0 10px;font-size:1.2rem;color:#22d3ee;}
    .post-content p{margin:0 0 14px;color:#9ca3af;font-size:0.95rem;line-height:1.6;}
    .meta{font-size:0.8rem;color:#a78bfa;margin-bottom:10px;}
    .btn{display:inline-block;background:linear-gradient(90deg,#22d3ee,#34d399);color:#0b1220;padding:8px 16px;border-radius:999px;font-weight:600;text-decoration:none;}
    @media(max-width:640px){.hero{padding:40px 14px;}}
  </style>
</head>
<body>
  {{-- HEADER --}}
  <header style="position:sticky;top:0;z-index:1000;background:#0d1b2a;color:#fff;">
        <div class="wrap nav" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;padding:16px 0;">
            <div class="brand" style="font-weight:800;background:linear-gradient(90deg,#22d3ee,#a78bfa);-webkit-background-clip:text;background-clip:text;color:transparent;">
            Dr. Yousuf Patwari Blog
            </div>
            <nav class="menu" style="display:flex;gap:20px;flex-wrap:wrap;">
            <a href="{{ url('/') }}" style="color:#9ca3af;text-decoration:none;">Home</a>
            <a href="#articles" style="color:#9ca3af;text-decoration:none;">Articles</a>
            <a href="{{ url('/#appointment') }}" style="color:#9ca3af;text-decoration:none;">Contact</a>
            </nav>
        </div>
    </header>


  {{-- HERO --}}
  <section class="hero">
    <h1>Doctor’s Blog</h1>
    <p>Insights on health, wellness, and preventive medicine — written by Dr. Yousuf Patwari.</p>
  </section>

  {{-- BLOG POSTS --}}
  <main class="wrap" id="articles">
    <div class="grid">
      <!-- Blog Post 1 -->
      <article class="post">
        <img src="{{ asset('blog_img/presure.jpg') }}" alt="Healthy Living Tips">
        <div class="post-content">
          <div class="meta">Published on Sep 10, 2025</div>
          <h3>10 Tips for a Healthier Lifestyle</h3>
          <p>Discover simple daily habits that can improve your health, energy, and overall well-being.</p>
          <a href="#" class="btn">Read More</a>
        </div>
      </article>

      <!-- Blog Post 2 -->
      <article class="post">
        <img src="{{ asset('blog_img/heart.jpg') }}" alt="Heart Health">
        <div class="post-content">
          <div class="meta">Published on Aug 28, 2025</div>
          <h3>Understanding Heart Health</h3>
          <p>Learn about risk factors, symptoms, and preventive measures for maintaining a healthy heart.</p>
          <a href="#" class="btn">Read More</a>
        </div>
      </article>

      <!-- Blog Post 3 -->
      <article class="post">
        <img src="{{ asset('blog_img/nutrition.jpg') }}" alt="Nutrition Guide">
        <div class="post-content">
          <div class="meta">Published on Aug 15, 2025</div>
          <h3>The Importance of Balanced Nutrition</h3>
          <p>Explore how a balanced diet supports immunity, recovery, and long-term wellness.</p>
          <a href="#" class="btn">Read More</a>
        </div>
      </article>
    </div>
    <div class="grid">
      <!-- Blog Post 1 -->
      <article class="post">
        <img src="{{ asset('blog_img/diabets.jpg') }}" alt="Healthy Living Tips">
        <div class="post-content">
          <div class="meta">Published on Sep 10, 2025</div>
          <h3>10 Tips for a Healthier Lifestyle</h3>
          <p>Discover simple daily habits that can improve your health, energy, and overall well-being.</p>
          <a href="#" class="btn">Read More</a>
        </div>
      </article>

      <!-- Blog Post 2 -->
      <article class="post">
        <img src="{{ asset('blog_img/child.jpg') }}" alt="Heart Health">
        <div class="post-content">
          <div class="meta">Published on Aug 28, 2025</div>
          <h3>Understanding Heart Health</h3>
          <p>Learn about risk factors, symptoms, and preventive measures for maintaining a healthy heart.</p>
          <a href="#" class="btn">Read More</a>
        </div>
      </article>

      <!-- Blog Post 3 -->
      <article class="post">
        <img src="{{ asset('blog_img/sleep.jpg') }}" alt="Nutrition Guide">
        <div class="post-content">
          <div class="meta">Published on Aug 15, 2025</div>
          <h3>The Importance of Balanced Nutrition</h3>
          <p>Explore how a balanced diet supports immunity, recovery, and long-term wellness.</p>
          <a href="#" class="btn">Read More</a>
        </div>
      </article>
    </div>
  </main>

  {{-- FOOTER --}}
  @include('frontend.footer')

</body>
</html>
