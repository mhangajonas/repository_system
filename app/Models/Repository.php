<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Repository extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'title',
        'abstract',
        'authors',
        'supervisor',
        'department',
        'year',
        'degree_programme',
        'keywords',
        'document_type',
        'file_path',
        'status',
        'access_level',
        'accession_number',
        'comments',
    ];

    // Uhusiano na Mwanafunzi aliyepakia
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Uhusiano na Download Logs (FR-3.5)
    public function downloads()
    {
        return $this->hasMany(DownloadLog::class);
    }
}