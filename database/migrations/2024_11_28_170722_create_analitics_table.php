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
        Schema::create('analitics', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('parent')->nullable();
            $table->string('name')->nullable();
            $table->string('tag_id')->nullable();
            $table->string('data_value')->nullable();
            $table->json('graph_type')->nullable();
            $table->integer('hassubmenu')->default(0)->nullable();
            $table->tinyInteger('status')->default(1)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('analitics');
    }
};
