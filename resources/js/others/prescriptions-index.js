
//  Tiny JS for preview toggles 
 document.addEventListener('click', function(e) {
      var t = e.target.closest('[data-toggle]');
      if (!t) return;
      var id = t.getAttribute('data-toggle');
      var row = document.getElementById(id);
      if (row) row.classList.toggle('hidden');
    }, false);