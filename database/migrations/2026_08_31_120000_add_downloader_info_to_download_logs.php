<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('download_logs', function (Blueprint $table) {
            $table->string('downloaded_by_name')->nullable()->after('user_id');
            $table->string('downloaded_by_role')->nullable()->after('downloaded_by_name');
        });
    }

    public function down(): void
    {
        Schema::table('download_logs', function (Blueprint $table) {
            $table->dropColumn(['downloaded_by_name', 'downloaded_by_role']);
        });
    }
};
