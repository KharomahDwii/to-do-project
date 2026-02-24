<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    // Jika ingin kategori dinamis disimpan di tabel terpisah, buat tabel 'categories'
    // Namun, berdasarkan kode Anda sebelumnya yang menggunakan metadata, kita bisa simpan list kategori custom di user_settings atau tabel terpisah.
    // Untuk solusi sederhana & cepat sesuai struktur Anda, kita akan buat tabel pivot atau kolom JSON di users, 
    // TAPI cara terbaik adalah membuat tabel master kecil.
    
    Schema::create('custom_categories', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->string('name');
        $table->string('color')->default('gray'); // hex code atau tailwind class
        $table->timestamps();
    });

    Schema::create('custom_pjs', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->string('name');
        $table->string('role')->nullable(); // Jabatan opsional
        $table->string('photo_path')->nullable();
        $table->timestamps();
    });

    // Tambahkan kolom foreign key ke tabel todos jika ingin relasi resmi, 
    // atau tetap gunakan metadata['category_id'] dan metadata['pj_id']
    Schema::table('todos', function (Blueprint $table) {
        $table->foreignId('category_id')->nullable()->constrained('custom_categories')->nullOnDelete();
        $table->foreignId('pj_id')->nullable()->constrained('custom_pjs')->nullOnDelete();
    });
}
};
