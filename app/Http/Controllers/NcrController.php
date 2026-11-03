<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NcrController extends Controller
{
    public function index()
    {
        $ncrs = [
            [
                'id' => 1,
                'ncr_no' => 'NCR-2026-0042',
                'ssc_id' => 'SSC SBY-5778',
                'part_name' => 'OUTPUT SHAFT',
                'customer' => 'PT Pamapersada Nusantara',
                'issue_description' => 'OD Area H terukur 93.970 mm (Undersize 0.017 mm di bawah limit spec 93.987 mm)',
                'severity' => 'Mayor',
                'disposition' => 'Laser Cladding & Re-machining to Nominal Ø94.00',
                'pic_engineer' => 'Agus Riyanto (Leader QC)',
                'status' => 'In-Rework Workshop'
            ],
            [
                'id' => 2,
                'ncr_no' => 'NCR-2026-0039',
                'ssc_id' => 'SSC SBY-5612',
                'part_name' => 'OUTPUT SHAFT',
                'customer' => 'PT United Tractors Tbk',
                'issue_description' => 'Runout TIR pada titik dial D terukur 0.28 mm (Batas maksimal 0.20 mm mengacu Datum B & E)',
                'severity' => 'Kritis',
                'disposition' => 'Hydraulic Press Straightening & Final Grinding',
                'pic_engineer' => 'Faqih S A A (QC Inspector)',
                'status' => 'Pending Verification'
            ],
            [
                'id' => 3,
                'ncr_no' => 'NCR-2026-0035',
                'ssc_id' => 'SSC SBY-5501',
                'part_name' => 'HYDRAULIC CYLINDER ROD',
                'customer' => 'PT Bukit Makmur Mandiri Utama',
                'issue_description' => 'Pitting visual mendalam sedalam 0.35 mm pada area sealing journal',
                'severity' => 'Mayor',
                'disposition' => 'Hard Chrome Striping, Grinding, and Re-chroming',
                'pic_engineer' => 'M Kevin S (QC Inspector)',
                'status' => 'Resolved / Closed'
            ]
        ];

        return view('ncrs.index', compact('ncrs'));
    }
}

