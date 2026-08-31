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
        Schema::create('repositories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Metadata kulingana na SRS FR-2.1
            $table->string('title');
            $table->text('abstract');
            $table->string('authors');
            $table->string('supervisor');
            $table->string('department');
            $table->integer('year');
            $table->string('degree_programme');
            $table->string('keywords');
            $table->string('document_type');
            
            // Faili na Status (FR-2.2 & FR-2.3)
            $table->string('file_path');
            $table->enum('status', ['pending_supervisor', 'pending_library', 'approved', 'rejected', 'revision_requested'])->default('pending_supervisor');
            $table->enum('access_level', ['open_access', 'institution_only', 'restricted'])->default('open_access');
            $table->string('accession_number')->nullable()->unique();
            
            $table->softDeletes(); // Hapa ndipo ilipoongezwa kwa ajili ya Soft Deletes/Restore
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('repositories');
    }
};