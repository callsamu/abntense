<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Document extends Model
{
    /** @use HasFactory<\Database\Factories\DocumentFactory> */
    use HasFactory;

    const ROLE_OWNER = 'owner';
    const ROLE_EDITOR = 'editor';
    const ROLE_VIEWER = 'viewer';

    protected function casts(): array
    {
        return [
            'metadata' => AsArrayObject::class,
            'content' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function collaborators(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'collaborators');
    }
}
