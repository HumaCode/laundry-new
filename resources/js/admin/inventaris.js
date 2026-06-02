/* =========================================================
   DATA — 32 inventory items
========================================================= */
const CATEGORIES = ['Deterjen & Kimia','Pewangi & Softener','Plastik & Kemasan','Peralatan Cuci','Peralalan Setrika','Kebersihan Outlet','ATK & Administrasi'];
const OUTLETS    = ['Outlet Pusat','Outlet Bandung','Outlet Surabaya','Outlet Yogyakarta'];
const CAT_ICONS  = {'Deterjen & Kimia':'🧴','Pewangi & Softener':'🌸','Plastik & Kemasan':'📦','Peralatan Cuci':'🪣','Peralalan Setrika':'🔌','Kebersihan Outlet':'🧹','ATK & Administrasi':'📋'};
const CAT_COLORS = {'Deterjen & Kimia':'#6366F1','Pewangi & Softener':'#EC4899','Plastik & Kemasan':'#3B82F6','Peralatan Cuci':'#10B981','Peralalan Setrika':'#F59E0B','Kebersihan Outlet':'#14B8A6','ATK & Administrasi':'#F97316'};

const RAW_ITEMS = [
    /* Deterjen */
    {name:'Deterjen Attack Cair',brand:'PT Kao Indonesia',price:45000,minS:20,maxS:100,unit:'liter'},
    {name:'Deterjen Rinso Ultra',brand:'PT Unilever',price:38000,minS:15,maxS:80,unit:'kg'},
    {name:'Deterjen Bubuk Wings',brand:'PT Wings Surya',price:28000,minS:10,maxS:60,unit:'kg'},
    {name:'Pemutih Bayclin',brand:'PT Clorox',price:15000,minS:12,maxS:50,unit:'botol'},
    {name:'Cairan Antinoda Pro',brand:'CV Kimia Prima',price:55000,minS:8,maxS:40,unit:'liter'},
    /* Pewangi */
    {name:'Pewangi Molto Ultra',brand:'PT Unilever',price:32000,minS:15,maxS:60,unit:'liter'},
    {name:'Softener Downy',brand:'P&G Indonesia',price:42000,minS:10,maxS:50,unit:'liter'},
    {name:'Pengharum Baju Fresh',brand:'CV Aroma Nusantara',price:18000,minS:20,maxS:80,unit:'botol'},
    /* Kemasan */
    {name:'Plastik PP 40x60 cm',brand:'CV Plastik Jaya',price:85000,minS:10,maxS:50,unit:'pack'},
    {name:'Plastik Gantung Jas',brand:'CV Plastik Jaya',price:65000,minS:5,maxS:30,unit:'pack'},
    {name:'Kantong Laundry Medium',brand:'UD Kemasan Prima',price:95000,minS:8,maxS:40,unit:'pack'},
    {name:'Label Stiker Pelanggan',brand:'CV Print Express',price:25000,minS:20,maxS:100,unit:'roll'},
    /* Peralatan Cuci */
    {name:'Sikat Noda Pakaian',brand:'Merk Lokal',price:12000,minS:10,maxS:40,unit:'pcs'},
    {name:'Ember Cuci 25L',brand:'Shinpo',price:35000,minS:5,maxS:20,unit:'pcs'},
    {name:'Keranjang Cucian Besar',brand:'Olax',price:55000,minS:5,maxS:20,unit:'pcs'},
    {name:'Selang Air 10m',brand:'Wavin',price:75000,minS:3,maxS:15,unit:'pcs'},
    /* Peralalan Setrika */
    {name:'Setrika Uap Philips GC',brand:'Philips',price:450000,minS:2,maxS:10,unit:'pcs'},
    {name:'Papan Setrika Besar',brand:'Toyama',price:180000,minS:3,maxS:12,unit:'pcs'},
    {name:'Spray Pelicin Pakaian',brand:'Robin',price:22000,minS:10,maxS:40,unit:'botol'},
    {name:'Kain Lap Setrika',brand:'Merk Lokal',price:8000,minS:20,maxS:80,unit:'lembar'},
    /* Kebersihan */
    {name:'Sabun Cuci Tangan Dettol',brand:'RB Health',price:28000,minS:10,maxS:50,unit:'botol'},
    {name:'Wipol Pembersih Lantai',brand:'Unilever',price:18000,minS:8,maxS:40,unit:'botol'},
    {name:'Tisu Lap Serbaguna',brand:'Paseo',price:35000,minS:10,maxS:50,unit:'pack'},
    {name:'Masker Kain Kasir',brand:'CV Sehat Jaya',price:5000,minS:30,maxS:100,unit:'pcs'},
    /* ATK */
    {name:'Nota Bon Rangkap',brand:'Sinar Dunia',price:8500,minS:20,maxS:80,unit:'dus'},
    {name:'Bolpoin Pilot Hitam',brand:'Pilot',price:4500,minS:30,maxS:100,unit:'pcs'},
    {name:'Buku Kas Laundry A4',brand:'Kiky',price:12000,minS:10,maxS:40,unit:'pcs'},
    {name:'Stapler + Isi Staples',brand:'MAX',price:35000,minS:5,maxS:20,unit:'pcs'},
    {name:'Selotip Besar',brand:'Nachi',price:9000,minS:10,maxS:40,unit:'pcs'},
    {name:'Spidol Permanen',brand:'Snowman',price:6500,minS:15,maxS:50,unit:'pcs'},
    {name:'Map Plastik Pelanggan',brand:'Bantex',price:3500,minS:50,maxS:200,unit:'pcs'},
    {name:'Tinta Printer Epson',brand:'Epson',price:95000,minS:3,maxS:15,unit:'botol'},
];

