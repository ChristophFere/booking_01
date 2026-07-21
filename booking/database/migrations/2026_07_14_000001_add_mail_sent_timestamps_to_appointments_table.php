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
        Schema::table('appointments', function (Blueprint $table) {
            $table->timestamp('confirmation_mail_sent_at')->nullable()->after('cancelled_at');
            $table->timestamp('cancellation_mail_sent_at')->nullable()->after('confirmation_mail_sent_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn(['confirmation_mail_sent_at', 'cancellation_mail_sent_at']);
        });
    }
};
