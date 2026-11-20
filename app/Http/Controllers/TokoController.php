<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Utang;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Throwable;

class TokoController extends Controller
{
    public function index(): View
    {
        return view('toko.index', [
            'barangs' => Barang::orderBy('nama_barang')->get(),
            'utangs' => Utang::where('status', 'belum_lunas')->latest()->get(),
        ]);
    }

    public function storeBarang(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nama_barang' => ['required', 'string', 'max:255'],
            'harga_beli' => ['required', 'numeric', 'min:0'],
            'harga_jual' => ['required', 'numeric', 'min:0'],
            'stok' => ['required', 'integer', 'min:0'],
            'min_stok' => ['nullable', 'integer', 'min:0'],
        ]);

        $barang = Barang::firstOrNew(['nama_barang' => $data['nama_barang']]);
        $barang->harga_beli = $data['harga_beli'];
        $barang->harga_jual = $data['harga_jual'];
        $barang->stok = ($barang->exists ? $barang->stok : 0) + $data['stok'];
        $barang->min_stok = $data['min_stok'] ?? $barang->min_stok ?? 0;
        $barang->save();

        return back()->with('success', 'Barang berhasil disimpan.');
    }

    public function storeTransaksi(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.barang_id' => ['required', 'integer', 'distinct', 'exists:barangs,id'],
            'items.*.jumlah' => ['required', 'integer', 'min:1'],
        ]);

        try {
            $total = DB::transaction(function () use ($data) {
                $total = 0;
                foreach ($data['items'] as $item) {
                    $barang = Barang::whereKey($item['barang_id'])->lockForUpdate()->firstOrFail();
                    if ($barang->stok < $item['jumlah']) {
                        throw new \RuntimeException("Stok {$barang->nama_barang} tidak mencukupi.");
                    }
                    $barang->decrement('stok', $item['jumlah']);
                    $total += $barang->harga_jual * $item['jumlah'];
                }
                return $total;
            });
        } catch (Throwable $exception) {
            return back()->withInput()->withErrors(['items' => $exception->getMessage()]);
        }

        return back()->with('success', "Transaksi berhasil. Total: {$total}");
    }

    public function storeUtang(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nama_pelanggan' => ['required', 'string', 'max:255'],
            'keterangan_barang' => ['required', 'string'],
            'total_utang' => ['required', 'numeric', 'min:0'],
        ]);
        Utang::create($data + ['status' => 'belum_lunas']);
        return back()->with('success', 'Utang berhasil dicatat.');
    }

    public function lunasUtang(Utang $utang): RedirectResponse
    {
        $utang->update(['status' => 'lunas']);
        return back()->with('success', 'Utang ditandai lunas.');
    }
}
