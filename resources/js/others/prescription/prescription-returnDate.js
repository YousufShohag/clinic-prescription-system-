// resources/js/return-date-suggestions.js
(function () {
  const SELECTOR = 'input[name="return_date"]';

  function pad(n){ return String(n).padStart(2,'0'); }
  function fmtYMD(d){ return d.getFullYear() + '-' + pad(d.getMonth()+1) + '-' + pad(d.getDate()); }
  function parseYMD(s){
    const m = /^(\d{4})-(\d{2})-(\d{2})$/.exec(String(s||'').trim());
    if (!m) return null;
    return new Date(+m[1], +m[2]-1, +m[3]);
  }
  function addDays(d, n){ const x = new Date(d.getFullYear(), d.getMonth(), d.getDate()); x.setDate(x.getDate()+n); return x; }
  function addMonths(d, n){ const x = new Date(d.getFullYear(), d.getMonth(), d.getDate()); x.setMonth(x.getMonth()+n); return x; }

  const PICKS = [
    { label: 'Today',    calc: b => addDays(b, 0) },
    { label: 'Tomorrow', calc: b => addDays(b, 1) },
    { label: '3 days',   calc: b => addDays(b, 3) },
    { label: '5 days',   calc: b => addDays(b, 5) },
    { label: '7 days',   calc: b => addDays(b, 7) },
    { label: '10 days',  calc: b => addDays(b, 10) },
    { label: '14 days',  calc: b => addDays(b, 14) },
    { label: '3 weeks',  calc: b => addDays(b, 21) },
    { label: '1 month',  calc: b => addMonths(b, 1) },
    { label: '2 months', calc: b => addMonths(b, 2) },
  ];

  function buildUI(input){
    const parent = input.closest('div') || input.parentElement;

    let labelEl = parent.querySelector('label');
    const header = document.createElement('div');
    header.className = 'flex items-center justify-between mb-1';

    if (labelEl) {
      header.appendChild(labelEl);
    } else {
      const fallback = document.createElement('label');
      fallback.className = 'block text-sm font-medium text-gray-700';
      fallback.textContent = 'Return Date';
      header.appendChild(fallback);
    }

    const controls = document.createElement('div');
    controls.className = 'flex items-center gap-2 relative';

    const wrap = document.createElement('div');
    wrap.className = 'relative';

    const btn = document.createElement('button');
    btn.type = 'button';
    btn.className = 'px-2 py-1 text-xs rounded bg-gray-100 text-gray-700';
    btn.textContent = 'Suggestions ▾';

    // ⬇️ If you want the menu left aligned, use 'left-0'. For right aligned, use 'right-0'.
    const menu = document.createElement('div');
    menu.className = 'hidden absolute right-0 z-30 mt-1 w-64 bg-white border rounded shadow p-2 max-h-64 overflow-auto';
    menu.innerHTML = '<div class="text-xs text-gray-500 px-1 mb-1">Click to set</div><div class="flex flex-wrap gap-1"></div>';

    const listWrap = menu.querySelector('.flex');

    function setDateFromPick(calcFn){
      const base = parseYMD(input.value) || new Date();
      const d = calcFn(base);
      input.value = fmtYMD(d);
      input.dispatchEvent(new Event('input',  { bubbles: true }));
      input.dispatchEvent(new Event('change', { bubbles: true }));
    }

    PICKS.forEach(p => {
      const chip = document.createElement('button');
      chip.type = 'button';
      chip.className = 'px-2 py-1 text-xs border rounded hover:bg-gray-50';
      chip.textContent = p.label;
      chip.addEventListener('click', () => { setDateFromPick(p.calc); hide(); });
      listWrap.appendChild(chip);
    });

    const clearBtn = document.createElement('button');
    clearBtn.type = 'button';
    clearBtn.className = 'px-2 py-1 text-xs border rounded hover:bg-gray-50';
    clearBtn.textContent = 'Clear';
    clearBtn.addEventListener('click', () => {
      input.value = '';
      input.dispatchEvent(new Event('input',  { bubbles: true }));
      input.dispatchEvent(new Event('change', { bubbles: true }));
      hide();
    });
    listWrap.appendChild(clearBtn);

    const toggle = () => menu.classList.toggle('hidden');
    const hide   = () => menu.classList.add('hidden');
    function outside(e){
      if (menu.classList.contains('hidden')) return;
      if (!menu.contains(e.target) && !btn.contains(e.target)) hide();
    }

    btn.addEventListener('click', toggle);
    document.addEventListener('click', outside);
    document.addEventListener('keydown', e => { if (e.key === 'Escape') hide(); });

    wrap.appendChild(btn);
    wrap.appendChild(menu);
    controls.appendChild(wrap);
    header.appendChild(controls);

    parent.insertBefore(header, input);
  }

  function init(){
    const input = document.querySelector(SELECTOR);
    if (!input) return;
    if (input.dataset.rdSuggAttached === '1') return;
    input.dataset.rdSuggAttached = '1';
    buildUI(input);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
