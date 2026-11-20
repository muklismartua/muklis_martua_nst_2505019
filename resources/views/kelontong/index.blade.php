<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KelontongKu - Kasir Warung</title>
    <style>
        :root { --primary:#1769aa; --success:#16803c; --warning:#b45309; --danger:#b42318; --bg:#f4f7fb; --text:#172033; --muted:#64748b; --line:#dbe3ee; }
        * { box-sizing:border-box; }
        body { margin:0; font-family:Arial, sans-serif; color:var(--text); background:var(--bg); }
        header { padding:28px 5%; color:white; background:linear-gradient(135deg,#155e91,#1d8a72); }
        header h1 { margin:0 0 6px; font-size:clamp(28px,4vw,42px); }
        header p { margin:0; font-size:17px; opacity:.9; }
        main { width:min(1200px,92%); margin:26px auto 48px; display:grid; gap:24px; }
        .card { background:white; border:1px solid var(--line); border-radius:16px; padding:24px; box-shadow:0 8px 24px rgba(23,32,51,.06); }
        .card h2 { margin:0 0 5px; font-size:24px; }
        .card > p { margin:0 0 20px; color:var(--muted); }
        .grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:14px; }
        label { display:block; font-weight:700; margin-bottom:7px; }
        input, select, textarea { width:100%; border:1px solid #bdc9d8; border-radius:9px; padding:12px; font:inherit; }
        textarea { min-height:86px; resize:vertical; }
        .table-wrap { overflow-x:auto; }
        table { width:100%; border-collapse:collapse; min-width:620px; }
        th, td { padding:13px 12px; text-align:left; border-bottom:1px solid var(--line); vertical-align:middle; }
        th { background:#f7f9fc; font-size:14px; }
        td input { max-width:120px; }
        .button { border:0; border-radius:9px; padding:13px 18px; color:white; background:var(--primary); font-weight:700; font-size:16px; cursor:pointer; }
        .button:hover { filter:brightness(.92); }
        .button.success { background:var(--success); }
        .button.warning { background:var(--warning); }
        .button.small { padding:9px 13px; font-size:14px; }
        .actions { margin-top:18px; display:flex; gap:10px; flex-wrap:wrap; align-items:center; }
        .total { margin-left:auto; font-size:21px; font-weight:700; }
        .notice { padding:13px 16px; border-radius:9px; background:#e8f5ed; color:#146c36; font-weight:700; }
        .errors { padding:13px 16px; border-radius:9px; background:#fff0ee; color:var(--danger); }
        .empty { color:var(--muted); text-align:center; padding:24px; }
        @media (max-width:700px) { .grid { grid-template-columns:1fr; } .card { padding:18px; } .total { width:100%; margin-left:0; } }
    </style>
</head>
<body>
<header>
    <h1>KelontongKu</h1>
    <p>Kasir sederhana untuk mengelola penjualan, stok, dan utang warung.</p>
</header>
<main>
    @if(session('success')) <div class="notice">{{ session('success') }}</div> @endif
    @if($errors->any()) <div class="errors">{{ $errors->first() }}</div> @endif

    <section class="card">
        <h2>1. Kasir / Transaksi</h2>
        <p>Pilih jumlah barang yang dibeli. Biarkan 0 untuk barang yang tidak dibeli.</p>
        <form method="POST" action="{{ route('transaksi.store') }}" id="transaction-form">
            @csrf
            <div class="table-wrap"><table>
                <thead><tr><th>Barang</th><th>Harga Jual</th><th>Stok</th><th>Qty Beli</th><th>Subtotal</th></tr></thead>
                <tbody>
                @forelse($barangs as $barang)
                    <tr data-price="{{ $barang->harga_jual }}">
                        <td>{{ $barang->nama_barang }}</td>
                        <td>Rp {{ number_format($barang->harga_jual, 0, ',', '.') }}</td>
                        <td>{{ $barang->stok }}</td>
                        <td><input type="hidden" name="items[{{ $loop->index }}][barang_id]" value="{{ $barang->id }}"><input class="qty" type="number" name="items[{{ $loop->index }}][jumlah]" value="0" min="0" max="{{ $barang->stok }}" aria-label="Jumlah {{ $barang->nama_barang }}"></td>
                        <td class="subtotal">Rp 0</td>
                    </tr>
                @empty <tr><td colspan="5" class="empty">Belum ada barang.</td></tr> @endforelse
                </tbody>
            </table></div>
            <div class="actions"><button class="button" type="submit">Simpan Transaksi</button><span class="total">Total Belanja: <span id="total">Rp 0</span></span></div>
        </form>
    </section>

    <section class="card">
        <h2>2. Daftar Barang & Barang Masuk</h2>
        <p>Tambah stok barang lama atau masukkan barang baru.</p>
        <form method="POST" action="{{ route('barang.store') }}">
            @csrf
            <div class="grid">
                <div><label for="nama_barang">Nama barang</label><input id="nama_barang" name="nama_barang" required placeholder="Contoh: Mi instan"></div>
                <div><label for="stok">Jumlah stok masuk</label><input id="stok" name="stok" type="number" min="0" required placeholder="Contoh: 12"></div>
                <div><label for="harga_beli">Harga beli</label><input id="harga_beli" name="harga_beli" type="number" min="0" step="0.01" required></div>
                <div><label for="harga_jual">Harga jual</label><input id="harga_jual" name="harga_jual" type="number" min="0" step="0.01" required></div>
                <div><label for="min_stok">Batas stok minimum</label><input id="min_stok" name="min_stok" type="number" min="0" value="0"></div>
            </div>
            <div class="actions"><button class="button success" type="submit">Simpan Barang / Tambah Stok</button></div>
        </form>
        <div class="table-wrap" style="margin-top:22px"><table>
            <thead><tr><th>Nama Barang</th><th>Harga Beli</th><th>Harga Jual</th><th>Stok</th><th>Status</th></tr></thead>
            <tbody>@forelse($barangs as $barang)<tr><td>{{ $barang->nama_barang }}</td><td>Rp {{ number_format($barang->harga_beli, 0, ',', '.') }}</td><td>Rp {{ number_format($barang->harga_jual, 0, ',', '.') }}</td><td>{{ $barang->stok }}</td><td>{{ $barang->stok <= $barang->min_stok ? 'Perlu restok' : 'Aman' }}</td></tr>@empty<tr><td colspan="5" class="empty">Belum ada barang.</td></tr>@endforelse</tbody>
        </table></div>
    </section>

    <section class="card">
        <h2>3. Daftar Utang Pelanggan</h2>
        <p>Catat utang baru, lalu tekan tombol Lunas saat pembayaran diterima.</p>
        <form method="POST" action="{{ route('utang.store') }}">
            @csrf
            <div class="grid"><div><label for="nama_pelanggan">Nama pelanggan</label><input id="nama_pelanggan" name="nama_pelanggan" required></div><div><label for="total_utang">Total utang</label><input id="total_utang" name="total_utang" type="number" min="0" step="0.01" required></div><div style="grid-column:1/-1"><label for="keterangan_barang">Keterangan barang</label><textarea id="keterangan_barang" name="keterangan_barang" required placeholder="Contoh: Beras 5 kg, minyak 1 liter"></textarea></div></div>
            <div class="actions"><button class="button warning" type="submit">Simpan Utang</button></div>
        </form>
        <div class="table-wrap" style="margin-top:22px"><table><thead><tr><th>Pelanggan</th><th>Keterangan</th><th>Total Utang</th><th>Aksi</th></tr></thead><tbody>@forelse($utangs as $utang)<tr><td>{{ $utang->nama_pelanggan }}</td><td>{{ $utang->keterangan_barang }}</td><td>Rp {{ number_format($utang->total_utang, 0, ',', '.') }}</td><td><form method="POST" action="{{ route('utang.lunas', $utang) }}">@csrf @method('PATCH')<button class="button success small" type="submit">Lunas</button></form></td></tr>@empty<tr><td colspan="4" class="empty">Tidak ada utang aktif.</td></tr>@endforelse</tbody></table></div>
    </section>
</main>
<script>
    const formatRupiah = value => 'Rp ' + new Intl.NumberFormat('id-ID').format(value);

    function updateTotalBelanja() {
        let total = 0;

        document.querySelectorAll('#transaction-form tbody tr[data-price]').forEach(row => {
            const qtyInput = row.querySelector('.qty');
            const qty = Math.max(0, Number(qtyInput.value) || 0);
            const subtotal = qty * Number(row.dataset.price);

            total += subtotal;
            row.querySelector('.subtotal').textContent = formatRupiah(subtotal);
        });

        document.getElementById('total').textContent = formatRupiah(total);
    }

    document.querySelectorAll('#transaction-form .qty').forEach(input => {
        input.addEventListener('input', updateTotalBelanja);
        input.addEventListener('change', updateTotalBelanja);
    });

    updateTotalBelanja();
</script>
</body>
</html>
