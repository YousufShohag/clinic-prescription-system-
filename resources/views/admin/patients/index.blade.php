{{-- resources/views/patients/index.blade.php --}}
<x-app-layout>
  {{-- ===== AG Grid (Community) CDN ===== --}}
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/ag-grid-community/styles/ag-grid.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/ag-grid-community/styles/ag-theme-quartz.css" />
  <script src="https://cdn.jsdelivr.net/npm/ag-grid-community/dist/ag-grid-community.min.js"></script>

  {{-- ===== Theme tweaks (Quartz) ===== --}}
  <style>
    .ag-theme-quartz.custom {
      --ag-font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, "Helvetica Neue", Arial, "Noto Sans",
                        "Apple Color Emoji","Segoe UI Emoji","Segoe UI Symbol","Noto Color Emoji";
      --ag-font-size: 13px;
      --ag-grid-size: 4px;
      --ag-border-radius: 8px;

      --ag-background-color: #ffffff;
      --ag-foreground-color: #111827;        /* gray-900 */
      --ag-border-color: #e5e7eb;            /* gray-200 */
      --ag-header-background-color: #f3f4f6; /* gray-100 */
      --ag-header-foreground-color: #374151; /* gray-700 */
      --ag-odd-row-background-color: #fafafa;/* gray-50 */
      --ag-row-hover-color: #f5f5f5;         /* gray-100 */
      --ag-selected-row-background-color: #e0f2fe; /* sky-100 */
      --ag-checkbox-checked-color: #2563eb;  /* blue-600 */
      --ag-range-selection-border-color: #60a5fa; /* blue-400 */

      --ag-header-column-separator-display: block;
      --ag-header-column-separator-color: #e5e7eb;
      --ag-header-column-separator-height: 60%;
    }
    .ag-theme-quartz.custom .ag-root-wrapper { border-radius: .5rem; }
    .ag-theme-quartz.custom .ag-header-cell-label {
      font-weight: 600; text-transform: uppercase; font-size: 11px; letter-spacing: .02em;
    }
    .ag-theme-quartz.custom .ag-row.tint-overdue  .ag-cell { background-color: #FEF2F2 !important; } /* red-50 */
    .ag-theme-quartz.custom .ag-row.tint-today    .ag-cell { background-color: #FFFBEB !important; } /* amber-50 */
    .ag-theme-quartz.custom .ag-row.tint-upcoming .ag-cell { background-color: #F0FDF4 !important; } /* green-50 */
    .ag-theme-quartz.custom.striped-off { --ag-odd-row-background-color: transparent; }

    /* Tiny dropdown menu for column chooser */
    .menu {
      position: relative;
    }
    .menu > button {
      border: 1px solid #e5e7eb; border-radius: .375rem; padding: .5rem .75rem; background: #fff;
    }
    .menu .menu-panel {
      position: absolute; right: 0; top: calc(100% + .25rem);
      width: 220px; max-height: 260px; overflow: auto;
      background: #fff; border: 1px solid #e5e7eb; border-radius: .5rem; padding: .5rem;
      box-shadow: 0 10px 20px rgba(0,0,0,.08);
      display: none; z-index: 30;
    }
    .menu.open .menu-panel { display: block; }
    .menu .menu-row { display: flex; align-items: center; gap: .5rem; padding: .25rem .25rem; }
  </style>

  <div class="container mx-auto py-8 px-4 sm:px-6 lg:px-8">
    {{-- Header --}}
    <div class="flex justify-between items-center mb-6">
      <h2 class="text-2xl font-semibold">Patients</h2>
      <a href="{{ route('patients.create') }}"
         class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">+ Add Patient</a>
    </div>

    {{-- Toolbar --}}
    <div class="bg-white rounded shadow mb-3 px-3 py-2 flex flex-wrap items-center gap-2">
      <div class="relative">
        <input id="quickFilter" type="text" placeholder="Search patients…"
               class="pl-9 pr-3 py-2 border rounded w-72 focus:outline-none focus:ring-2 focus:ring-blue-500/40" />
        <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" viewBox="0 0 24 24" fill="none">
          <path stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                d="M21 21l-4.35-4.35m1.85-5.15a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
      </div>

      <select id="pageSize" class="px-2 py-2 border rounded focus:outline-none">
        <option value="10">10 / page</option>
        <option value="25" selected>25 / page</option>
        <option value="50">50 / page</option>
        <option value="100">100 / page</option>
      </select>

      <button id="btnExport" class="px-3 py-2 border rounded hover:bg-gray-50">Export CSV</button>
      <button id="btnExportSel" class="px-3 py-2 border rounded hover:bg-gray-50" disabled>Export Selected</button>
      <button id="btnDeleteSel" class="px-3 py-2 border rounded text-red-700 hover:bg-red-50" disabled>Delete Selected</button>

      <div class="menu" id="colMenu">
        <button type="button" id="btnCols">Columns ▾</button>
        <div class="menu-panel" id="colPanel"></div>
      </div>

      <button id="btnFit"  class="px-3 py-2 border rounded hover:bg-gray-50">Fit Columns</button>
      <button id="btnAuto" class="px-3 py-2 border rounded hover:bg-gray-50">Auto-size</button>

      <div class="ml-2 flex gap-2">
        <button id="btnSaveView" class="px-3 py-2 border rounded hover:bg-gray-50">Save View</button>
        <button id="btnLoadView" class="px-3 py-2 border rounded hover:bg-gray-50">Load View</button>
        <button id="btnResetView" class="px-3 py-2 border rounded hover:bg-gray-50">Reset View</button>
      </div>

      <span class="ml-auto text-sm text-gray-600">
        Selected: <strong id="selCount">0</strong>
      </span>

      <label class="inline-flex items-center gap-2 ml-3">
        <input id="dense" type="checkbox" class="rounded border-gray-300">
        <span class="text-sm text-gray-700">Dense</span>
      </label>

      <label class="inline-flex items-center gap-2">
        <input id="striped" type="checkbox" class="rounded border-gray-300" checked>
        <span class="text-sm text-gray-700">Striped rows</span>
      </label>
    </div>

    {{-- Grid --}}
    <div class="overflow-hidden bg-white rounded shadow">
      <div id="patientsGrid" class="ag-theme-quartz custom" style="height: 680px;"></div>
    </div>
  </div>

  {{-- SweetAlert2 (load only if not in layout) --}}
  <script>
    if (typeof window.Swal === 'undefined') {
      const s = document.createElement('script');
      s.src = 'https://cdn.jsdelivr.net/npm/sweetalert2@11';
      document.head.appendChild(s);
    }
  </script>

  {{-- ===== PHP → JS: Build safe row data ===== --}}
  @php
    $items = $patients instanceof \Illuminate\Pagination\AbstractPaginator
      ? $patients->getCollection()
      : collect($patients);

    $gridRows = $items->map(function ($p) {
        if (is_int($p) || is_string($p)) {
            return ['id' => (int) $p];
        }

        $id           = $p->id            ?? null;
        $name         = $p->name          ?? null;
        $age          = $p->age           ?? null;
        $sex          = $p->sex           ?? null;
        $blood        = $p->blood_group   ?? null;
        $guardian     = $p->guardian_name ?? null;
        $phone        = $p->phone         ?? null;
        $email        = $p->email         ?? null;
        $status       = $p->status        ?? '-';
        $address      = $p->address       ?? null;
        $notes        = $p->notes         ?? null;

        $doctorName   = isset($p->doctor) && is_object($p->doctor) ? ($p->doctor->name ?? null) : null;

        $imagesRaw = $p->images ?? [];
        if (is_string($imagesRaw)) {
            $decoded = json_decode($imagesRaw, true);
            $images  = is_array($decoded) ? $decoded : [];
        } else {
            $images  = is_array($imagesRaw) ? $imagesRaw : [];
        }
        $first  = count($images) ? $images[0] : null;
        $avatar = $first ? (preg_match('/^(https?:\/\/|\/)/i', $first) ? $first : '/storage/' . ltrim($first, '/')) : null;

        $nextReturn = $p->next_return_date ?? null;
        $returnDisp = $nextReturn ? \Carbon\Carbon::parse($nextReturn)->format('d/m/Y') : '-';
        $created    = isset($p->created_at) ? \Carbon\Carbon::parse($p->created_at)->timezone(config('app.timezone','UTC'))->format('d/m/Y H:i') : null;
        $updated    = isset($p->updated_at) ? \Carbon\Carbon::parse($p->updated_at)->timezone(config('app.timezone','UTC'))->format('d/m/Y H:i') : null;

        $rxCount = $p->prescriptions_count ?? 0;

        $rxUrl     = $id ? route('patients.prescriptions', ['patient' => $id]) : '#';
        $docsUrl   = $id ? route('patients.documents',     ['patient' => $id]) : '#';
        $editUrl   = $id ? route('patients.edit',          ['patient' => $id]) : '#';
        $showUrl   = $id ? route('patients.show',          ['patient' => $id]) : '#';
        $deleteUrl = $id ? route('patients.destroy',       ['patient' => $id]) : '#';

        return [
          'id'        => $id,
          'avatar'    => $avatar,
          'images'    => $images,

          'name'      => $name,
          'age'       => $age,
          'sex'       => $sex,
          'blood'     => $blood,
          'guardian'  => $guardian,
          'phone'     => $phone,
          'email'     => $email,
          'status'    => ucfirst($status ?? '-'),
          'address'   => $address,
          'notes'     => $notes,

          'return'    => $returnDisp,
          'rx_count'  => $rxCount,
          'doctor'    => $doctorName,

          'created'   => $created,
          'updated'   => $updated,

          'rx_url'    => $rxUrl,
          'docs_url'  => $docsUrl,
          'edit_url'  => $editUrl,
          'show_url'  => $showUrl,
          'delete_url'=> $deleteUrl,
        ];
    })->values();
  @endphp

  <script>
    /* ===================== Data & helpers ===================== */
    const ROWS = @json($gridRows, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    const CSRF = '{{ csrf_token() }}';
    const STORAGE_KEY = 'patientsGrid:view';

    const esc = s => (s==null?'':String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'));
    const nl2br = s => esc(s).replace(/\n/g,'<br>');

    function dueTone(dateStr){
      if(!dateStr || dateStr==='-') return { cls:'bg-gray-100 text-gray-800', text:'No Return' };
      const [d,m,y] = (dateStr||'').split(/[\/\-\.]/).map(Number);
      const dt = (y&&m&&d) ? new Date(y, m-1, d) : new Date(dateStr);
      if(isNaN(dt)) return { cls:'bg-gray-100 text-gray-800', text: dateStr };
      const today = new Date(); today.setHours(0,0,0,0);
      const cmp = new Date(dt.getFullYear(), dt.getMonth(), dt.getDate());
      if (cmp < today)  return { cls:'bg-red-100 text-red-800',   text:`Overdue ${dateStr}` };
      if (cmp.getTime() === today.getTime()) return { cls:'bg-amber-100 text-amber-800', text:`Due Today ${dateStr}` };
      return { cls:'bg-green-100 text-green-800', text:`Due ${dateStr}` };
    }

    function deletePatient(url){
      if(!url || url==='#') return;
      if(!confirm('Delete this patient?')) return;
      const f = document.createElement('form');
      f.method = 'POST'; f.action = url;
      const t = document.createElement('input'); t.type='hidden'; t.name='_token';  t.value=CSRF; f.appendChild(t);
      const m = document.createElement('input'); m.type='hidden'; m.name='_method'; m.value='DELETE'; f.appendChild(m);
      document.body.appendChild(f); f.submit();
    }

    // Files & images helpers (for modal Docs/Images)
    const isAbs = u => /^https?:\/\//i.test(u||'') || (u||'').startsWith('/');
    const toUrl = p => !p ? '' : (isAbs(p) ? p : ('/storage/' + String(p).replace(/^\/+/,'')));
    const base = p => { try { const q=(p||'').split('?')[0].split('#')[0]; const a=q.split('/'); return a[a.length-1]||q; } catch { return p||'document'; } };
    const mimeGuess = p => {
      const e=(p||'').toLowerCase().split('.').pop();
      return {png:'image/png',jpg:'image/jpeg',jpeg:'image/jpeg',webp:'image/webp',gif:'image/gif',svg:'image/svg+xml',pdf:'application/pdf'}[e]||'application/octet-stream';
    };
    const isImg=(u,m)=> (m||'').toLowerCase().startsWith('image/') || /\.(png|jpe?g|webp|gif|svg)$/i.test((u||'').toLowerCase());
    const isPdf=(u,m)=> (m||'').toLowerCase()==='application/pdf' || /\.pdf$/i.test((u||'').toLowerCase());

    function renderPatientImages(rawList){
      const imgs = (Array.isArray(rawList)?rawList:[]).map(toUrl).filter(Boolean);
      if(!imgs.length) return '';
      const grid = imgs.map((u,i)=>`
        <a href="${esc(u)}" target="_blank" rel="noopener" class="block border rounded overflow-hidden hover:opacity-90 transition" title="${esc(base(u))}">
          <img src="${esc(u)}" alt="image-${i}" class="w-full h-40 object-cover" loading="lazy">
          <div class="p-2 text-xs text-gray-700 truncate">${esc(base(u))}</div>
        </a>`).join('');
      return `
        <div class="mt-6">
          <div class="text-gray-800 font-semibold mb-2">Patient Images (${imgs.length})</div>
          <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3" style="max-height:40vh;overflow:auto">
            ${grid}
          </div>
        </div>`;
    }

    async function fetchAndRenderDocuments(url){
      const holder = document.getElementById('docsSection');
      if(!holder) return;
      try{
        if(!url){ holder.innerHTML = `<div class="text-gray-600">No documents endpoint configured.</div>`; return; }
        const res = await fetch(url, { headers:{'X-Requested-With':'XMLHttpRequest','Accept':'application/json'}, credentials:'same-origin' });
        if(!res.ok){ holder.innerHTML = `<div class="text-red-600">Failed to load documents (HTTP ${res.status}).</div>`; return; }
        const json = await res.json();
        let raw = Array.isArray(json) ? json : (json.data ?? json.documents ?? []);
        if(!Array.isArray(raw)) raw = [];
        const docs = raw.map((item,i)=>{
          if(typeof item==='string'){
            const url = toUrl(item); const m = mimeGuess(item);
            return { id:String(i), name:base(item), url, mime:m, thumb:isImg(url,m)?url:null };
          } else {
            const rawPath = item.url || item.path || item.link || '';
            const url = toUrl(rawPath);
            const m = (item.mime || item.mimetype || item.type || mimeGuess(url)).toLowerCase();
            return { id:item.id ?? String(i), name:item.name || item.filename || base(url), url, mime:m, thumb:item.thumb_url || item.thumbnail || (isImg(url,m)?url:null) };
          }
        }).filter(x=>x.url);

        const images = docs.filter(d=>isImg(d.url,d.mime));
        const others = docs.filter(d=>!isImg(d.url,d.mime));

        const imagesGrid = images.map(it=>`
          <a href="${esc(it.url)}" target="_blank" rel="noopener" class="block border rounded overflow-hidden hover:opacity-90 transition" title="${esc(it.name)}">
            <img src="${esc(it.thumb||it.url)}" alt="${esc(it.name)}" class="w-full h-40 object-cover" loading="lazy">
            <div class="p-2 text-xs text-gray-700 truncate">${esc(it.name)}</div>
          </a>`).join('');

        const filesList = others.map(it=>`
          <div class="flex items-center justify-between border rounded p-2">
            <div class="pr-3">
              <div class="text-sm font-medium">${esc(it.name)}</div>
              <div class="text-xs text-gray-500">${esc(it.mime || (isPdf(it.url,it.mime)?'application/pdf':'file'))}</div>
            </div>
            <div class="flex items-center gap-2">
              <a href="${esc(it.url)}" target="_blank" rel="noopener" class="text-blue-600 hover:underline text-sm">Open</a>
              ${isPdf(it.url,it.mime)?`<a href="${esc(it.url)}#toolbar=0" target="_blank" rel="noopener" class="text-blue-600 hover:underline text-sm">Preview</a>`:''}
            </div>
          </div>`).join('');

        holder.innerHTML = `
          <div class="mt-6 space-y-4">
            <div class="text-gray-800 font-semibold">Documents</div>
            ${images.length ? `
              <div>
                <div class="text-gray-500 text-xs uppercase mb-2">Images (${images.length})</div>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3" style="max-height:40vh;overflow:auto">
                  ${imagesGrid}
                </div>
              </div>` : ``}
            ${others.length ? `
              <div>
                <div class="text-gray-500 text-xs uppercase mb-2">Files (${others.length})</div>
                <div class="space-y-2" style="max-height:${images.length ? '28vh' : '40vh'};overflow:auto">
                  ${filesList}
                </div>
              </div>` : ``}
            ${(!images.length && !others.length) ? `<div class="text-gray-600 text-sm">No documents available.</div>` : ``}
          </div>`;
      } catch(e){
        console.error('Docs fetch exception:', e);
        holder.innerHTML = `<div class="text-red-600">Error loading documents.</div>`;
      }
    }

    async function showPatientPrescriptions(btn){
      const url  = btn.getAttribute('data-url');
      const name = btn.getAttribute('data-patient-name') || '';
      try{
        const res = await fetch(url, { headers:{'X-Requested-With':'XMLHttpRequest','Accept':'application/json'}, credentials:'same-origin' });
        if(!res.ok) throw new Error(`HTTP ${res.status}`);
        const payload = await res.json();
        const items = payload.data || [];
        if(!items.length){
          return Swal.fire({ icon:'info', title:'No prescriptions', text:`No prescriptions found for ${name}.` });
        }
        const rows = items.map(p=>`
          <tr class="border-t">
            <td class="py-2 pr-2">#${esc(p.id)}</td>
            <td class="py-2 pr-2">${esc(p.date ?? '-')}</td>
            <td class="py-2 pr-2">${esc(p.doctor ?? '—')}</td>
            <td class="py-2 text-right"><a href="${esc(p.show_url)}" class="text-blue-600 hover:underline" target="_blank" rel="noopener">View</a></td>
          </tr>`).join('');
        Swal.fire({
          title: `Prescriptions — ${esc(name)} (${items.length})`,
          html: `<div class="text-left"><div style="max-height:60vh;overflow:auto">
                  <table class="w-full text-sm">
                    <thead><tr>
                      <th class="py-2 pr-2 text-gray-500 font-semibold text-xs uppercase">ID</th>
                      <th class="py-2 pr-2 text-gray-500 font-semibold text-xs uppercase">Date</th>
                      <th class="py-2 pr-2 text-gray-500 font-semibold text-xs uppercase">Doctor</th>
                      <th class="py-2 text-gray-500 font-semibold text-xs uppercase text-right">Open</th>
                    </tr></thead>
                    <tbody>${rows}</tbody>
                  </table>
                </div></div>`,
          width:'48rem', showCloseButton:true, focusConfirm:false, confirmButtonText:'Close',
        });
      } catch(e){
        console.error('Rx fetch error:', e);
        Swal.fire({ icon:'error', title:'Error', text:'Could not load prescriptions.' });
      }
    }

    function modalForRow(d){
      const hasRx = (d.rx_count || 0) > 0;
      const header = `
        <div class="flex items-center gap-4 mb-4">
          ${d.avatar
            ? `<img src="${esc(d.avatar)}" alt="${esc(d.name||'-')}" class="w-16 h-16 rounded-full object-cover ring-1 ring-gray-200">`
            : `<div class="w-16 h-16 rounded-full bg-gray-200 flex items-center justify-center ring-1 ring-gray-200">
                <span class="text-gray-600 text-lg font-semibold">${esc((d.name||'P').charAt(0).toUpperCase())}</span>
               </div>`}
          <div>
            <div class="text-lg font-semibold">${esc(d.name||'-')}</div>
            <div class="text-xs text-gray-500">ID #${esc(d.id||'')}</div>
          </div>
        </div>`;
      const infoGrid = `
        <div class="text-sm">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div><div class="text-gray-500 text-xs uppercase">Doctor</div><div class="font-medium">${esc(d.doctor||'-')}</div></div>
            <div><div class="text-gray-500 text-xs uppercase">Status</div><div class="font-medium">${esc(d.status||'-')}</div></div>
            <div><div class="text-gray-500 text-xs uppercase">Age</div><div class="font-medium">${esc(d.age ?? '-')}</div></div>
            <div><div class="text-gray-500 text-xs uppercase">Sex</div><div class="font-medium">${esc(d.sex||'-')}</div></div>
            <div><div class="text-gray-500 text-xs uppercase">Phone</div><div class="font-medium">${esc(d.phone||'-')}</div></div>
            <div><div class="text-gray-500 text-xs uppercase">Blood Group</div><div class="font-medium">${esc(d.blood||'-')}</div></div>
            <div><div class="text-gray-500 text-xs uppercase">Guardian</div><div class="font-medium">${esc(d.guardian||'-')}</div></div>
            <div><div class="text-gray-500 text-xs uppercase">Email</div><div class="font-medium">${esc(d.email||'-')}</div></div>
            <div class="md:col-span-2"><div class="text-gray-500 text-xs uppercase">Address</div><div class="font-medium">${esc(d.address||'-')}</div></div>
            <div><div class="text-gray-500 text-xs uppercase">Return Date</div><div class="font-medium">${esc(d.return||'-')}</div></div>
            <div><div class="text-gray-500 text-xs uppercase">Created</div><div class="font-medium">${esc(d.created||'-')}</div></div>
            <div><div class="text-gray-500 text-xs uppercase">Updated</div><div class="font-medium">${esc(d.updated||'-')}</div></div>
            <div class="md:col-span-2">
              <div class="text-gray-500 text-xs uppercase">Notes</div>
              <div class="font-medium leading-relaxed">${nl2br(d.notes||'-')}</div>
            </div>
          </div>
        </div>`;
      Swal.fire({
        title: `Patient — ${esc(d.name||'-')}`,
        html: `<div class="text-left">
                ${header}
                ${infoGrid}
                ${renderPatientImages(d.images)}
                <div id="docsSection" class="mt-6 text-sm text-gray-600">Loading documents…</div>
              </div>`,
        width: '64rem',
        showCloseButton: true,
        showCancelButton: true,
        cancelButtonText: 'Close',
        showConfirmButton: hasRx,
        confirmButtonText: hasRx ? `View Prescriptions (${d.rx_count})` : undefined,
        didOpen: () => fetchAndRenderDocuments(d.docs_url)
      }).then((res)=>{
        if(res.isConfirmed && hasRx && d.rx_url){
          const fakeBtn = { getAttribute: (k)=> k==='data-url' ? d.rx_url : (k==='data-patient-name' ? (d.name||'') : null) };
          showPatientPrescriptions(fakeBtn);
        }
      });
    }

    /* ===================== AG Grid ===================== */
    let gridApi, gridColumnApi;
    const colDefs = [
      { colId:'id', headerName: '#ID', field: 'id', width: 90, pinned: 'left',
        checkboxSelection: true, headerCheckboxSelection: true,
        sortable: true, filter: 'agNumberColumnFilter', floatingFilter: true },

      { colId:'avatar', headerName: 'Image', field: 'avatar', width: 96, floatingFilter: false,
        filter: false, sortable: false,
        cellRenderer: p => {
          const url = p.value, name = p.data.name || '';
          if (url) return `<img src="${esc(url)}" alt="${esc(name)}" class="w-10 h-10 rounded-full object-cover ring-1 ring-gray-200" />`;
          const initial = (name||'P').charAt(0).toUpperCase();
          return `<div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center ring-1 ring-gray-200">
                    <span class="text-gray-600 text-sm font-semibold">${esc(initial)}</span>
                  </div>`;
        }
      },

      { colId:'name', headerName: 'Name', field: 'name', flex: 1, minWidth: 160,
        sortable: true, filter: 'agTextColumnFilter', floatingFilter: true,
        filterParams: { debounceMs: 300, trimInput: true } },

      { colId:'age', headerName: 'Age', field: 'age', width: 90,
        sortable: true, filter: 'agNumberColumnFilter', floatingFilter: true },

      { colId:'sex', headerName: 'Gender', field: 'sex', width: 110,
        sortable: true, filter: 'agTextColumnFilter', floatingFilter: true,
        filterParams: { debounceMs: 300 } },

      { colId:'blood', headerName: 'Blood Group', field: 'blood', width: 130,
        sortable: true, filter: 'agTextColumnFilter', floatingFilter: true },

      { colId:'guardian', headerName: 'Guardian', field: 'guardian', minWidth: 140, flex: 1,
        sortable: true, filter: 'agTextColumnFilter', floatingFilter: true, filterParams: { debounceMs: 300 } },

      { colId:'phone', headerName: 'Phone', field: 'phone', minWidth: 130,
        sortable: true, filter: 'agTextColumnFilter', floatingFilter: true, filterParams: { debounceMs: 300 } },

      { colId:'return', headerName: 'Return Date', field: 'return', width: 170,
        sortable: true, filter: 'agDateColumnFilter', floatingFilter: true,
        filterParams: {
          debounceMs: 200,
          comparator: (filterDate, cellValue) => {
            // cellValue format: dd/mm/yyyy or '-'
            if (!cellValue || cellValue === '-') return -1;
            const [d,m,y] = cellValue.split(/[\/\-\.]/).map(Number);
            if (!(y&&m&&d)) return -1;
            const cell = new Date(y, m-1, d);
            // Return 0 if equal, negative if cell < filter, positive if cell > filter
            const diff = cell.setHours(0,0,0,0) - filterDate.setHours(0,0,0,0);
            return diff === 0 ? 0 : (diff < 0 ? -1 : 1);
          }
        },
        cellRenderer: p => {
          const info = dueTone(p.value);
          return `<span class="inline-flex items-center px-2 py-0.5 rounded text-xs ${info.cls}">${esc(info.text)}</span>`;
        }
      },

      { colId:'rx_count', headerName: 'Presc.', field: 'rx_count', width: 110,
        sortable: true, filter: 'agNumberColumnFilter', floatingFilter: true,
        cellRenderer: p => {
          const d = p.data, count = Number(d.rx_count || 0);
          const btn = document.createElement('button');
          btn.type = 'button';
          btn.textContent = String(count);
          btn.title = count === 0 ? 'No prescriptions' : 'View prescriptions';
          btn.className = `inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${count>0?'bg-blue-100 text-blue-800':'bg-gray-100 text-gray-700'}`;
          if (count > 0) {
            btn.setAttribute('data-url', d.rx_url || '');
            btn.setAttribute('data-patient-name', d.name || '');
            btn.addEventListener('click', () => showPatientPrescriptions(btn));
          } else {
            btn.disabled = true; btn.setAttribute('aria-disabled','true');
          }
          return btn;
        }
      },

      { colId:'actions', headerName: 'Actions', width: 280, pinned: 'right',
        sortable: false, filter: false, floatingFilter: false,
        cellRenderer: p => {
          const d = p.data;
          const wrap = document.createElement('div');
          wrap.className = 'flex flex-wrap items-center gap-2';

          const view = document.createElement('button');
          view.type = 'button';
          view.className = 'bg-indigo-600 text-white px-2 py-1 rounded hover:bg-indigo-700 text-sm';
          view.textContent = 'View';
          view.addEventListener('click', () => modalForRow(d));

          const edit = document.createElement('a');
          edit.href = d.edit_url || '#';
          edit.className = 'bg-yellow-500 text-white px-2 py-1 rounded hover:bg-yellow-600 text-sm';
          edit.textContent = 'Edit';

          const del = document.createElement('button');
          del.type = 'button';
          del.className = 'bg-red-600 text-white px-2 py-1 rounded hover:bg-red-700 text-sm';
          del.textContent = 'Delete';
          del.addEventListener('click', () => deletePatient(d.delete_url || '#'));

          wrap.appendChild(view);
          wrap.appendChild(edit);
          wrap.appendChild(del);
          return wrap;
        }
      },
    ];

    const gridOptions = {
      rowData: ROWS,
      columnDefs: colDefs,
      defaultColDef: {
        resizable: true,
        sortable: true,
        filter: true,
        floatingFilter: true,
        tooltipValueGetter: p => (p && p.value != null ? String(p.value) : ''),
        filterParams: { debounceMs: 250 },
      },
      rowSelection: 'multiple',
      pagination: true,
      paginationPageSize: 25,
      animateRows: true,
      suppressCellFocus: true,
      headerHeight: 44,
      rowHeight: 46,
      enableBrowserTooltips: true,

      rowClassRules: {
        'tint-overdue':  p => p.data && dueTone(p.data.return).cls.includes('red-'),
        'tint-today':    p => p.data && dueTone(p.data.return).cls.includes('amber-'),
        'tint-upcoming': p => p.data && dueTone(p.data.return).cls.includes('green-'),
      },

      // Selection counter & toggle bulk actions
      onSelectionChanged: () => {
        const sel = gridApi ? gridApi.getSelectedRows() : [];
        const n = (sel || []).length;
        document.getElementById('selCount').textContent = n;
        document.getElementById('btnExportSel').disabled = n === 0;
        document.getElementById('btnDeleteSel').disabled = n === 0;
      },
    };

    // Mount grid
    function mountGrid() {
      const el = document.getElementById('patientsGrid');
      if (!el) return;
      if (window.agGrid && typeof agGrid.createGrid === 'function') {
        gridApi = agGrid.createGrid(el, gridOptions);
        gridColumnApi = gridApi.getColumnApi ? gridApi.getColumnApi() : (gridOptions.columnApi || null);
      } else if (window.agGrid && typeof agGrid.Grid === 'function') {
        new agGrid.Grid(el, gridOptions);
        gridApi = gridOptions.api;
        gridColumnApi = gridOptions.columnApi;
      } else {
        console.error('AG Grid not loaded.');
      }
    }

    /* ===================== Toolbar wiring ===================== */
    function setQuickFilter(val){
      if (!gridApi) return;
      if (gridApi.setGridOption) gridApi.setGridOption('quickFilterText', val);
      else if (gridApi.setQuickFilter) gridApi.setQuickFilter(val);
    }
    function setPageSize(n){
      if (!gridApi) return;
      n = Number(n)||25;
      if (gridApi.setGridOption) gridApi.setGridOption('paginationPageSize', n);
      else if (gridApi.paginationSetPageSize) gridApi.paginationSetPageSize(n);
    }
    function resetAll(){
      if (!gridApi) return;
      if (gridApi.setFilterModel) gridApi.setFilterModel(null);
      if (gridApi.setSortModel) gridApi.setSortModel(null);
      if (gridColumnApi && gridColumnApi.resetColumnState) gridColumnApi.resetColumnState();
      setQuickFilter('');
      const q = document.getElementById('quickFilter'); if (q) q.value = '';
      const ps = document.getElementById('pageSize'); if (ps) ps.value = '25';
      setPageSize(25);
      if (gridApi.paginationGoToFirstPage) gridApi.paginationGoToFirstPage();
    }
    function exportCsv(opts = {}) {
      if (!gridApi || !gridApi.exportDataAsCsv) return;
      gridApi.exportDataAsCsv({ fileName: 'patients.csv', ...opts });
    }
    function autoSizeAll() {
      if (!gridColumnApi) return;
      const all = [];
      gridColumnApi.getAllDisplayedColumns().forEach(c => all.push(c.getColId ? c.getColId() : c.colId));
      if (all.length) gridColumnApi.autoSizeColumns(all, false);
    }
    function fitColumns() {
      if (!gridApi || !gridApi.sizeColumnsToFit) return;
      gridApi.sizeColumnsToFit();
    }

    // Save / Load View state
    function saveView() {
      if (!gridApi || !gridColumnApi) return;
      const state = {
        sort: gridApi.getSortModel ? gridApi.getSortModel() : null,
        filter: gridApi.getFilterModel ? gridApi.getFilterModel() : null,
        colState: gridColumnApi.getColumnState ? gridColumnApi.getColumnState() : null,
        pageSize: (gridApi.paginationGetPageSize && gridApi.paginationGetPageSize()) || 25,
        quick: document.getElementById('quickFilter')?.value || '',
      };
      localStorage.setItem(STORAGE_KEY, JSON.stringify(state));
      alert('View saved');
    }
    function loadView() {
      if (!gridApi || !gridColumnApi) return;
      const raw = localStorage.getItem(STORAGE_KEY);
      if (!raw) { alert('No saved view'); return; }
      try {
        const s = JSON.parse(raw);
        if (s.colState && gridColumnApi.applyColumnState) {
          gridColumnApi.applyColumnState({ state: s.colState, applyOrder: true });
        }
        if (s.filter && gridApi.setFilterModel) gridApi.setFilterModel(s.filter);
        if (s.sort && gridApi.setSortModel) gridApi.setSortModel(s.sort);
        if (s.pageSize) {
          const ps = document.getElementById('pageSize'); if (ps) ps.value = String(s.pageSize);
          setPageSize(s.pageSize);
        }
        if (s.quick != null) {
          const q = document.getElementById('quickFilter'); if (q) q.value = s.quick;
          setQuickFilter(s.quick);
        }
      } catch {}
    }
    function resetView() {
      localStorage.removeItem(STORAGE_KEY);
      resetAll();
    }

    // Column chooser
    function buildColumnMenu() {
      const panel = document.getElementById('colPanel');
      if (!panel || !gridColumnApi) return;
      panel.innerHTML = '';
      (gridColumnApi.getAllGridColumns ? gridColumnApi.getAllGridColumns() : []).forEach(col => {
        const id = col.getColId ? col.getColId() : col.colId;
        if (id === 'actions') return; // keep actions visible
        const visible = col.isVisible ? col.isVisible() : true;
        const row = document.createElement('label');
        row.className = 'menu-row';
        const cb = document.createElement('input');
        cb.type = 'checkbox'; cb.checked = visible;
        cb.addEventListener('change', () => gridColumnApi.setColumnVisible(id, cb.checked));
        const name = document.createElement('span');
        name.textContent = col.getColDef ? (col.getColDef().headerName || id) : id;
        row.appendChild(cb); row.appendChild(name);
        panel.appendChild(row);
      });
    }
    function toggleMenu(el) {
      const menu = document.getElementById('colMenu');
      if (!menu) return;
      menu.classList.toggle('open');
      if (menu.classList.contains('open')) buildColumnMenu();
    }

    // Bulk actions
    function exportSelected() {
      exportCsv({ onlySelected: true, fileName: 'patients-selected.csv' });
    }
    function deleteSelected() {
      if (!gridApi) return;
      const sel = gridApi.getSelectedRows() || [];
      if (!sel.length) return;
      if (!confirm(`Delete ${sel.length} selected patient(s)?`)) return;
      // Submit one-by-one (simple & safe)
      sel.forEach(r => r.delete_url && deletePatient(r.delete_url));
    }

    // Density & stripes
    function toggleDensity(dense) {
      if (!gridApi) return;
      const rh = dense ? 38 : 46;
      const hh = dense ? 38 : 44;
      if (gridApi.setGridOption) {
        gridApi.setGridOption('rowHeight', rh);
        gridApi.setGridOption('headerHeight', hh);
      }
      if (gridApi.resetRowHeights) gridApi.resetRowHeights();
      if (gridApi.refreshHeader) gridApi.refreshHeader();
    }
    function toggleStriped(on){
      const gridEl = document.getElementById('patientsGrid');
      if (!gridEl) return;
      gridEl.classList.toggle('striped-off', !on);
    }

    document.addEventListener('DOMContentLoaded', () => {
      mountGrid();

      // toolbar wiring
      const q   = document.getElementById('quickFilter');
      const ps  = document.getElementById('pageSize');
      const rs  = document.getElementById('btnReset');     // Reset filters (kept if you want)
      const ex  = document.getElementById('btnExport');
      const exs = document.getElementById('btnExportSel');
      const dels= document.getElementById('btnDeleteSel');
      const dn  = document.getElementById('dense');
      const st  = document.getElementById('striped');
      const fit = document.getElementById('btnFit');
      const autos = document.getElementById('btnAuto');
      const saveV = document.getElementById('btnSaveView');
      const loadV = document.getElementById('btnLoadView');
      const resetV= document.getElementById('btnResetView');
      const menuB = document.getElementById('btnCols');
      const menu  = document.getElementById('colMenu');

      if (q)   q.addEventListener('input', e => setQuickFilter(e.target.value));
      if (ps)  ps.addEventListener('change', e => setPageSize(e.target.value));
      if (rs)  rs.addEventListener('click', resetAll);
      if (ex)  ex.addEventListener('click', () => exportCsv());
      if (exs) exs.addEventListener('click', exportSelected);
      if (dels)dels.addEventListener('click', deleteSelected);
      if (dn)  dn.addEventListener('change', e => toggleDensity(e.target.checked));
      if (st)  st.addEventListener('change', e => toggleStriped(e.target.checked));
      if (fit) fit.addEventListener('click', fitColumns);
      if (autos) autos.addEventListener('click', autoSizeAll);
      if (saveV) saveV.addEventListener('click', saveView);
      if (loadV) loadV.addEventListener('click', loadView);
      if (resetV)resetV.addEventListener('click', resetView);
      if (menuB) menuB.addEventListener('click', () => toggleMenu(menu));

      // close column menu when clicking outside
      document.addEventListener('click', (e) => {
        const cm = document.getElementById('colMenu');
        if (!cm) return;
        if (!cm.contains(e.target)) cm.classList.remove('open');
      });

      // initial stripes checked
      toggleStriped(true);
    });
  </script>
</x-app-layout>
