<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->timestamps();
        });

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('email', 255)->unique();
            $table->string('password', 255);
            $table->boolean('is_admin')->default(false);
            $table->foreignId('status_id')->nullable()->default(1)->constrained('statuses')->onDelete('set null');
            $table->timestamps();
        });

        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->date('date');
            $table->time('work_start')->nullable();
            $table->time('work_end')->nullable();
            $table->foreignId('status_id')->nullable()->constrained('statuses')->nullOnDelete();
            $table->text('note')->nullable();
            $table->boolean('is_pending')->default(false);
            $table->timestamps();
            $table->unique(['user_id', 'date']);
        });

        Schema::create('rests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attendance_id')->constrained('attendances')->onDelete('cascade');
            $table->time('start');
            $table->time('end')->nullable();
            $table->timestamps();
        });

        Schema::create('stamp_correction_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('attendance_id')->constrained('attendances')->onDelete('cascade');
            $table->string('reason', 255);
            $table->string('status', 255);
            $table->timestamp('requested_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_months');
        Schema::dropIfExists('stamp_correction_requests');
        Schema::dropIfExists('rests');
        Schema::dropIfExists('attendances');
        Schema::dropIfExists('users');
        Schema::dropIfExists('statuses');
    }
};
