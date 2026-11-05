<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'ssc_id',
        'report_no',
        'date',
        'customer',
        'part_name',
        'inspection_type',
        'job_no',
        'model_unit',
        'quote_no',
        'part_no',
        'doc_no',
        'rev',
        'sections',
        'tir',
        'recommendations',
        'signatures',
        'photos',
        'has_rejects'
    ];

    protected $casts = [
        'sections' => 'array',
        'tir' => 'array',
        'recommendations' => 'array',
        'signatures' => 'array',
        'photos' => 'array',
        'has_rejects' => 'boolean',
        'date' => 'date:Y-m-d'
    ];
}
