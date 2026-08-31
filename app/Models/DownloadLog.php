<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DownloadLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'repository_id',
        'user_id',
        'downloaded_by_name',
        'downloaded_by_role',
        'ip_address',
        'user_agent',
    ];

    public function repository()
    {
        return $this->belongsTo(Repository::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Jina la mtumiaji aliyepakua (kwa urahisi)
     */
    public function getDownloaderDisplayAttribute(): string
    {
        if ($this->downloaded_by_name) {
            return $this->downloaded_by_name;
        }
        if ($this->user) {
            return $this->user->name;
        }
        return 'Mgeni (Guest)';
    }

    /**
     * Badge ya rangi kulingana na role
     */
    public function getRoleBadgeColorAttribute(): string
    {
        return match ($this->downloaded_by_role) {
            'admin'      => 'red',
            'librarian'  => 'purple',
            'supervisor' => 'blue',
            'student'    => 'green',
            default      => 'gray',
        };
    }
}