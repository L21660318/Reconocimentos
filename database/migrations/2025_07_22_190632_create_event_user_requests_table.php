<?php

// database/migrations/xxxx_xx_xx_create_event_user_requests_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('event_user_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['pendiente', 'aceptado', 'rechazado'])->default('pendiente');
            $table->timestamps();

            $table->unique(['event_id', 'user_id']); // evitar duplicados
        });
    }

    public function down(): void {
        Schema::dropIfExists('event_user_requests');
    }
};
