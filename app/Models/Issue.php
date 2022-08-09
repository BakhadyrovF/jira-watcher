<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Issue extends Model
{
    use HasFactory;

    protected $fillable = [
        'issue_id', 'link', 'summary', 'key', 'summary', 'description', 'issue_created_at', 'user_id'
    ];

}
