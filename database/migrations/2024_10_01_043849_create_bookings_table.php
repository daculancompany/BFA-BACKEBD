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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();

            // Foreign key to the personnel table
            $table->foreignId('personnel_id')
                ->nullable()  // Allow personnel to be null initially
                ->constrained('personnel')  // Assuming 'personnel' is the name of the table
                ->onDelete('set null');  // Set to null if the personnel is deleted

            // Foreign key to the building table (non-nullable)
            $table->foreignId('buildings_id')
                ->default(1)  // Set a default building ID, replace '1' with a valid default ID
                ->constrained('buildings')
                ->onDelete('cascade');

            // Address column
            $table->string('address')->nullable(); // Address can be null

            // Enum to track if this is a permit or a survey
            $table->enum('type', ['survey']);

            // Make 'appointment_date' nullable (timestamp for full date and time)
            $table->timestamp('appointment_date')->nullable();

            // Enum to track the status of the booking, including 'deployed'
            $table->enum('status', ['pending', 'approved', 'canceled', 'deployed'])
                ->default('pending');  // Default status is pending

            // Foreign key to track the admin who approved the booking (nullable)
            $table->foreignId('approved_by_admin_id')
                ->nullable()  // Admin approval can be null initially
                ->constrained('users')  // 'users' table is referenced for admin
                ->onDelete('set null');  // Set to null if the admin is deleted

            // Timestamps for created_at and updated_at
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
