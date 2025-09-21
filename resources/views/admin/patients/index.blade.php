{{-- resources/views/patients/index.blade.php --}}
<x-app-layout>
    <div class="container mx-auto py-8">
        {{-- Header --}}
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold">Patients</h2>
            <a href="{{ route('patients.create') }}"
               class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">+ Add Patient</a>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto bg-white rounded shadow">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#ID</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Image</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Age</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Gender</th>
                          <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Blood Group</th>
                          <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Guardian</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Return Date</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Presc.</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>

                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($patients as $patient)
                        @php
                            $count = $patient->prescriptions_count ?? 0;

                            // Images (JSON array); get first one for avatar & pass all to popup
                            $imgArr  = is_array($patient->images ?? null) ? $patient->images : [];
                            $imgPath = count($imgArr) ? $imgArr[0] : null;

                            // Build URL for the table/avatar without Storage facade
                            $imgUrl = $imgPath
                                ? (preg_match('/^(https?:\/\/|\/)/i', $imgPath) ? $imgPath : '/storage/' . ltrim($imgPath, '/'))
                                : null;

                            $returnDisplay  = $patient->next_return_date
                                ? \Carbon\Carbon::parse($patient->next_return_date)->format('d/m/Y')
                                : '-';
                            $createdDisplay = optional($patient->created_at)?->timezone(config('app.timezone', 'UTC'))?->format('d/m/Y H:i');
                            $updatedDisplay = optional($patient->updated_at)?->timezone(config('app.timezone', 'UTC'))?->format('d/m/Y H:i');
                        @endphp

                        <tr class="hover:bg-gray-50 transition">
                          <td class="px-4 py-2">{{''. $patient->id }}</td>
                            <td class="px-4 py-2">
                                @if($imgUrl)
                                    <img src="{{ $imgUrl }}" alt="{{ $patient->name }}"
                                         class="w-10 h-10 rounded-full object-cover ring-1 ring-gray-200">
                                @else
                                    <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center ring-1 ring-gray-200">
                                        <span class="text-gray-600 text-sm font-semibold">
                                            {{ strtoupper(substr($patient->name, 0, 1)) }}
                                        </span>
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-2">{{ $patient->name }}</td>
                            <td class="px-4 py-2">{{ $patient->age ?? '-' }}</td>
                            <td class="px-4 py-2">{{ $patient->sex ?? '-' }}</td>
                            <td class="px-4 py-2">{{ $patient->blood_group ?? '-' }}</td>
                              <td class="px-4 py-2">{{ $patient->guardian_name ?? '-' }}</td>
                            <td class="px-4 py-2">{{ $patient->phone ?? '-' }}</td>
                            <td class="px-4 py-2 max-w-[240px] truncate" title="{{ $returnDisplay !== '-' ? $returnDisplay : '' }}">
                                {{ $returnDisplay }}
                            </td>

                            {{-- Prescriptions badge --}}
                            <td class="px-4 py-2">
                                <button
                                    type="button"
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                           {{ $count > 0 ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-700' }}
                                           hover:opacity-80 transition"
                                    data-url="{{ route('patients.prescriptions', $patient) }}"
                                    data-patient-name="{{ $patient->name }}"
                                    onclick="showPatientPrescriptions(this)"
                                    {{ $count === 0 ? 'disabled' : '' }}
                                    title="{{ $count === 0 ? 'No prescriptions' : 'View prescriptions' }}"
                                >
                                    {{ $count }}
                                </button>
                            </td>

                            <td class="px-4 py-2 flex flex-wrap gap-2">
                                {{-- View details (with patient images + documents) --}}
                                <button
                                    type="button"
                                    class="bg-indigo-600 text-white px-2 py-1 rounded hover:bg-indigo-700 text-sm"
                                    onclick="showPatientInfo(this)"
                                    data-id="{{ $patient->id }}"
                                    data-avatar="{{ $imgUrl }}"
                                    data-images='@json($imgArr)'
                                    data-name="{{ $patient->name }}"
                                    data-age="{{ $patient->age ?? '-' }}"
                                    data-sex="{{ $patient->sex ?? '-' }}"
                                    data-dob="{{ $patient->dob ?? '-' }}"
                                    data-blood_group="{{ $patient->	blood_group ?? '-' }}"
                                    data-guardian_name="{{ $patient->	guardian_name ?? '-' }}"
                                    data-address="{{ $patient->address ?? '-' }}"
                                    data-doctor="{{ $patient->doctor->name ?? '-' }}"
                                    data-phone="{{ $patient->phone ?? '-' }}"
                                    data-email="{{ $patient->email ?? '-' }}"
                                    data-status="{{ ucfirst($patient->status ?? '-') }}"
                                    data-notes="{{ $patient->notes ?? '' }}"
                                    data-return-date="{{ $returnDisplay }}"
                                    data-created="{{ $createdDisplay ?? '-' }}"
                                    data-updated="{{ $updatedDisplay ?? '-' }}"
                                    data-rx-count="{{ $count }}"
                                    data-rx-url="{{ route('patients.prescriptions', $patient) }}"
                                    data-docs-url="{{ route('patients.documents', $patient) }}"
                                >
                                   {{-- Heroicon "eye" for "View" --}}
                                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                      stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                      <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 
                                              4.5c4.638 0 8.574 3.01 9.963 7.183.07.207.07.431 
                                              0 .639C20.577 16.49 16.64 19.5 12 
                                              19.5c-4.638 0-8.574-3.01-9.963-7.183z" />
                                      <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                  </svg>
                                </button>

                                <a href="{{ route('patients.edit', $patient->id) }}"class="bg-yellow-500 text-white px-2 py-1 rounded hover:bg-yellow-600 text-sm"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 
                                            16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 
                                            0 011.13-1.897l8.932-8.931z" />
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M19.5 7.125L17.25 4.875" />
                                </svg></a>

                                <form action="{{ route('patients.destroy', $patient->id) }}" method="POST"
                                      onsubmit="return confirm('Are you sure?');">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            class="bg-red-600 text-white px-2 py-1 rounded hover:bg-red-700 text-sm">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  d="M6 7h12M9 7v10m6-10v10M4 7h16l-1 12a2 2 0 01-2 2H7a2 2 0 01-2-2L4 7zM9 4h6v2H9V4z" />
                                        </svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="px-4 py-10 text-center text-gray-500">No patients found.</td></tr>
                    @endforelse
                </tbody>
            </table>

            @if(method_exists($patients,'links'))
                <div class="p-3 border-t bg-gray-50">
                    {{ $patients->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- SweetAlert2 (load if not already in your layout) --}}
    <script>
      if (typeof window.Swal === 'undefined') {
        const s = document.createElement('script');
        s.src = 'https://cdn.jsdelivr.net/npm/sweetalert2@11';
        document.head.appendChild(s);
      }
    </script>

    <script>
    /* ========= helpers ========= */
    function esc(s){ if(s==null) return ''; return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#39;'); }
    function nl2br(s){ return esc(s).replace(/\n/g,'<br>'); }
    function isAbs(u){ return /^https?:\/\//i.test(u) || (u||'').startsWith('/'); }
    function toUrl(p){ if(!p) return ''; return isAbs(p) ? p : ('/storage/' + String(p).replace(/^\/+/,'')); }
    function base(p){ try{ const q=(p||'').split('?')[0].split('#')[0]; const a=q.split('/'); return a[a.length-1]||q; }catch{ return p||'document'; } }
    function mimeGuess(p){ const e=(p||'').toLowerCase().split('.').pop(); return {png:'image/png',jpg:'image/jpeg',jpeg:'image/jpeg',webp:'image/webp',gif:'image/gif',svg:'image/svg+xml',pdf:'application/pdf'}[e]||'application/octet-stream'; }
    const isImg=(u,m)=> (m||'').toLowerCase().startsWith('image/') || /\.(png|jpe?g|webp|gif|svg)$/i.test((u||'').toLowerCase());
    const isPdf=(u,m)=> (m||'').toLowerCase()==='application/pdf' || /\.pdf$/i.test((u||'').toLowerCase());

    /* ========= View (details + patient images + documents) ========= */
    async function showPatientInfo(btn) {
      // pull data-* attrs
      let imgsRaw = [];
      try { imgsRaw = JSON.parse(btn.getAttribute('data-images') || '[]'); } catch (_){}
      const data = {
        id: btn.getAttribute('data-id'),
        avatar: btn.getAttribute('data-avatar') || '',
        images: Array.isArray(imgsRaw) ? imgsRaw : [],
        name: btn.getAttribute('data-name') || '-',
        age: btn.getAttribute('data-age') || '-',
        sex: btn.getAttribute('data-sex') || '-',
        address: btn.getAttribute('data-address') || '-',
        doctor: btn.getAttribute('data-doctor') || '-',
        phone: btn.getAttribute('data-phone') || '-',
        blood_group: btn.getAttribute('data-blood-group') || '-',
        guardian: btn.getAttribute('data-guardian') || '-',
        email: btn.getAttribute('data-email') || '-',
        status: btn.getAttribute('data-status') || '-',
        notes: btn.getAttribute('data-notes') || '-',
        return_date: btn.getAttribute('data-return-date') || '-',
        created_at: btn.getAttribute('data-created') || '-',
        updated_at: btn.getAttribute('data-updated') || '-',
        prescriptions_count: parseInt(btn.getAttribute('data-rx-count') || '0', 10),
        prescriptions_url: btn.getAttribute('data-rx-url') || null,
        docs_url: btn.getAttribute('data-docs-url') || null,
      };
      const hasRx = data.prescriptions_count > 0;

      // header with avatar
      const header = `
        <div class="flex items-center gap-4 mb-4">
          ${data.avatar
            ? `<img src="${esc(data.avatar)}" alt="${esc(data.name)}" class="w-16 h-16 rounded-full object-cover ring-1 ring-gray-200">`
            : `<div class="w-16 h-16 rounded-full bg-gray-200 flex items-center justify-center ring-1 ring-gray-200">
                 <span class="text-gray-600 text-lg font-semibold">${esc((data.name||'P').charAt(0).toUpperCase())}</span>
               </div>`}
          <div>
            <div class="text-lg font-semibold">${esc(data.name)}</div>
            <div class="text-xs text-gray-500">ID #${esc(data.id)}</div>
          </div>
        </div>
      `;

      // info grid
      const infoGrid = `
        <div class="text-sm">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div><div class="text-gray-500 text-xs uppercase">Doctor</div><div class="font-medium">${esc(data.doctor)}</div></div>
            <div><div class="text-gray-500 text-xs uppercase">Status</div><div class="font-medium">${esc(data.status)}</div></div>
            <div><div class="text-gray-500 text-xs uppercase">Age</div><div class="font-medium">${esc(data.age)}</div></div>
            <div><div class="text-gray-500 text-xs uppercase">Sex</div><div class="font-medium">${esc(data.sex)}</div></div>
            <div><div class="text-gray-500 text-xs uppercase">Phone</div><div class="font-medium">${esc(data.phone)}</div></div>
            <div><div class="text-gray-500 text-xs uppercase">Blood Group</div><div class="font-medium">${esc(data.blood_group)}</div></div>
            <div><div class="text-gray-500 text-xs uppercase">Guardian</div><div class="font-medium">${esc(data.guardian)}</div></div>
            <div><div class="text-gray-500 text-xs uppercase">Email</div><div class="font-medium">${esc(data.email)}</div></div>
            <div class="md:col-span-2"><div class="text-gray-500 text-xs uppercase">Address</div><div class="font-medium">${esc(data.address)}</div></div>
            <div><div class="text-gray-500 text-xs uppercase">Return Date</div><div class="font-medium">${esc(data.return_date)}</div></div>
            <div><div class="text-gray-500 text-xs uppercase">Created</div><div class="font-medium">${esc(data.created_at)}</div></div>
            <div><div class="text-gray-500 text-xs uppercase">Updated</div><div class="font-medium">${esc(data.updated_at)}</div></div>
            <div class="md:col-span-2">
              <div class="text-gray-500 text-xs uppercase">Notes</div>
              <div class="font-medium leading-relaxed">${nl2br(data.notes)}</div>
            </div>
          </div>
        </div>
      `;

      // render popup immediately, then fetch docs in didOpen
      Swal.fire({
        title: `Patient — ${esc(data.name)}`,
        html: `
          <div class="text-left">
            ${header}
            ${infoGrid}
            ${renderPatientImages(data.images)}
            <div id="docsSection" class="mt-6 text-sm text-gray-600">Loading documents…</div>
          </div>
        `,
        width: '64rem',
        showCloseButton: true,
        showCancelButton: true,
        cancelButtonText: 'Close',
        showConfirmButton: hasRx,
        confirmButtonText: hasRx ? `View Prescriptions (${data.prescriptions_count})` : undefined,
        didOpen: () => fetchAndRenderDocuments(data.docs_url)
      }).then((res) => {
        if (res.isConfirmed && hasRx && data.prescriptions_url) {
          const fakeBtn = {
            getAttribute: (k) => {
              if (k === 'data-url') return data.prescriptions_url;
              if (k === 'data-patient-name') return data.name || '';
              return null;
            }
          };
          showPatientPrescriptions(fakeBtn);
        }
      });
    }

    function renderPatientImages(rawList){
      // raw paths from DB; convert to URLs
      const imgs = (Array.isArray(rawList) ? rawList : [])
        .map(p => toUrl(p))
        .filter(Boolean);

      if (!imgs.length) return '';

      const grid = imgs.map((u, i) => `
        <a href="${esc(u)}" target="_blank" rel="noopener"
           class="block border rounded overflow-hidden hover:opacity-90 transition"
           title="${esc(base(u))}">
          <img src="${esc(u)}" alt="image-${i}" class="w-full h-40 object-cover" loading="lazy">
          <div class="p-2 text-xs text-gray-700 truncate">${esc(base(u))}</div>
        </a>
      `).join('');

      return `
        <div class="mt-6">
          <div class="text-gray-800 font-semibold mb-2">Patient Images (${imgs.length})</div>
          <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3" style="max-height:40vh;overflow:auto">
            ${grid}
          </div>
        </div>
      `;
    }

    async function fetchAndRenderDocuments(url){
      const holder = document.getElementById('docsSection');
      if (!holder) return;

      try{
        if (!url) {
          holder.innerHTML = `<div class="text-gray-600">No documents endpoint configured.</div>`;
          return;
        }
        const res = await fetch(url, {
          headers: { 'X-Requested-With':'XMLHttpRequest','Accept':'application/json' },
          credentials: 'same-origin'
        });
        if (!res.ok) {
          const t = await res.text();
          console.error('Docs error:', res.status, t);
          holder.innerHTML = `<div class="text-red-600">Failed to load documents (HTTP ${res.status}).</div>`;
          return;
        }

        const json = await res.json();
        let raw = Array.isArray(json) ? json : (json.data ?? json.documents ?? []);
        if (!Array.isArray(raw)) raw = [];

        const docs = raw.map((item, i) => {
          if (typeof item === 'string') {
            const url = toUrl(item);
            const m = mimeGuess(item);
            return { id: String(i), name: base(item), url, mime: m, thumb: isImg(url, m) ? url : null };
          } else {
            const rawPath = item.url || item.path || item.link || '';
            const url = toUrl(rawPath);
            const m = (item.mime || item.mimetype || item.type || mimeGuess(url)).toLowerCase();
            return {
              id: item.id ?? String(i),
              name: item.name || item.filename || base(url),
              url,
              mime: m,
              thumb: item.thumb_url || item.thumbnail || (isImg(url, m) ? url : null),
            };
          }
        }).filter(x => x.url);

        const images = docs.filter(d => isImg(d.url, d.mime));
        const others = docs.filter(d => !isImg(d.url, d.mime));

        const imagesGrid = images.map(it => `
          <a href="${esc(it.url)}" target="_blank" rel="noopener"
             class="block border rounded overflow-hidden hover:opacity-90 transition"
             title="${esc(it.name)}">
            <img src="${esc(it.thumb || it.url)}" alt="${esc(it.name)}"
                 class="w-full h-40 object-cover" loading="lazy">
            <div class="p-2 text-xs text-gray-700 truncate">${esc(it.name)}</div>
          </a>
        `).join('');

        const filesList = others.map(it => `
          <div class="flex items-center justify-between border rounded p-2">
            <div class="pr-3">
              <div class="text-sm font-medium">${esc(it.name)}</div>
              <div class="text-xs text-gray-500">${esc(it.mime || (isPdf(it.url, it.mime) ? 'application/pdf' : 'file'))}</div>
            </div>
            <div class="flex items-center gap-2">
              <a href="${esc(it.url)}" target="_blank" rel="noopener" class="text-blue-600 hover:underline text-sm">Open</a>
              ${isPdf(it.url, it.mime) ? `<a href="${esc(it.url)}#toolbar=0" target="_blank" rel="noopener" class="text-blue-600 hover:underline text-sm">Preview</a>` : ''}
            </div>
          </div>
        `).join('');

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
          </div>
        `;
      } catch(e){
        console.error('Docs fetch exception:', e);
        holder.innerHTML = `<div class="text-red-600">Error loading documents.</div>`;
      }
    }

    /* ========= Prescriptions popup ========= */
    async function showPatientPrescriptions(btn) {
      const url  = btn.getAttribute('data-url');
      const name = btn.getAttribute('data-patient-name');

      try {
        const res = await fetch(url, {
          headers: { 'X-Requested-With':'XMLHttpRequest','Accept':'application/json' },
          credentials: 'same-origin'
        });
        if (!res.ok) throw new Error(`HTTP ${res.status}`);
        const payload = await res.json();
        const items = payload.data || [];

        if (!items.length) {
          return Swal.fire({ icon:'info', title:'No prescriptions', text:`No prescriptions found for ${name}.` });
        }

        const rows = items.map(p => `
          <tr class="border-t">
            <td class="py-2 pr-2">#${esc(p.id)}</td>
            <td class="py-2 pr-2">${esc(p.date ?? '-')}</td>
            <td class="py-2 pr-2">${esc(p.doctor ?? '—')}</td>
            <td class="py-2 text-right"><a href="${esc(p.show_url)}" class="text-blue-600 hover:underline" target="_blank" rel="noopener">View</a></td>
          </tr>
        `).join('');

        Swal.fire({
          title: `Prescriptions — ${esc(name)} (${items.length})`,
          html: `
            <div class="text-left">
              <div style="max-height:60vh;overflow:auto">
                <table class="w-full text-sm">
                  <thead><tr>
                    <th class="py-2 pr-2 text-gray-500 font-semibold text-xs uppercase">ID</th>
                    <th class="py-2 pr-2 text-gray-500 font-semibold text-xs uppercase">Date</th>
                    <th class="py-2 pr-2 text-gray-500 font-semibold text-xs uppercase">Doctor</th>
                    <th class="py-2 text-gray-500 font-semibold text-xs uppercase text-right">Open</th>
                  </tr></thead>
                  <tbody>${rows}</tbody>
                </table>
              </div>
            </div>`,
          width:'48rem',
          showCloseButton:true,
          focusConfirm:false,
          confirmButtonText:'Close',
        });
      } catch (e) {
        console.error('Rx fetch error:', e);
        Swal.fire({ icon:'error', title:'Error', text:'Could not load prescriptions.' });
      }
    }
    </script>

<script>
  /* ---- tiny helpers (keep or merge with yours) ---- */
  const svg = {
    user:  '<svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 7.5a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.5 19.5a7.5 7.5 0 0115 0"/></svg>',
    phone: '<svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 4.5l4.5-2.25L9 6.75 6.75 9l7.5 7.5 2.25-2.25 4.5 2.25-2.25 4.5a3 3 0 01-3 1.5c-7.456 0-13.5-6.044-13.5-13.5a3 3 0 011.5-3z"/></svg>',
    mail:  '<svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21.75 7.5v9a2.25 2.25 0 01-2.25 2.25H4.5A2.25 2.25 0 012.25 16.5v-9"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21.75 7.5L12 13.5 2.25 7.5"/></svg>',
    home:  '<svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 12l9.75-7.5L21.75 12v7.5A1.5 1.5 0 0120.25 21H3.75A1.5 1.5 0 012.25 19.5V12z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 21V12h6v9"/></svg>',
    id:    '<svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 7.5A2.25 2.25 0 015.25 5.25h13.5A2.25 2.25 0 0121 7.5V16.5A2.25 2.25 0 0118.75 18.75H5.25A2.25 2.25 0 013 16.5V7.5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7.5 9h4.5M7.5 12h6.75M7.5 15h3"/></svg>',
    doctor:'<svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7a4 4 0 118 0v2a4 4 0 11-8 0V7z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 21v-3a4 4 0 014-4h4a4 4 0 014 4v3"/></svg>',
    status:'<svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
    calendar:'<svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 3v3M16 3v3M3 8h18M4.5 21h15A1.5 1.5 0 0021 19.5V7.5A1.5 1.5 0 0019.5 6h-15A1.5 1.5 0 003 7.5v12A1.5 1.5 0 004.5 21z"/></svg>',
    file:  '<svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 3H6a1.5 1.5 0 00-1.5 1.5v15A1.5 1.5 0 006 21h12a1.5 1.5 0 001.5-1.5V8.25L15.75 3z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 3V8.25H21"/></svg>',
    pills: '<svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 3h6a3 3 0 013 3v12a3 3 0 01-3 3H9a3 3 0 01-3-3V6a3 3 0 013-3z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 9h6"/></svg>'
  };

  const badge = (text, tone='gray') => {
    const tones = {
      gray:  'bg-gray-100 text-gray-800',
      green: 'bg-green-100 text-green-800',
      red:   'bg-red-100 text-red-800',
      amber: 'bg-amber-100 text-amber-800',
      blue:  'bg-blue-100 text-blue-800',
    };
    return `<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs ${tones[tone]||tones.gray}">${text}</span>`;
  };

  function dueTone(dateStr){ // returns {text, tone}
    if(!dateStr || dateStr==='-') return { text:'No Return', tone:'gray' };
    // expect format d/m/Y; fallback safe parsing
    const [d,m,y] = (dateStr||'').split(/[\/\-\.]/).map(Number);
    const dt = (y && m && d) ? new Date(y, m-1, d) : new Date(dateStr);
    if(isNaN(dt)) return { text: dateStr, tone:'gray' };
    const today = new Date(); today.setHours(0,0,0,0);
    const cmp = new Date(dt.getFullYear(), dt.getMonth(), dt.getDate());
    if (cmp < today)  return { text:`Overdue ${dateStr}`, tone:'red' };
    if (cmp.getTime() === today.getTime()) return { text:`Due Today ${dateStr}`, tone:'amber' };
    return { text:`Due ${dateStr}`, tone:'green' };
  }

  /* ========= Friendlier profile popup ========= */
  async function showPatientInfo(btn) {
    // data pull
    let imgsRaw = [];
    try { imgsRaw = JSON.parse(btn.getAttribute('data-images') || '[]'); } catch(_){}
    const data = {
      id: btn.getAttribute('data-id'),
      avatar: btn.getAttribute('data-avatar') || '',
      images: Array.isArray(imgsRaw) ? imgsRaw : [],
      name: btn.getAttribute('data-name') || '-',
      age: btn.getAttribute('data-age') || '-',
      sex: btn.getAttribute('data-sex') || '-',
      dob: btn.getAttribute('data-dob') || '-',
      blood_group: btn.getAttribute('data-blood_group') || '-',
      guardian_name: btn.getAttribute('data-guardian_name') || '-',
      address: btn.getAttribute('data-address') || '-',
      doctor: btn.getAttribute('data-doctor') || '-',
      phone: btn.getAttribute('data-phone') || '-',
      email: btn.getAttribute('data-email') || '-',
      status: btn.getAttribute('data-status') || '-',
      notes: btn.getAttribute('data-notes') || '-',
      return_date: btn.getAttribute('data-return-date') || '-',
      created_at: btn.getAttribute('data-created') || '-',
      updated_at: btn.getAttribute('data-updated') || '-',
      rxCount: parseInt(btn.getAttribute('data-rx-count') || '0', 10),
      rxUrl: btn.getAttribute('data-rx-url') || null,
      docsUrl: btn.getAttribute('data-docs-url') || null,
    };

    const due = dueTone(data.return_date);
    const hasRx = data.rxCount > 0;

    // header card (avatar + core)
    const header = `
      <div class="flex items-center gap-4">
        ${data.avatar
          ? `<img src="${esc(data.avatar)}" class="w-16 h-16 rounded-full object-cover ring-1 ring-gray-200" alt="${esc(data.name)}">`
          : `<div class="w-16 h-16 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center ring-1 ring-indigo-200">
               <span class="text-lg font-bold">${esc((data.name||'P').charAt(0).toUpperCase())}</span>
             </div>`}
        <div class="flex-1 min-w-0">
          <div class="text-lg font-semibold truncate">${esc(data.name)}</div>
          <div class="text-xs text-gray-500 flex items-center gap-2 mt-0.5">
            <span class="inline-flex items-center gap-1">${svg.id}<span>#${esc(data.id)}</span></span>
            <span>•</span>
            <span class="inline-flex items-center gap-1">${svg.doctor}<span>${esc(data.doctor)}</span></span>
          </div>
          <div class="mt-1 flex flex-wrap items-center gap-2">
            ${badge(`${svg.user} ${esc(data.age)} / ${esc(data.sex)}`, 'blue')}
            ${badge(`${svg.calendar} ${esc(due.text)}`, due.tone)}
            ${badge(`${svg.status} ${esc(data.status)}`, 'gray')}
          </div>
        </div>
        <div class="flex flex-col gap-2">
          ${hasRx ? `<button class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded text-sm"
                     onclick="(function(){ const fake={getAttribute:(k)=>k==='data-url'?'${esc(data.rxUrl)}':(k==='data-patient-name'?'${esc(data.name)}':null)}; showPatientPrescriptions(fake); })()">
                     ${svg.pills} <span class="ml-1">Prescriptions (${data.rxCount})</span></button>` : ''}
          <a href="/patients/${encodeURIComponent(data.id)}/edit"
             class="px-3 py-1.5 bg-yellow-500 hover:bg-yellow-600 text-white rounded text-sm text-center">
             Edit</a>
        </div>
      </div>
    `;

    // info cards
    const info = `
      <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
        <div class="border rounded-lg p-3">
          <div class="text-[11px] uppercase text-gray-500 mb-1">Contact</div>
          <div class="text-sm flex flex-col gap-1">
            <div><span class="text-gray-500">Gurdian Name:</span> <span class="font-medium">${esc(data.guardian_name)}</span></div>
            <div><span class="text-gray-500">Address:</span> <span class="font-medium">${esc(data.address)}</span></div>
            <div class="inline-flex items-center gap-2">Phone: 
              ${data.phone && data.phone !== '-' ? `<a href="tel:${esc(data.phone)}" class="text-blue-600 hover:underline">${esc(data.phone)}</a>` : '<span>-</span>'}
            </div>
          
          </div>
        </div>
        <div class="border rounded-lg p-3">
          <div class="text-[11px] uppercase text-gray-500 mb-1">Meta</div>
          <div class="text-sm grid grid-cols-2 gap-x-3 gap-y-1">
            <div><span class="text-gray-500">Created:</span> <span class="font-medium">${esc(data.created_at)}</span></div>
            <div><span class="text-gray-500">Updated:</span> <span class="font-medium">${esc(data.updated_at)}</span></div>
            <div><span class="text-gray-500">Return:</span> <span class="font-medium">${esc(data.return_date)}</span></div>
            <div><span class="text-gray-500">Status:</span> <span class="font-medium">${esc(data.status)}</span></div>
          </div>
        </div>
        <div class="md:col-span-2 border rounded-lg p-3">
          <div class="text-[11px] uppercase text-gray-500 mb-1">Notes</div>
          <div class="text-sm leading-relaxed">${nl2br(data.notes)}</div>
        </div>
      </div>
    `;

    // images (your existing renderer works — keeping style consistent)
    const imgsHtml = renderPatientImages(data.images);

    // documents holder (loaded async)
    const docsHolder = `<div id="docsSection" class="mt-6 text-sm text-gray-600">Loading documents…</div>`;

    Swal.fire({
      title: `Patient`,
      html: `<div class="text-left space-y-4">${header}${info}${imgsHtml}${docsHolder}</div>`,
      width: '64rem',
      showCloseButton: true,
      showCancelButton: true,
      cancelButtonText: 'Close',
      showConfirmButton: false,
      didOpen: () => fetchAndRenderDocuments(data.docsUrl)
    });
  }
</script>



</x-app-layout>
