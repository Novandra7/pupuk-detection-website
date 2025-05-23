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
        Schema::create('ms_cctv_sources', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ms_warehouse_id')->nullable();
            $table->string('source_name')->nullable();
            $table->string('url_streaming')->nullable();
            $table->string('endpoint')->nullable();
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();
            $table->boolean('is_active')->nullable();

            $table->foreign('ms_warehouse_id')->references('id')->on('ms_warehouse');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ms_cctv_sources_');
    }
};
