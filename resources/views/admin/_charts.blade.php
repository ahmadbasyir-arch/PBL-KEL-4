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

<script>
(function() {

    const chartSarpras = @json($chartSarpras ?? ['labels'=>[], 'data'=>[]]);
    const chartUsers   = @json($chartUsers ?? ['labels'=>[], 'data'=>[]]);
    const chartDurasi  = @json($chartDurasi ?? ['labels'=>[], 'data'=>[]]);

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
                }
            },
            cutout: '55%',
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

        // bersihkan pesan fallback sebelumnya (jika ada)
        const existingMsg = container.querySelector('.no-data-message');
        if (existingMsg) existingMsg.remove();

        if (!canvas) return; // safety

        if (sum <= 0) {
            // sembunyikan canvas dan tampilkan pesan "Tidak ada data"
            canvas.style.display = 'none';

            const msg = document.createElement('div');
            msg.className = 'no-data-message';
            msg.innerText = 'Tidak ada data untuk grafik ini.';
            container.appendChild(msg);
            return null;
        } else {
            // pastikan canvas ditampilkan
            canvas.style.display = 'block';
            try {
                // inisialisasi chart
                const ctx = canvas.getContext('2d');

                // clear any previously created chart instance on this canvas (if Chart.instances available)
                // (this reduces duplicates if partial re-render)
                if (window._charts === undefined) window._charts = {};
                if (window._charts[canvasId]) {
                    try { window._charts[canvasId].destroy(); } catch(e){}
                    window._charts[canvasId] = null;
                }

                window._charts[canvasId] = buildPieChart(ctx, labels, data);
                return window._charts[canvasId];
            } catch (e) {
                // fallback: tampilkan pesan error kecil
                canvas.style.display = 'none';
                const msg = document.createElement('div');
                msg.className = 'no-data-message';
                msg.innerText = 'Gagal menampilkan grafik.';
                container.appendChild(msg);
                return null;
            }
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Sarpras
        try {
            renderOrFallback('chartSarpras', 'card-sarpras', chartSarpras.labels || [], chartSarpras.data || []);
        } catch (e) { console.error(e); }

        // Users
        try {
            renderOrFallback('chartUsers', 'card-users', chartUsers.labels || [], chartUsers.data || []);
        } catch (e) { console.error(e); }

        // Durasi (catatan: mungkin semua 0)
        try {
            renderOrFallback('chartDurasi', 'card-durasi', chartDurasi.labels || [], chartDurasi.data || []);
        } catch (e) { console.error(e); }
    });

})();
</script>
