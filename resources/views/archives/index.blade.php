    <!DOCTYPE html>
    <html lang="id">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Sistem Temu Kembali Arsip Tender - PT Waskita Adhi Sejahtera</title>
        <!-- Tailwind CSS CDN -->
        <script src="https://cdn.tailwindcss.com"></script>
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    </head>

    <body class="bg-slate-100 text-slate-800 min-h-screen">

        <!-- Header Navbar -->
        <header class="bg-slate-900 text-white shadow-md">
            <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-orange-600 rounded flex items-center justify-center font-bold text-xl">W</div>
                    <div>
                        <h1 class="text-lg font-bold tracking-tight">Sistem Temu Kembali Arsip Dokumen Tender</h1>
                        <p class="text-xs text-slate-400">PT Waskita Adhi Sejahtera</p>
                    </div>
                </div>
                <span class="text-xs bg-slate-800 border border-slate-700 px-3 py-1.5 rounded-full text-slate-300">
                    <i class="fa-solid fa-folder-closed text-orange-400 mr-1.5"></i> Modul Pencarian Rak Fisik
                </span>
            </div>
        </header>

        <main class="max-w-7xl mx-auto px-4 py-8">
            <!-- Notifikasi Sukses -->
            @if(session('success'))
            <div class="mb-4 p-4 bg-emerald-100 border border-emerald-300 text-emerald-800 rounded-lg text-sm flex items-center">
                <i class="fa-solid fa-circle-check text-emerald-600 mr-2 text-base"></i>
                <span>{{ session('success') }}</span>
            </div>
            @endif

            <!-- Kotak Form Upload CSV dengan Opsi Timpa -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 mb-6 flex flex-col md:flex-row justify-between items-center gap-4">
            <div class="text-xs text-slate-600">
                <span class="font-bold text-slate-800 block text-sm mb-0.5">
                    <i class="fa-solid fa-file-csv text-emerald-600 mr-1.5"></i> Import Data Berkas Otomatis
                </span>
                Unggah file <strong>.csv</strong> untuk menambahkan atau memperbarui data arsip rak.
            </div>
            <form action="{{ route('archives.import.csv') }}" method="POST" enctype="multipart/form-data" class="flex flex-wrap items-center gap-3">
                @csrf
                <input type="file" name="file" accept=".csv" required class="text-xs text-slate-600 file:mr-2 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 cursor-pointer">
                
                <!-- Checkbox Pilihan Timpa -->
                <label class="flex items-center space-x-1.5 text-xs text-slate-600 cursor-pointer select-none bg-slate-50 px-2.5 py-1.5 rounded border border-slate-200 hover:bg-slate-100">
                    <input type="checkbox" name="replace_old" value="1" class="rounded text-orange-600 focus:ring-orange-500">
                    <span class="text-slate-700 font-medium">Kosongkan data lama (Timpa)</span>
                </label>

                <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold px-4 py-2 rounded-lg transition duration-150 flex items-center gap-1.5 shadow-sm">
                    <i class="fa-solid fa-upload"></i>
                    <span>Upload CSV</span>
                </button>
            </form>
        </div>            <!-- Form Pencarian & Filter -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 mb-8">
                <form method="GET" action="{{ route('archives.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">

                    <!-- Keyword Input -->
                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1">Cari Dokumen / Proyek</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                                <i class="fa-solid fa-magnifying-glass"></i>
                            </span>
                            <input type="text" name="keyword" value="{{ request('keyword') }}"
                                placeholder="Ketik judul proyek, klien, atau nomor tender (RFP)..."
                                class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-orange-500 text-sm">
                        </div>
                    </div>

                    <!-- Filter Tahun -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1">Periode / Tahun</label>
                        <select name="year" class="w-full px-3 py-2.5 rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-orange-500 text-sm bg-white">
                            <option value="">Semua Tahun</option>
                            @foreach($availableYears as $year)
                            <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>{{ $year }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Tombol Aksi -->
                    <div class="flex items-end space-x-2">
                        <button type="submit" class="w-full bg-orange-600 hover:bg-orange-700 text-white font-medium py-2.5 px-4 rounded-lg transition duration-150 text-sm flex items-center justify-center space-x-2 shadow-sm">
                            <i class="fa-solid fa-search"></i>
                            <span>Cari Lokasi</span>
                        </button>
                        @if(request()->anyFilled(['keyword', 'year', 'cabinet_name']))
                        <a href="{{ route('archives.index') }}" class="px-3 py-2.5 bg-slate-200 hover:bg-slate-300 text-slate-700 rounded-lg text-sm transition" title="Reset Filter">
                            <i class="fa-solid fa-rotate-left"></i>
                        </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Hasil Pencarian -->
            <div class="mb-4 flex justify-between items-center">
                <h2 class="text-sm font-bold text-slate-700 uppercase tracking-wide">
                    Hasil Pencarian: <span class="text-orange-600 font-extrabold">{{ $archives->total() }}</span> Berkas Ditemukan
                </h2>
            </div>

            @if($archives->isEmpty())
            <div class="bg-white rounded-xl border border-dashed border-slate-300 p-12 text-center">
                <i class="fa-solid fa-box-open text-4xl text-slate-300 mb-3"></i>
                <h3 class="text-base font-semibold text-slate-700">Dokumen Tidak Ditemukan</h3>
                <p class="text-sm text-slate-500 mt-1">Coba periksa kembali ejaan judul, nomor referensi tender, atau reset filter.</p>
            </div>
            @else
            <div class="grid grid-cols-1 gap-6">
                @foreach($archives as $archive)
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden hover:border-orange-300 transition duration-200">
                    <div class="p-6 grid grid-cols-1 lg:grid-cols-12 gap-6 items-center">

                        <!-- Kolom Detail Dokumen (8 Cols) -->
                        <div class="lg:col-span-8 space-y-2">
                            <div class="flex items-center space-x-2 flex-wrap gap-y-1">
                                <span class="text-xs font-bold uppercase tracking-wider px-2 py-0.5 rounded bg-blue-100 text-blue-800">
                                    {{ $archive->document_name ?? 'Dokumen Tender' }}
                                </span>
                                <span class="text-xs px-2 py-0.5 rounded {{ $archive->document_status == 'Original' ? 'bg-emerald-100 text-emerald-800 font-bold' : 'bg-slate-100 text-slate-700' }}">
                                    {{ $archive->document_status }} ({{ $archive->copy_info }})
                                </span>
                                @if($archive->year)
                                <span class="text-xs font-semibold px-2 py-0.5 rounded bg-slate-100 text-slate-700">
                                    Tahun: {{ $archive->year }}
                                </span>
                                @endif
                            </div>

                            <h3 class="text-base font-bold text-slate-900 leading-snug">
                                {{ $archive->title }}
                            </h3>

                            <div class="text-xs text-slate-600 space-y-1">
                                <p><strong class="text-slate-800">No. Referensi / RFP:</strong> {{ $archive->tender_ref_no ?: '-' }}</p>
                                <p><strong class="text-slate-800">Klien / Pemilik Proyek:</strong> {{ $archive->client_name }}</p>
                                @if($archive->notes)
                                <p class="text-slate-500 italic"><strong class="text-slate-700 not-italic">Catatan:</strong> {{ $archive->notes }}</p>
                                @endif
                            </div>
                        </div>

                        <!-- Kolom Visualisasi Lokasi Fisik Rak (4 Cols) -->
                        <div class="lg:col-span-4 bg-slate-50 border border-slate-200 rounded-lg p-3">
                            <div class="text-xs font-bold text-slate-700 mb-1 flex justify-between items-center">
                                <span><i class="fa-solid fa-location-dot text-red-500 mr-1"></i> POSISI RAK:</span>
                                <span class="text-orange-600 font-mono font-bold">{{ $archive->archive_code ?? '-' }}</span>
                            </div>

                            <div class="text-[11px] font-semibold text-slate-600 mb-2">
                                {{ $archive->cabinet_name }}
                            </div>

                            <!-- Simulasi Lemari 2D Interaktif -->
                            <div class="border-2 border-slate-700 rounded bg-slate-200 p-1 flex flex-col gap-1 shadow-inner">
                                <!-- Rak Atas -->
                                <div class="grid grid-cols-2 gap-1 text-[10px] text-center font-bold">
                                    <!-- Atas Kiri -->
                                    <div class="py-2.5 rounded border border-slate-300 {{ ($archive->rack_position == 'ATAS' && $archive->door_position == 'KIRI') ? 'bg-orange-500 text-white ring-2 ring-orange-300 animate-pulse' : 'bg-white text-slate-400' }}">
                                        ATAS - KIRI
                                    </div>
                                    <!-- Atas Kanan -->
                                    <div class="py-2.5 rounded border border-slate-300 {{ ($archive->rack_position == 'ATAS' && $archive->door_position == 'KANAN') ? 'bg-orange-500 text-white ring-2 ring-orange-300 animate-pulse' : 'bg-white text-slate-400' }}">
                                        ATAS - KANAN
                                    </div>
                                </div>
                                <!-- Sekat Tengah -->
                                <div class="h-1 bg-slate-700 rounded-full"></div>
                                <!-- Rak Bawah -->
                                <div class="grid grid-cols-2 gap-1 text-[10px] text-center font-bold">
                                    <!-- Bawah Kiri -->
                                    <div class="py-2.5 rounded border border-slate-300 {{ ($archive->rack_position == 'BAWAH' && $archive->door_position == 'KIRI') ? 'bg-orange-500 text-white ring-2 ring-orange-300 animate-pulse' : 'bg-white text-slate-400' }}">
                                        BAWAH - KIRI
                                    </div>
                                    <!-- Bawah Kanan -->
                                    <div class="py-2.5 rounded border border-slate-300 {{ ($archive->rack_position == 'BAWAH' && $archive->door_position == 'KANAN') ? 'bg-orange-500 text-white ring-2 ring-orange-300 animate-pulse' : 'bg-white text-slate-400' }}">
                                        BAWAH - KANAN
                                    </div>
                                </div>
                            </div>

                            <div class="mt-2 text-center text-[10px] text-slate-600 font-medium">
                                Buka: <strong>Pintu {{ $archive->door_position }}</strong> &bull; Rak <strong>{{ $archive->rack_position }}</strong>
                            </div>
                        </div>

                    </div>
                </div>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $archives->links() }}
            </div>
            @endif

        </main>

    </body>

    </html>