const HISTORY_DATES = ['2024-12-01','2024-11-15','2024-11-01','2024-10-15','2024-10-01'];

function buildStock(item, i) {
    const cat   = CATEGORIES[i % CATEGORIES.length];
    const outlet= OUTLETS[i % OUTLETS.length];
    // vary stock levels to get interesting data
    const stockLevels = [0, 3, 7, 14, 25, 40, 65, 90];
    const rawStock = stockLevels[i % stockLevels.length];
    const stock = Math.min(rawStock, item.maxS);
    const pct   = stock / item.maxS;
    let status;
    if      (stock === 0)              status = 'habis';
    else if (stock < item.minS * .5)   status = 'kritis';
    else if (stock < item.minS)        status = 'rendah';
    else if (pct > .8)                 status = 'lebih';
    else                               status = 'cukup';

    const history = HISTORY_DATES.slice(0,3).map((d,j)=>({
        date: d,
        qty: item.minS + j*5,
        supplier: item.brand,
        invoice: `INV-2024-${String(100+i*3+j).padStart(4,'0')}`,
    }));

    return {
        id:   `INV-${String(1000+i).padStart(4,'0')}`,
        name: item.name, brand: item.brand,
        code: `${cat.slice(0,3).toUpperCase().replace(/ /g,'')}-${String(i+1).padStart(3,'0')}`,
        category: cat, outlet,
        emoji: CAT_ICONS[cat] || '📦',
        color: CAT_COLORS[cat] || '#6366F1',
        stock, minStock: item.minS, maxStock: item.maxS,
        unit: item.unit, price: item.price,
        value: stock * item.price,
        status, desc: '',
        lastRestock: HISTORY_DATES[i % HISTORY_DATES.length],
        lastRestockQty: item.minS + 10,
        history,
    };
}

let allItems = RAW_ITEMS.map((item, i) => buildStock(item, i));

/* =========================================================
   STATE
========================================================= */
let filtered       = [...allItems];
let currentPage    = 1;
let perPage        = 10;
let selectedIds    = new Set();
let sortCol        = 'name';
let sortDir        = 'asc';
let activeStatFilter = 'all';
let activeItem     = null;
let editMode       = false;

/* =========================================================
   HELPERS
========================================================= */
function formatRp(n){ return 'Rp '+n.toLocaleString('id-ID'); }
function formatRpK(n){
    if(n>=1000000000) return 'Rp '+(n/1000000000).toFixed(1)+'M';
    if(n>=1000000)    return 'Rp '+(n/1000000).toFixed(1)+'jt';
    if(n>=1000)       return 'Rp '+(n/1000).toFixed(0)+'rb';
    return formatRp(n);
}
function relDate(d){
    const diff=Math.floor((new Date()-new Date(d))/86400000);
    if(diff===0)return'Hari ini';if(diff===1)return'Kemarin';
    if(diff<30)return diff+' hari lalu';return Math.floor(diff/30)+' bln lalu';
}
function getStockStatusClass(status){
    const m={habis:'habis',kritis:'kritis',rendah:'rendah',cukup:'cukup',lebih:'lebih'};
    return m[status]||'cukup';
}
function stockStatusLabel(status){
    const m={habis:'Habis',kritis:'Kritis',rendah:'Rendah',cukup:'Cukup',lebih:'Lebih'};
    return m[status]||status;
}
function stockBarColor(status){
    if(status==='habis'||status==='kritis') return 'var(--danger)';
    if(status==='rendah') return 'var(--warning)';
    if(status==='lebih')  return 'var(--primary)';
    return 'var(--secondary)';
}

