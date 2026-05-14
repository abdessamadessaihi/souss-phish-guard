<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('simulation_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('simulation_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('unique_token'); // Token propre à ce user pour ce sim
            $table->boolean('email_opened')->default(false);
            $table->boolean('link_clicked')->default(false);
            $table->boolean('data_submitted')->default(false);
            $table->boolean('reported_phish')->default(false);
            $table->timestamp('opened_at')->nullable();
            $table->timestamp('clicked_at')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('reported_at')->nullable();
            $table->string('user_ip')->nullable();
            $table->string('user_agent')->nullable();
            $table->enum('outcome', ['safe', 'clicked', 'submitted', 'reported'])->default('safe');
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('simulation_results');
    }
};