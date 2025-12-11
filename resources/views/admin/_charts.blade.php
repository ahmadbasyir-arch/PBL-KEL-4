{{-- Partial: resources/views/admin/_charts.blade.php --}}

<style>
    /* ===== FIX PIE CHART MEMANJANG & NO-DATA ===== */
    .chart-card {
        background: #fff;
        border-radius: 16px;
        padding: 20px;
        margin-top: 20px;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
        border: 1px solid rgba(0,0,0,0.03);

        /* perbaikan utama */
        height: 260px;          /* tinggi chart card stabil */
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
    }

    .chart-title {
        font-size: 1rem;
        font-weight: 600;
        color: #374151;
        margin-bottom: 10px;
    }

    .chart-card canvas {
        width: 100% !important;
        height: 100% !important;
        max-height: 220px !important; /* agar tidak panjang */
        object-fit: contain;
        /* beri ruang bawah supaya legend tidak menimpa */
        margin-bottom: 6px;
    }

    .no-data-message {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #6b7280;
        font-weight: 600;
        font-size: 0.98rem;
    }

    /* responsif: di layar sempit, kecilkan tinggi card */
    @media (max-width: 640px) {
        .chart-card { height: 300px; }
    }
</style>

<div class="chart-card" id="card-sarpras">
    <div class="chart-title">Distribusi Jenis Sarpras</div>
    <canvas id="chartSarpras" aria-label="Grafik Distribusi Jenis Sarpras"></canvas>
</div>

<div class="chart-card" id="card-users">
    <div class="chart-title">Distribusi Pengguna Peminjam</div>
    <canvas id="chartUsers" aria-label="Grafik Distribusi Pengguna"></canvas>
</div>

<div class="chart-card" id="card-durasi">
    <div class="chart-title">Durasi Peminjaman (Rentang Jam)</div>
    <canvas id="chartDurasi" aria-label="Grafik Durasi Peminjaman"></canvas>
    {{-- element fallback untuk pesan "tidak ada data" akan dibuat oleh JS bila perlu --}}
</div>

<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>
<script>
(function() {
    // Init Data from Server
    let dataSarpras = @json($chartSarpras ?? ['labels'=>[], 'data'=>[]]);
    let dataUsers   = @json($chartUsers ?? ['labels'=>[], 'data'=>[]]);
    let dataDurasi  = @json($chartDurasi ?? ['labels'=>[], 'data'=>[]]);

    function buildPieChart(ctx, labels, data, optionsExtra = {}) {
        const bg = [
            'rgba(59,130,246,0.85)',
            'rgba(16,185,129,0.85)',
            'rgba(234,88,12,0.85)',
            'rgba(236,72,153,0.85)',
            'rgba(99,102,241,0.85)'
        ];

        const baseOptions = {
            responsive: true,
            maintainAspectRatio: true,
            aspectRatio: 1.4,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        boxWidth: 12,
                        padding: 12
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.label + ': ' + context.formattedValue;
                        }
                    }
                },
                // KONFIGURASI DATALABELS
                datalabels: {
                    color: '#fff',
                    font: {
                        weight: 'bold',
                        size: 14
                    },
                    formatter: function(value, context) {
                        return value > 0 ? value : '';
                    }
                }
            },
            cutout: '50%', // sedikit diperkecil agar text muat
        };

        const finalOptions = Object.assign({}, baseOptions, optionsExtra);

        return new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: bg.slice(0, labels.length),
                    borderWidth: 0
                }]
            },
            options: finalOptions
        });
    }

    function renderOrFallback(canvasId, containerId, labels, data) {
        const sum = data.reduce((s, v) => s + (Number(v) || 0), 0);
        const canvas = document.getElementById(canvasId);
        const container = document.getElementById(containerId);

        // bersihkan pesan fallback
        const existingMsg = container.querySelector('.no-data-message');
        if (existingMsg) existingMsg.remove();

        if (!canvas) return null;

        if (sum <= 0) {
            canvas.style.display = 'none';
            const msg = document.createElement('div');
            msg.className = 'no-data-message';
            msg.innerText = 'Tidak ada data untuk grafik ini.';
            container.appendChild(msg);
            return null;
        } else {
            canvas.style.display = 'block';
            try {
                const ctx = canvas.getContext('2d');

                // Jika sudah ada instance, destroy dulu
                if (window._charts === undefined) window._charts = {};
                if (window._charts[canvasId]) {
                    window._charts[canvasId].destroy();
                    window._charts[canvasId] = null;
                }

                window._charts[canvasId] = buildPieChart(ctx, labels, data);
                return window._charts[canvasId];
            } catch (e) {
                console.error(e);
                canvas.style.display = 'none';
                return null;
            }
        }
    }

    // Fungsi Render Awal
    function initCharts() {
        // Safe Registration
        try {
            if (typeof Chart !== 'undefined' && typeof ChartDataLabels !== 'undefined') {
                Chart.register(ChartDataLabels);
            }
        } catch(e) {}

        renderOrFallback('chartSarpras', 'card-sarpras', dataSarpras.labels || [], dataSarpras.data || []);
        renderOrFallback('chartUsers', 'card-users', dataUsers.labels || [], dataUsers.data || []);
        renderOrFallback('chartDurasi', 'card-durasi', dataDurasi.labels || [], dataDurasi.data || []);
    }

    // REAL-TIME / POLLING (5 detik)
    function startPolling() {
        setInterval(() => {
            fetch(window.location.href, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest', // trigger $request->ajax()
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                // Update Stat Cards
                if(document.getElementById('stat-total')) document.getElementById('stat-total').innerText = data.totalPeminjaman;
                if(document.getElementById('stat-pending')) document.getElementById('stat-pending').innerText = data.totalPending;
                if(document.getElementById('stat-disetujui')) document.getElementById('stat-disetujui').innerText = data.totalDisetujui;
                if(document.getElementById('stat-ditolak')) document.getElementById('stat-ditolak').innerText = data.totalDitolak;
                if(document.getElementById('stat-riwayat')) document.getElementById('stat-riwayat').innerText = data.totalRiwayat;

                // Update Charts
                // Sarpras
                if (JSON.stringify(dataSarpras) !== JSON.stringify(data.chartSarpras)) {
                    dataSarpras = data.chartSarpras;
                    renderOrFallback('chartSarpras', 'card-sarpras', dataSarpras.labels, dataSarpras.data);
                }
                // Users
                if (JSON.stringify(dataUsers) !== JSON.stringify(data.chartUsers)) {
                    dataUsers = data.chartUsers;
                    renderOrFallback('chartUsers', 'card-users', dataUsers.labels, dataUsers.data);
                }
                // Durasi
                if (JSON.stringify(dataDurasi) !== JSON.stringify(data.chartDurasi)) {
                    dataDurasi = data.chartDurasi;
                    renderOrFallback('chartDurasi', 'card-durasi', dataDurasi.labels, dataDurasi.data);
                }
            })
            .catch(err => console.error("Polling error:", err));
        }, 5000); // 5000ms = 5 detik
    }

    document.addEventListener('DOMContentLoaded', function() {
        initCharts();
        startPolling();
    });

})();
</script>
