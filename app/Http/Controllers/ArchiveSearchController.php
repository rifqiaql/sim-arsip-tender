<?php

namespace App\Http\Controllers;

use App\Models\TenderArchive;
use Illuminate\Http\Request;

class ArchiveSearchController extends Controller
{
    public function index(Request $request)
    {
        $query = TenderArchive::query();

        if ($request->filled('keyword')) {
            $keyword = trim($request->keyword);
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                  ->orWhere('tender_ref_no', 'like', "%{$keyword}%")
                  ->orWhere('client_name', 'like', "%{$keyword}%")
                  ->orWhere('document_name', 'like', "%{$keyword}%");
            });
        }

        if ($request->filled('year')) {
            $query->where('year', $request->year);
        }

        if ($request->filled('cabinet_name')) {
            $query->where('cabinet_name', $request->cabinet_name);
        }

        $archives = $query->orderBy('cabinet_name')
                          ->orderBy('rack_position')
                          ->paginate(10)
                          ->withQueryString();

        $availableYears = TenderArchive::whereNotNull('year')->distinct()->orderByDesc('year')->pluck('year');
        $availableCabinets = TenderArchive::distinct()->orderBy('cabinet_name')->pluck('cabinet_name');

        return view('archives.index', compact('archives', 'availableYears', 'availableCabinets'));
    }

    public function importCsv(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt'
        ]);

        $file = $request->file('file');
        $handle = fopen($file->getRealPath(), 'r');
        
        // Deteksi delimiter
        $header = fgetcsv($handle, 1000, ',');
        if ($header && count($header) == 1) {
            rewind($handle);
            $header = fgetcsv($handle, 1000, ';');
            $delimiter = ';';
        } else {
            $delimiter = ',';
        }

        // HANYA KOSONGKAN JIKA USER CENTANG PILIHAN "TIMPA DATA LAMA"
        if ($request->has('replace_old')) {
            \App\Models\TenderArchive::truncate();
        }

        while (($row = fgetcsv($handle, 1000, $delimiter)) !== FALSE) {
            if (empty($row[0]) && isset($row[1])) {
                array_shift($row);
            }

            if (empty($row) || !isset($row[2]) || empty(trim($row[2])) || str_contains(strtolower($row[2]), 'judul pekerjaan')) {
                continue;
            }

            $posisiSimpan = strtolower($row[11] ?? ($row[12] ?? ''));
            $rackPos = str_contains($posisiSimpan, 'bawah') ? 'BAWAH' : 'ATAS';
            $doorPos = str_contains($posisiSimpan, 'kanan') ? 'KANAN' : 'KIRI';

            $yearRaw = $row[7] ?? null;
            $year = is_numeric($yearRaw) ? (int)$yearRaw : null;

            \App\Models\TenderArchive::create([
                'document_name'   => $row[1] ?? 'Dokumen Tender',
                'title'           => trim($row[2]),
                'tender_ref_no'   => !empty($row[3]) ? trim($row[3]) : '-',
                'client_name'     => !empty($row[4]) ? trim($row[4]) : '-',
                'vendor_name'     => !empty($row[5]) ? trim($row[5]) : 'PT Waskita Adhi Sejahtera',
                'location'        => !empty($row[6]) ? trim($row[6]) : '-',
                'year'            => $year,
                'copy_info'       => !empty($row[8]) ? trim($row[8]) : '1 of 1',
                'document_status' => !empty($row[9]) ? trim($row[9]) : 'Original',
                'cabinet_name'    => !empty($row[10]) ? trim($row[10]) : 'Lemari Arsip',
                'rack_position'   => $rackPos,
                'door_position'   => $doorPos,
                'archive_code'    => $row[12] ?? null,
            ]);
        }

        fclose($handle);

        $pesan = $request->has('replace_old') 
            ? 'Data lama berhasil dibersihkan dan diganti dengan data CSV baru!' 
            : 'Data baru dari CSV berhasil ditambahkan ke database!';

        return redirect()->route('archives.index')->with('success', $pesan);
    }
}