<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->enum('role', ['user', 'admin'])->default('user');
            $table->string('department')->nullable();
            $table->integer('vigilance_score')->default(0);
            $table->integer('reports_count')->default(0);
            $table->integer('simulations_passed')->default(0);
            $table->integer('simulations_failed')->default(0);
            $table->boolean('is_active')->default(true);
            $table->string('avatar')->nullable();
            $table->string('locale')->default('fr');
            $table->timestamp('last_login_at')->nullable();
            $table->string('last_login_ip')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};