/* =========================================================
   RENDER TABLE
========================================================= */
function renderTable(){
    const tbody = document.getElementById('invTableBody');
    const empty = document.getElementById('emptyState');
    const start = (currentPage-1)*perPage;
    const page  = filtered.slice(start, start+perPage);

    document.getElementById('totalCount').textContent = filtered.length;
    document.getElementById('showCount').textContent  = Math.min(perPage, filtered.length-start);

    if(!filtered.length){ tbody.innerHTML=''; empty.style.display=''; return; }
    empty.style.display = 'none';

    tbody.innerHTML = page.map(item => {
        const pct = item.maxStock > 0 ? Math.min(100, (item.stock/item.maxStock)*100) : 0;
        const cls = getStockStatusClass(item.status);
        return `
        <tr>
            <td class="cb-cell"><input type="checkbox" class="custom-cb row-cb" data-id="${item.id}" ${selectedIds.has(item.id)?'checked':''} onchange="toggleRowCheck(this)"></td>
            <td>
                <div style="display:flex;align-items:center;gap:.75rem">
                    <div class="item-icon-cell" style="background:${item.color}22;font-size:1.25rem">${item.emoji}</div>
                    <div>
                        <div class="item-name">${item.name}</div>
                        <div class="item-id">${item.code}</div>
                        <div style="font-size:.72rem;color:var(--gray-light);margin-top:.1rem">${item.brand}</div>
                    </div>
                </div>
            </td>
            <td>
                <span class="item-category" style="background:${item.color}18;color:${item.color}">
                    ${item.emoji} ${item.category}
                </span>
            </td>
            <td style="font-size:.8rem;color:var(--gray)">${item.outlet}</td>
            <td>
                <div class="stock-val ${cls}">${item.stock} <span style="font-size:.72rem;font-weight:400;color:var(--gray-light)">${item.unit}</span></div>
                <div class="stock-bar-wrap">
                    <div class="stock-bar">
                        <div class="stock-bar-fill" style="width:${pct}%;background:${stockBarColor(item.status)}"></div>
                    </div>
                </div>
                <div style="font-size:.68rem;color:var(--gray-light);margin-top:.2rem">Min: ${item.minStock} | Max: ${item.maxStock}</div>
            </td>
            <td><span class="inv-status ${cls}">${stockStatusLabel(item.status)}</span></td>
            <td style="font-size:.875rem;font-weight:600;color:var(--dark)">${formatRp(item.price)}<div style="font-size:.7rem;color:var(--gray-light)">per ${item.unit}</div></td>
            <td style="font-weight:700;color:var(--primary)">${formatRpK(item.value)}</td>
            <td>
                <div style="font-size:.8rem;font-weight:600;color:var(--dark)">${item.lastRestock}</div>
                <div style="font-size:.7rem;color:var(--gray-light)">${relDate(item.lastRestock)}</div>
            </td>
            <td>
                <div class="act-cell">
                    <button class="act-btn act-btn-view"    title="Detail"  onclick="openDetail('${item.id}')"><i class="fas fa-eye"></i></button>
                    <button class="act-btn act-btn-edit"    title="Edit"    onclick="openEditModal('${item.id}')"><i class="fas fa-pen"></i></button>
                    <button class="act-btn act-btn-restock" title="Restock" onclick="openRestock('${item.id}')"><i class="fas fa-redo-alt"></i></button>
                    <button class="act-btn act-btn-delete"  title="Hapus"   onclick="deleteById('${item.id}')"><i class="fas fa-trash-alt"></i></button>
                </div>
            </td>
        </tr>`;
    }).join('');

    syncCheckAll();
    renderPagination();
}

/* =========================================================
   STATS
========================================================= */
function updateStats(){
    const all    = allItems;
    const habis  = all.filter(x=>x.status==='habis'||x.status==='kritis').length;
    const rendah = all.filter(x=>x.status==='rendah').length;
    const cukup  = all.filter(x=>x.status==='cukup'||x.status==='lebih').length;
    const total  = all.reduce((s,x)=>s+x.value,0);

    document.getElementById('sc-all').textContent    = all.length;
    document.getElementById('sc-all-sub').textContent= all.length + ' jenis barang';
    document.getElementById('sc-cukup').textContent  = cukup;
    document.getElementById('sc-rendah').textContent = rendah;
    document.getElementById('sc-kritis').textContent = habis;
    document.getElementById('sc-nilai').textContent  = formatRpK(total);

    // Alert list
    const alerts = all.filter(x=>x.status==='habis'||x.status==='kritis'||x.status==='rendah')
                       .sort((a,b)=> {
                           const o={habis:0,kritis:1,rendah:2};
                           return (o[a.status]||3)-(o[b.status]||3);
                       });
    document.getElementById('alertCount').textContent = alerts.length + ' item';
    document.getElementById('alertList').innerHTML = alerts.length ? alerts.map(item=>{
        const cls = item.status==='rendah' ? 'warning' : 'critical';
        return `
        <div class="low-stock-item ${cls}" onclick="openDetail('${item.id}')">
            <div class="ls-icon ${cls}" style="font-size:1.1rem">${item.emoji}</div>
            <div class="ls-info">
                <div class="ls-name">${item.name}</div>
                <div class="ls-outlet">${item.outlet} · ${item.category}</div>
            </div>
            <div class="ls-right">
                <div class="ls-stock ${cls}">${item.stock} ${item.unit}</div>
                <div class="ls-min">Min: ${item.minStock} ${item.unit}</div>
                <button class="ls-reorder-btn ${item.status==='rendah'?'warning':''}" onclick="event.stopPropagation();openRestock('${item.id}')">Restock</button>
            </div>
        </div>`;
    }).join('') : `<div style="text-align:center;padding:2rem;color:var(--gray-light)"><i class="fas fa-check-circle" style="font-size:2rem;color:var(--secondary);display:block;margin-bottom:.5rem"></i>Semua stok dalam kondisi aman</div>`;
}

