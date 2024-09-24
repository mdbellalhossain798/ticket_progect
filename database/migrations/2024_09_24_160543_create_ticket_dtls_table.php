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
        Schema::create('ticket_dtls', function (Blueprint $table) {
            $table->id();
            $table->string('reply_details',5000);
            $table->foreignId('ticket_mst_id')->constrained('ticketMsts')->onDelete('cascade');   
            $table->unsignedBigInteger('reply_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_dtls');
    }
};
