<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tabel penyimpanan Passkey (login wajah / sidik jari via WebAuthn).
 * Yang disimpan hanya KUNCI PUBLIK perangkat — tidak ada foto atau data wajah.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('passkeys')) {
            return;
        }

        Schema::create('passkeys', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name', 100);
            $table->string('credential_id', 512)->unique();
            $table->text('public_key');
            $table->integer('algorithm');
            $table->unsignedBigInteger('sign_count')->default(0);
            $table->json('transports')->nullable();
            $table->string('rp_id', 255)->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('passkeys');
    }
};
