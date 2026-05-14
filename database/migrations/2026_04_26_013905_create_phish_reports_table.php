<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('phish_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['url', 'email', 'sms', 'other']);
            $table->text('content'); // URL ou corps de l'email
            $table->string('subject')->nullable(); // Objet de l'email
            $table->string('sender_email')->nullable();
            $table->string('sender_ip')->nullable();
            $table->text('email_headers')->nullable(); // Headers bruts pour forensic
            $table->text('ai_analysis')->nullable(); // Résultat analyse IA
            $table->integer('ai_risk_score')->nullable(); // 0-100
            $table->json('virustotal_result')->nullable();
            $table->enum('status', ['pending', 'analyzing', 'confirmed_phish', 'false_positive', 'blocked'])->default('pending');
            $table->enum('severity', ['low', 'medium', 'high', 'critical'])->nullable();
            $table->text('admin_feedback')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('phish_reports');
    }
};