/* =========================================================
   CHART
========================================================= */
let stockChart;
function renderChart(){
    const chartEl = document.getElementById('stockChart');
    if (!chartEl) return;
    const ctx = chartEl.getContext('2d');
    const cats = CATEGORIES;
    const data = cats.map(cat => {
        const items = allItems.filter(x=>x.category===cat);
        return items.reduce((s,x)=>s+x.stock,0);
    });
    const maxData = cats.map(cat => {
        const items = allItems.filter(x=>x.category===cat);
        return items.reduce((s,x)=>s+x.maxStock,0);
    });
    const colors = cats.map(cat => CAT_COLORS[cat]||'#6366F1');
    const shortLabels = cats.map(c => c.length > 14 ? c.slice(0,14)+'…' : c);

    if(stockChart) stockChart.destroy();
    stockChart = new Chart(ctx,{
        type:'bar',
        data:{
            labels: shortLabels,
            datasets:[
                {label:'Stok Saat Ini', data, backgroundColor: colors.map(c=>c+'CC'), borderRadius:8, borderSkipped:false},
                {label:'Kapasitas Max',  data:maxData, backgroundColor: colors.map(c=>c+'22'), borderRadius:8, borderSkipped:false},
            ]
        },
        options:{
            responsive:true, maintainAspectRatio:false,
            interaction:{mode:'index',intersect:false},
            plugins:{
                legend:{display:true,position:'bottom',labels:{font:{size:11},boxWidth:12,padding:12}},
                tooltip:{
                    backgroundColor:'rgba(31,41,55,.95)',titleColor:'#fff',bodyColor:'rgba(255,255,255,.7)',
                    padding:12,cornerRadius:10,
                    callbacks:{label:ctx=>' '+ctx.dataset.label+': '+ctx.parsed.y.toLocaleString()}
                }
            },
            scales:{
                x:{grid:{display:false},border:{display:false},ticks:{color:'#9CA3AF',font:{size:10}}},
                y:{grid:{color:'#F3F4F6'},border:{display:false},ticks:{color:'#9CA3AF',font:{size:11}}}
            }
        }
    });
}

/* =========================================================
   FILTER & SORT
========================================================= */
function handleSearch(v){ document.getElementById('searchInput').value=v; applyFilters(); }

function applyFilters(){
    const q      = document.getElementById('searchInput').value.toLowerCase();
    const cat    = document.getElementById('filterCat').value;
    const outlet = document.getElementById('filterOutlet').value;
    const status = document.getElementById('filterStatus').value;

    filtered = allItems.filter(item => {
        const mq = !q || item.name.toLowerCase().includes(q) || item.code.toLowerCase().includes(q) || item.brand.toLowerCase().includes(q);
        const mc = !cat || item.category === cat;
        const mo = !outlet || item.outlet === outlet;
        const ms = !status || item.status === status;
        const mst= activeStatFilter==='all' || (activeStatFilter==='cukup'&&(item.status==='cukup'||item.status==='lebih')) || (activeStatFilter==='rendah'&&item.status==='rendah') || (activeStatFilter==='kritis'&&(item.status==='kritis'||item.status==='habis'));
        return mq && mc && mo && ms && mst;
    });

    // sort
    filtered.sort((a,b)=>{
        let va=a[sortCol], vb=b[sortCol];
        if(typeof va==='string') return sortDir==='asc'?va.localeCompare(vb):vb.localeCompare(va);
        return sortDir==='asc'?va-vb:vb-va;
    });

    currentPage=1; selectedIds.clear(); updateBulkBar(); renderTable();
}

