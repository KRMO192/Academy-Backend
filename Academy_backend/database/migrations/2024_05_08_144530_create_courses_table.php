<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Carbon;
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('title', 100)->unique();
            $table->longtext('description')->unique();
            $table->string('course_image')->nullable();
            $table->string('start_at')->nullable()->default(Carbon::now());
            $table->string('end_at')->nullable()->default(Carbon::now());
            $table->unsignedBigInteger('instructor_id');
            $table->enum('status', [ 'active', 'ended'])->nullable();
            $table->softDeletes();
            $table->timestamps();
            $table->foreign('instructor_id')->references('id')->on('instructors')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
