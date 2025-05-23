<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tr_fertilizer_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ms_cctv_sources_id')->nullable();
            $table->unsignedBigInteger('ms_shift_id')->nullable();
            $table->unsignedBigInteger('ms_bag_id');
            $table->integer('quantity');
            $table->timestamp('timestamp')->nullable();

            $table->foreign('ms_cctv_sources_id')->references('id')->on('ms_cctv_sources');
            $table->foreign('ms_shift_id')->references('id')->on('ms_shift');
            $table->foreign('ms_bag_id')->references('id')->on('ms_bag');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tr_fertilizer_records');
    }
};
