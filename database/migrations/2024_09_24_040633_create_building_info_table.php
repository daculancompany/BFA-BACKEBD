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
        Schema::create('building_info', function (Blueprint $table) {
            $table->id();

            // Foreign keys to related tables (optional, depending on your requirements)
            $table->foreignId('personnel_id')->nullable()->constrained('personnel')->onDelete('cascade');
            $table->foreignId('booking_id')->nullable()->constrained('bookings')->onDelete('cascade');
            // New fields based on form input
            $table->string('inspection_order_no'); // Optional: Add `nullable()` if required
            $table->date('date_issued'); // Optional: Add `nullable()` if required
            $table->date('date_inspected'); // Optional: Add `nullable()` if required
            $table->string('building_name'); // Name of the building
            $table->string('address'); // Address
            $table->string('business_name'); // Business name
            $table->string('nature_of_business'); // Nature of business
            $table->string('owner_name'); // Name of owner/representative
            $table->string('contact_no'); // Contact number

            // New fields added
            $table->string('fsec_no'); // FSEC number
            $table->string('building_permit'); // Building permit number
            $table->string('fsic_no'); // FSIC number
            $table->string('business_permit_no'); // Business permit number
            $table->string('fire_insurance_no'); // Fire insurance number
            $table->boolean('inspection_during_construction')->default(0);
            $table->boolean('fsic_occupancy')->default(0);
            $table->boolean('fsic_new_permit')->default(0);
            $table->boolean('fsic_renew_permit')->default(0);
            $table->boolean('ntc')->default(0);
            $table->boolean('ntcv')->default(0);
            $table->boolean('abatement')->default(0);
            $table->boolean('closure')->default(0);
            $table->boolean('disapproval')->default(0);
            $table->string('others');
            $table->boolean('fsic_annual_inspection')->default(0);
            $table->boolean('verification_inspection')->default(0);

            $table->boolean('mercantile')->default(0);
            $table->boolean('business')->default(0);
            $table->boolean('reinforcedconcrete')->default(0);
            $table->boolean('timberframedwalls')->default(0);
            $table->boolean('reinforced')->default(0);
            $table->boolean('steel')->default(0);
            $table->boolean('mixed')->default(0);
            // New fields added


            $table->timestamps(); // Timestamps for created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('building_info');
    }
};
