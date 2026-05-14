<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('simulations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->string('name');
            $table->string('template'); // 'microsoft', 'bank', 'hr', 'custom'
            $table->string('subject');
            $table->text('body');
            $table->string('from_name');
            $table->string('from_email');
            $table->string('landing_url')->nullable(); // Page de capture
            $table->string('tracking_token')->unique(); // Token unique pour tracking
            $table->enum('status', ['draft', 'scheduled', 'running', 'completed'])->default('draft');
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->integer('targets_count')->default(0);
            $table->integer('opened_count')->default(0);
            $table->integer('clicked_count')->default(0);
            $table->integer('submitted_count')->default(0);
            $table->integer('reported_count')->default(0);
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('simulations');
    }
};