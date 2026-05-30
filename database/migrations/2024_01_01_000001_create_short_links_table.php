<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('short_links', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 64)->unique();
            $table->string('destination_url', 2048);
            $table->unsignedBigInteger('click_count')->default(0);
            $table->string('created_ip', 45)->nullable();
            $table->timestamp('expires_at')->nullable()->index();
            $table->timestamps();

            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('short_links');
    }
};