function filterByStat(stat, el){
    activeStatFilter = stat;
    document.querySelectorAll('.stat-card').forEach(c=>c.classList.remove('active-filter'));
    el.classList.add('active-filter');
    applyFilters();
}

function resetFilters(){
    document.getElementById('searchInput').value='';
    document.getElementById('filterCat').value='';
    document.getElementById('filterOutlet').value='';
    document.getElementById('filterStatus').value='';
    activeStatFilter='all';
    document.querySelectorAll('.stat-card').forEach(c=>c.classList.remove('active-filter'));
    document.querySelector('.stat-card.c1').classList.add('active-filter');
    filtered=[...allItems]; currentPage=1; selectedIds.clear(); updateBulkBar(); renderTable();
}

function sortBy(col){
    if(sortCol===col) sortDir=sortDir==='asc'?'desc':'asc';
    else{sortCol=col;sortDir='asc';}
    document.querySelectorAll('.sort-icon').forEach(i=>i.classList.remove('active'));
    const el=document.getElementById('si-'+col); if(el) el.classList.add('active');
    applyFilters();
}

function changePerPage(v){ perPage=parseInt(v); currentPage=1; renderTable(); }

/* =========================================================
   PAGINATION
========================================================= */
function renderPagination(){
    const total=Math.ceil(filtered.length/perPage)||1;
    document.getElementById('currentPage').textContent=currentPage;
    document.getElementById('totalPages').textContent=total;
    const ctrl=document.getElementById('paginationControls');
    let h=`<button class="page-btn" onclick="goPage(${currentPage-1})" ${currentPage<=1?'disabled':''}><i class="fas fa-chevron-left"></i></button>`;
    let s=Math.max(1,currentPage-2),e=Math.min(total,s+4);
    if(e-s<4)s=Math.max(1,e-4);
    if(s>1){h+=`<button class="page-btn" onclick="goPage(1)">1</button>`;if(s>2)h+=`<span style="padding:0 .2rem;color:var(--gray-light)">…</span>`;}
    for(let i=s;i<=e;i++) h+=`<button class="page-btn ${i===currentPage?'active':''}" onclick="goPage(${i})">${i}</button>`;
    if(e<total){if(e<total-1)h+=`<span style="padding:0 .2rem;color:var(--gray-light)">…</span>`;h+=`<button class="page-btn" onclick="goPage(${total})">${total}</button>`;}
    h+=`<button class="page-btn" onclick="goPage(${currentPage+1})" ${currentPage>=total?'disabled':''}><i class="fas fa-chevron-right"></i></button>`;
    ctrl.innerHTML=h;
}
function goPage(p){
    const total=Math.ceil(filtered.length/perPage)||1;
    if(p<1||p>total)return; currentPage=p; renderTable(); window.scrollTo({top:0,behavior:'smooth'});
}

/* =========================================================
   CHECKBOX / BULK
========================================================= */
function toggleAllCheck(){
    const checked=document.getElementById('checkAll').checked;
    const start=(currentPage-1)*perPage;
    filtered.slice(start,start+perPage).forEach(x=>{checked?selectedIds.add(x.id):selectedIds.delete(x.id);});
    renderTable(); updateBulkBar();
}
function toggleRowCheck(cb){ const id=cb.dataset.id; cb.checked?selectedIds.add(id):selectedIds.delete(id); syncCheckAll(); updateBulkBar(); }
function syncCheckAll(){
    const start=(currentPage-1)*perPage;
    const page=filtered.slice(start,start+perPage);
    const all=page.length>0&&page.every(x=>selectedIds.has(x.id));
    const cb=document.getElementById('checkAll'); if(cb)cb.checked=all;
}
function clearSelection(){ selectedIds.clear(); renderTable(); updateBulkBar(); }
function updateBulkBar(){
    const n=selectedIds.size;
    document.getElementById('bulkCountText').textContent=n;
    document.getElementById('bulkBar').classList.toggle('show',n>0);
}
function bulkRestock(){ showToast('info','Restock Massal','Memproses restock untuk '+selectedIds.size+' barang'); }
function bulkExport(){  showToast('info','Export','Mengekspor '+selectedIds.size+' data barang'); }
function bulkDelete(){
    if(!confirm('Hapus '+selectedIds.size+' barang yang dipilih?'))return;
    selectedIds.forEach(id=>{const i=allItems.findIndex(x=>x.id===id);if(i>-1)allItems.splice(i,1);});
    selectedIds.clear(); applyFilters(); updateStats(); showToast('success','Dihapus','Barang berhasil dihapus');
}

