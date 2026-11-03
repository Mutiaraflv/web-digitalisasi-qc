<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CalibrationController extends Controller
{
    public function index()
    {
        $tools = [
            [
                'id' => 1,
                'tool_code' => 'MIC-OUT-004',
                'name' => 'Outside Micrometer 75 - 100 mm',
                'brand' => 'Mitutoyo Japan',
                'resolution' => '0.001 mm (Digital Digimatic)',
                'last_calibrated' => '2026-06-15',
                'valid_until' => '2027-06-15',
                'cert_no' => 'CAL/2026/KAN-0988',
                'status' => 'Valid / Siap Pakai'
            ],
            [
                'id' => 2,
                'tool_code' => 'MIC-OUT-007',
                'name' => 'Outside Micrometer 100 - 125 mm',
                'brand' => 'Mitutoyo Japan',
                'resolution' => '0.001 mm (Digital Digimatic)',
                'last_calibrated' => '2026-07-10',
                'valid_until' => '2027-07-10',
                'cert_no' => 'CAL/2026/KAN-1045',
                'status' => 'Valid / Siap Pakai'
            ],
            [
                'id' => 3,
                'tool_code' => 'DIAL-IND-012',
                'name' => 'Dial Indicator Plunger Type with Magnetic Base',
                'brand' => 'Mitutoyo Japan',
                'resolution' => '0.01 mm / Range 10 mm',
                'last_calibrated' => '2026-05-20',
                'valid_until' => '2027-05-20',
                'cert_no' => 'CAL/2026/KAN-0872',
                'status' => 'Valid / Siap Pakai'
            ],
            [
                'id' => 4,
                'tool_code' => 'CAL-VER-003',
                'name' => 'Vernier Caliper Heavy Duty 1000 mm',
                'brand' => 'Starrett USA',
                'resolution' => '0.02 mm (Vernier Scale)',
                'last_calibrated' => '2026-08-01',
                'valid_until' => '2027-08-01',
                'cert_no' => 'CAL/2026/KAN-1290',
                'status' => 'Valid / Siap Pakai'
            ],
            [
                'id' => 5,
                'tool_code' => 'BORE-GAUGE-002',
                'name' => 'Cylinder Bore Gauge Set 50 - 160 mm',
                'brand' => 'Mitutoyo Japan',
                'resolution' => '0.002 mm',
                'last_calibrated' => '2026-03-12',
                'valid_until' => '2027-03-12',
                'cert_no' => 'CAL/2026/KAN-0654',
                'status' => 'Valid / Siap Pakai'
            ]
        ];

        return view('calibration.index', compact('tools'));
    }
}

