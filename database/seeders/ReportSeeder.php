<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Report;

class ReportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jsonPath = 'C:\\Users\\mutiarafz\\.gemini\\antigravity\\scratch\\ccr-output-shaft-web\\data\\reports.json';
        if (file_exists($jsonPath)) {
            $data = json_decode(file_get_contents($jsonPath), true);
            if (!empty($data)) {
                $item = $data[0];
                $g = $item['general'] ?? [];

                Report::updateOrCreate(
                    ['ssc_id' => $g['sscId'] ?? 'SSC SBY-5778'],
                    [
                        'report_no' => $g['reportNo'] ?? '0742',
                        'date' => $g['date'] ?? '2026-10-17',
                        'customer' => $g['customer'] ?? 'AMMAN MINERAL NUSA TENGGARA, PT',
                        'part_name' => $g['partName'] ?? 'OUTPUT SHAFT',
                        'inspection_type' => $g['inspectionType'] ?? 'Pre',
                        'job_no' => $g['jobNo'] ?? '-',
                        'model_unit' => $g['modelUnit'] ?? 'HT051',
                        'quote_no' => $g['quoteNo'] ?? '-',
                        'part_no' => $g['partNo'] ?? '-',
                        'doc_no' => $g['docNo'] ?? 'FORM-QC-002',
                        'rev' => $g['rev'] ?? '04',
                        'sections' => $item['sections'] ?? [],
                        'tir' => $item['tir'] ?? [],
                        'recommendations' => $item['recommendations'] ?? [],
                        'signatures' => $item['signatures'] ?? [],
                        'has_rejects' => true
                    ]
                );
            }
        }
    }
}

