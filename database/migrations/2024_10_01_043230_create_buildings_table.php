<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('buildings', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('address');
            $table->string('lat');
            $table->string('lng');
            $table->string('building_type');
            $table->foreignId('building_owners_id')->constrained('building_owners')->onDelete('cascade'); // Foreign key to bookings table
            $table->integer('floors')->nullable(); // Nullable in case this info isn't available
            $table->integer('units')->nullable(); // Nullable as some buildings might not have this info
            $table->date('construction_date')->nullable(); // Nullable to allow for buildings without this data
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('buildings');
    }
};
