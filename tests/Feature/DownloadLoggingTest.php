<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\Repository;

class DownloadLoggingTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_download_creates_log()
    {
        // Ensure the file exists where the controller expects it: storage/app/public/{file_path}
        $publicPath = storage_path('app/public/documents');
        if (!is_dir($publicPath)) {
            mkdir($publicPath, 0777, true);
        }
        file_put_contents($publicPath . '/test.pdf', 'dummy');

        $student = User::factory()->create(['name' => 'frank', 'role' => 'student']);

        $repo = Repository::create([
            'user_id' => $student->id,
            'title' => 'Test Doc',
            'abstract' => 'abstract',
            'authors' => 'author',
            'supervisor' => 'sup',
            'department' => 'dept',
            'year' => 2026,
            'degree_programme' => 'prog',
            'keywords' => 'k',
            'document_type' => 'thesis',
            'file_path' => 'documents/test.pdf',
            'status' => 'approved',
            'access_level' => 'Open-Access',
        ]);

        $this->actingAs($student)
            ->get(route('repositories.download', $repo->id))
            ->assertStatus(200);

        $this->assertDatabaseHas('download_logs', [
            'repository_id' => $repo->id,
            'downloaded_by_name' => $student->name,
            'downloaded_by_role' => 'student',
        ]);
    }

    public function test_supervisor_download_creates_log()
    {
        $publicPath = storage_path('app/public/documents');
        if (!is_dir($publicPath)) {
            mkdir($publicPath, 0777, true);
        }
        file_put_contents($publicPath . '/test.pdf', 'dummy');

        $student = User::factory()->create(['role' => 'student']);
        $supervisor = User::factory()->create(['name' => 'kajubili', 'role' => 'supervisor']);

        $repo = Repository::create([
            'user_id' => $student->id,
            'title' => 'Test Doc 2',
            'abstract' => 'abstract',
            'authors' => 'author',
            'supervisor' => 'kajubili',
            'department' => 'dept',
            'year' => 2026,
            'degree_programme' => 'prog',
            'keywords' => 'k',
            'document_type' => 'thesis',
            'file_path' => 'documents/test.pdf',
            'status' => 'approved',
            'access_level' => 'Open-Access',
        ]);

        $this->actingAs($supervisor)
            ->get(route('repositories.download', $repo->id))
            ->assertStatus(200);

        $this->assertDatabaseHas('download_logs', [
            'repository_id' => $repo->id,
            'downloaded_by_name' => $supervisor->name,
            'downloaded_by_role' => 'supervisor',
        ]);
    }
}