/* =========================================================
   ADD / EDIT MODAL
========================================================= */
function openAddModal(){
    editMode=false; activeItem=null;
    document.getElementById('itemModalIcon').style.background='linear-gradient(135deg,var(--primary),var(--purple))';
    document.getElementById('itemModalTitle').textContent='Tambah Barang';
    document.getElementById('itemModalSub').textContent='Isi detail barang inventaris baru';
    ['f-name','f-code','f-brand','f-desc'].forEach(id=>document.getElementById(id).value='');
    document.getElementById('f-emoji').value='📦';
    document.getElementById('f-stock').value='';
    document.getElementById('f-minStock').value='';
    document.getElementById('f-maxStock').value='';
    document.getElementById('f-price').value='';
    openModal('itemModal');
}
function openEditModal(id){
    const item=allItems.find(x=>x.id===id); if(!item)return;
    editMode=true; activeItem=item;
    document.getElementById('itemModalIcon').style.background=`linear-gradient(135deg,${item.color},${item.color}99)`;
    document.getElementById('itemModalTitle').textContent='Edit Barang';
    document.getElementById('itemModalSub').textContent='Perbarui data barang '+item.name;
    document.getElementById('f-name').value=item.name;
    document.getElementById('f-code').value=item.code;
    document.getElementById('f-brand').value=item.brand;
    document.getElementById('f-desc').value=item.desc||'';
    document.getElementById('f-emoji').value=item.emoji;
    document.getElementById('f-category').value=item.category;
    document.getElementById('f-outlet').value=item.outlet;
    document.getElementById('f-unit').value=item.unit;
    document.getElementById('f-stock').value=item.stock;
    document.getElementById('f-minStock').value=item.minStock;
    document.getElementById('f-maxStock').value=item.maxStock;
    document.getElementById('f-price').value=item.price;
    document.getElementById('f-color').value=item.color;
    openModal('itemModal');
}
function saveItem(){
    const name=document.getElementById('f-name').value.trim();
    if(!name){showToast('error','Validasi','Nama barang wajib diisi');return;}
    const stock=parseInt(document.getElementById('f-stock').value)||0;
    const minS =parseInt(document.getElementById('f-minStock').value)||0;
    const maxS =parseInt(document.getElementById('f-maxStock').value)||100;
    const price=parseInt(document.getElementById('f-price').value)||0;
    const cat  =document.getElementById('f-category').value;
    let status;
    if(stock===0)status='habis';
    else if(stock<minS*.5)status='kritis';
    else if(stock<minS)status='rendah';
    else if(stock>maxS*.8)status='lebih';
    else status='cukup';

    if(editMode&&activeItem){
        Object.assign(activeItem,{name,code:document.getElementById('f-code').value,brand:document.getElementById('f-brand').value,desc:document.getElementById('f-desc').value,emoji:document.getElementById('f-emoji').value,category:cat,outlet:document.getElementById('f-outlet').value,unit:document.getElementById('f-unit').value,stock,minStock:minS,maxStock:maxS,price,value:stock*price,status,color:document.getElementById('f-color').value});
        showToast('success','Diperbarui',name+' berhasil diperbarui');
    } else {
        const newItem={
            id:`INV-${String(2000+allItems.length).padStart(4,'0')}`,name,
            code:document.getElementById('f-code').value||`${cat.slice(0,3).toUpperCase()}-${String(allItems.length).padStart(3,'0')}`,
            brand:document.getElementById('f-brand').value,desc:document.getElementById('f-desc').value,
            emoji:document.getElementById('f-emoji').value||CAT_ICONS[cat]||'📦',
            color:document.getElementById('f-color').value||CAT_COLORS[cat]||'#6366F1',
            category:cat,outlet:document.getElementById('f-outlet').value,
            unit:document.getElementById('f-unit').value,stock,minStock:minS,maxStock:maxS,price,value:stock*price,status,
            lastRestock:new Date().toISOString().slice(0,10),lastRestockQty:0,history:[],
        };
        allItems.unshift(newItem);
        showToast('success','Ditambahkan',name+' berhasil ditambahkan');
    }
    closeModal('itemModal'); applyFilters(); updateStats(); renderChart();
}

