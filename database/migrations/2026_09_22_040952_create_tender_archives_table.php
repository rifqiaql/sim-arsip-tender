<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tender_archives', function (Blueprint $table) {
            $table->id();
            $table->string('document_name')->nullable(); // Bid Document, PQ Document, ITB
            $table->string('title');                     // Judul Pekerjaan / Proyek
            $table->string('tender_ref_no')->nullable(); // No. RFP / No Tender
            $table->string('client_name');               // Perusahaan Pemilik Proyek (Klien)
            $table->string('vendor_name')->default('PT Waskita Adhi Sejahtera');
            $table->string('location')->nullable();      // Tuban / Surabaya / dsb.
            $table->year('year')->nullable();            // 2023, 2024, 2025, 2026
            $table->string('copy_info')->nullable();     // 1 of 7, 1 of 1, dsb.
            $table->string('document_status')->default('Original'); // Original / Copy
            
            // Kolom Lokasi Fisik Arsip
            $table->string('cabinet_name');              // Lemari Kayu Depan 1, Lemari 3, dsb.
            $table->enum('rack_position', ['ATAS', 'BAWAH']); // Posisi Vertikal
            $table->enum('door_position', ['KIRI', 'KANAN', 'TENGAH'])->default('KIRI'); // Posisi Pintu/Horisontal
            $table->string('archive_code')->nullable();  // LK-A-01, dsb.
            $table->text('notes')->nullable();           // Keterangan tambahan
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tender_archives');
    }
};