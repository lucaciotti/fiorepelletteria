<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProcessType extends Model
{
    protected $guarded = [
        'id'
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProcessTypeCategory::class);
    }
}