/* =========================================================
   DETAIL MODAL
========================================================= */
function openDetail(id){
    const item=allItems.find(x=>x.id===id); if(!item)return;
    activeItem=item;
    document.getElementById('dm-icon').textContent=item.emoji;
    document.getElementById('dm-icon').style.background=item.color+'22';
    document.getElementById('dm-icon').style.fontSize='1.75rem';
    document.getElementById('dm-name').textContent=item.name;
    document.getElementById('dm-code').textContent=item.code+' · '+item.brand;
    document.getElementById('dm-cat').textContent=item.category;
    document.getElementById('dm-brand').textContent=item.brand;
    document.getElementById('dm-outlet').textContent=item.outlet;
    document.getElementById('dm-unit').textContent=item.unit;
    document.getElementById('dm-desc').textContent=item.desc||'Tidak ada deskripsi.';
    document.getElementById('dm-stock').textContent=item.stock+' '+item.unit;
    document.getElementById('dm-stock').style.color=item.status==='kritis'||item.status==='habis'?'var(--danger)':item.status==='rendah'?'var(--warning)':'var(--primary)';
    document.getElementById('dm-status').innerHTML=`<span class="inv-status ${getStockStatusClass(item.status)}">${stockStatusLabel(item.status)}</span>`;
    document.getElementById('dm-min').textContent=item.minStock+' '+item.unit;
    document.getElementById('dm-max').textContent=item.maxStock+' '+item.unit;
    document.getElementById('dm-price').textContent=formatRp(item.price)+' / '+item.unit;
    document.getElementById('dm-value').textContent=formatRp(item.value);
    document.getElementById('dm-last-restock').textContent=item.lastRestock;
    document.getElementById('dm-last-qty').textContent=item.lastRestockQty+' '+item.unit;
    document.getElementById('dm-restock-history').innerHTML=item.history.map(h=>`
        <div class="restock-item">
            <div class="restock-icon"><i class="fas fa-redo-alt"></i></div>
            <div class="restock-info"><div class="restock-title">${h.invoice} · ${h.supplier}</div><div class="restock-date">${h.date}</div></div>
            <div class="restock-qty">+${h.qty} ${item.unit}</div>
        </div>`).join('')||'<div style="color:var(--gray-light);font-size:.875rem">Belum ada riwayat restock.</div>';
    openModal('detailModal');
}
function deleteCurrentItem(){ if(!activeItem)return; deleteById(activeItem.id); closeModal('detailModal'); }
function editCurrentItem(){   if(!activeItem)return; closeModal('detailModal'); openEditModal(activeItem.id); }
function restockCurrentItem(){ if(!activeItem)return; openRestock(activeItem.id); }

function deleteById(id){
    const item=allItems.find(x=>x.id===id); if(!item)return;
    if(!confirm('Hapus '+item.name+' dari inventaris?'))return;
    allItems=allItems.filter(x=>x.id!==id); applyFilters(); updateStats(); renderChart();
    showToast('success','Dihapus',item.name+' berhasil dihapus');
}

/* =========================================================
   RESTOCK MODAL
========================================================= */
function openRestock(id){
    const item=allItems.find(x=>x.id===id); if(!item)return;
    activeItem=item;
    document.getElementById('rs-icon').textContent=item.emoji;
    document.getElementById('rs-name').textContent=item.name;
    document.getElementById('rs-stock').textContent=item.stock+' '+item.unit;
    document.getElementById('rs-stock').className=item.status==='kritis'||item.status==='habis'?'critical':'';
    document.getElementById('rs-min').textContent=item.minStock+' '+item.unit;
    document.getElementById('rs-unit').value=item.unit;
    document.getElementById('rs-price').value=item.price;
    document.getElementById('rs-qty').value='';
    document.getElementById('rs-supplier').value=item.brand;
    document.getElementById('rs-invoice').value='';
    document.getElementById('rs-notes').value='';
    document.getElementById('rs-preview').textContent='—';
    
    // remove previous listeners to avoid double binding
    const qtyInput = document.getElementById('rs-qty');
    const newQtyInput = qtyInput.cloneNode(true);
    qtyInput.parentNode.replaceChild(newQtyInput, qtyInput);
    
    newQtyInput.addEventListener('input', function(){
        const newStock=item.stock+(parseInt(this.value)||0);
        document.getElementById('rs-preview').textContent=newStock+' '+item.unit;
    });
    
    openModal('restockModal');
}
function confirmRestock(){
    const qty=parseInt(document.getElementById('rs-qty').value)||0;
    if(qty<=0){showToast('error','Validasi','Jumlah restock harus lebih dari 0');return;}
    if(!activeItem)return;
    activeItem.stock+=qty;
    activeItem.value=activeItem.stock*activeItem.price;
    activeItem.lastRestock=new Date().toISOString().slice(0,10);
    activeItem.lastRestockQty=qty;
    activeItem.history.unshift({date:activeItem.lastRestock,qty,supplier:document.getElementById('rs-supplier').value,invoice:document.getElementById('rs-invoice').value||'—'});
    // recalculate status
    if(activeItem.stock===0)activeItem.status='habis';
    else if(activeItem.stock<activeItem.minStock*.5)activeItem.status='kritis';
    else if(activeItem.stock<activeItem.minStock)activeItem.status='rendah';
    else if(activeItem.stock>activeItem.maxStock*.8)activeItem.status='lebih';
    else activeItem.status='cukup';
    closeModal('restockModal');
    applyFilters(); updateStats(); renderChart();
    showToast('success','Restock Berhasil',`${activeItem.name} berhasil di-restock +${qty} ${activeItem.unit}`);
}
function openRestockAllModal(){ showToast('info','Restock Otomatis','Memproses restock otomatis untuk semua stok kritis...'); }

