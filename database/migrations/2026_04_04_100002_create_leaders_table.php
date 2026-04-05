<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * LEADERS / E-STRUKTUR
 *
 * Menyimpan data pengurus PCM dan PCW Genteng per periodesasi.
 * `period` dipakai sebagai penanda masa bakti (mis. 2022-2027).
 * `institution_id` opsional — jika amal usaha punya struktur tersendiri,
 * bisa di-link ke institution. NULL berarti pengurus PCM/PCW pusat.
 * `position_level` menegaskan hirarki jabatan untuk sorting tampilan.
 * `bio` dan kolom kontak profesional dipisah dari users table agar
 * data kepemimpinan bisa ditampilkan publik tanpa mengekspos akun login.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leaders', function (Blueprint $table) {
            $table->id();

            // Link ke user jika pengurus juga punya akses sistem (opsional)
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // Link ke lembaga jika ini struktur lembaga, bukan PCM pusat
            $table->foreignId('institution_id')
                ->nullable()
                ->constrained('institutions')
                ->nullOnDelete();

            // Identitas personal
            $table->string('name');
            $table->string('photo')->nullable();
            $table->string('position');               // Ketua, Sekretaris, dll.
            $table->string('division')->nullable();   // Divisi / Majelis
            $table->string('nbm', 20)->nullable();    // Nomor Baku Muhammadiyah

            $table->enum('organization', [
                'pcm',       // Pimpinan Cabang Muhammadiyah
                'pcw',       // Pimpinan Cabang Aisyiyah (wanita)
                'lazismu',   // Lembaga Zakat
                'institution', // Struktur amal usaha
            ])->default('pcm')->index();

            // Level jabatan untuk pengurutan otomatis di e-struktur
            $table->enum('position_level', [
                'leadership',    // Ketua / Ketua Umum
                'vice',          // Wakil Ketua
                'secretary',     // Sekretaris
                'treasurer',     // Bendahara
                'member',        // Anggota / Majelis
            ])->default('member')->index();

            $table->string('period', 20)->index();    // mis. "2022-2027"

            // Kontak profesional (boleh tampil publik)
            $table->string('phone', 20)->nullable();
            $table->string('email')->nullable();

            // Bio singkat
            $table->text('bio')->nullable();

            $table->enum('status', [
                'active',
                'inactive',  // Sudah tidak menjabat / demisioner
            ])->default('active')->index();

            $table->unsignedSmallInteger('order')->default(0);
            $table->timestamps();

            $table->index(['organization', 'period', 'status']);
            $table->index(['institution_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leaders');
    }
};