/* =========================================================
   MODAL UTILS
========================================================= */
function openModal(id){ document.getElementById(id).classList.add('show'); }
function closeModal(id){ document.getElementById(id).classList.remove('remove'); document.getElementById(id).classList.remove('show'); }
function closeModalOut(e,id){ if(e.target===e.currentTarget)closeModal(id); }
function exportData(){ showToast('info','Export','Mengekspor data inventaris ke Excel...'); }

/* =========================================================
   TOAST
========================================================= */
function showToast(type,title,msg){
    // Try to use global showToast if available
    if (typeof window.showToast === 'function') {
        window.showToast(msg, type, title);
        return;
    }
    const wrap=document.getElementById('toastWrap');
    if (!wrap) return;
    const t=document.createElement('div'); t.className='toast';
    t.innerHTML=`<div class="toast-icon ${type}"><i class="fas fa-${type==='success'?'check':type==='error'?'times':type==='warning'?'exclamation-triangle':'info-circle'}"></i></div><div style="flex:1"><div class="toast-title">${title}</div><div class="toast-msg">${msg}</div></div><button class="toast-x" onclick="this.closest('.toast').remove()"><i class="fas fa-times"></i></button>`;
    wrap.appendChild(t);
    setTimeout(()=>t.classList.add('show'),10);
    setTimeout(()=>{t.classList.remove('show');setTimeout(()=>t.remove(),400);},4500);
}

// Expose functions globally so inline HTML handlers can access them
window.toggleSidebar = function() {
    const sidebar = document.getElementById('sidebar');
    const topbar = document.getElementById('topbar');
    const mainCont = document.getElementById('mainContent');
    if(sidebar) sidebar.classList.toggle('collapsed');
    if(topbar) topbar.classList.toggle('sidebar-collapsed');
    if(mainCont) mainCont.classList.toggle('sidebar-collapsed');
};
window.filterByStat = filterByStat;
window.resetFilters = resetFilters;
window.handleSearch = handleSearch;
window.applyFilters = applyFilters;
window.sortBy = sortBy;
window.changePerPage = changePerPage;
window.toggleAllCheck = toggleAllCheck;
window.toggleRowCheck = toggleRowCheck;
window.clearSelection = clearSelection;
window.bulkRestock = bulkRestock;
window.bulkExport = bulkExport;
window.bulkDelete = bulkDelete;
window.openAddModal = openAddModal;
window.openEditModal = openEditModal;
window.saveItem = saveItem;
window.openDetail = openDetail;
window.deleteCurrentItem = deleteCurrentItem;
window.editCurrentItem = editCurrentItem;
window.restockCurrentItem = restockCurrentItem;
window.deleteById = deleteById;
window.openRestock = openRestock;
window.confirmRestock = confirmRestock;
window.openRestockAllModal = openRestockAllModal;
window.closeModal = closeModal;
window.closeModalOut = closeModalOut;
window.exportData = exportData;
window.showToast = showToast;

/* =========================================================
   FLOAT BUTTON
========================================================= */
window.addEventListener('scroll',()=>{ 
    const btn = document.getElementById('scrollTopBtn');
    if(btn) btn.classList.toggle('visible',window.scrollY>300); 
});
window.scrollToTop = function(event){
    const btn=document.getElementById('scrollTopBtn');
    const r=document.createElement('span');
    r.style.cssText='position:absolute;border-radius:50%;background:rgba(255,255,255,.4);width:52px;height:52px;left:0;top:0;transform:scale(0);animation:pulseBig .6s ease-out';
    btn.appendChild(r); setTimeout(()=>r.remove(),600);
    const start=window.scrollY,t0=performance.now();
    function bounce(t){
        let p=Math.min((t-t0)/800,1),n=7.5625,d=2.75;
        let e;
        if(p<1/d)e=n*p*p;
        else if(p<2/d)e=n*(p-=1.5/d)*p+.75;
        else if(p<2.5/d)e=n*(p-=2.25/d)*p+.9375;
        else e=n*(p-=2.625/d)*p+.984375;
        window.scrollTo(0,start*(1-e));
        if(p<1)requestAnimationFrame(bounce);
    }
    requestAnimationFrame(bounce);
};

/* =========================================================
   INIT
========================================================= */
document.addEventListener('DOMContentLoaded', () => {
    updateStats();
    applyFilters();
    renderChart();
});
