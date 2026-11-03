<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PartController extends Controller
{
    public function index()
    {
        $parts = [
            [
                'id' => 1,
                'part_no' => 'DWG-HT051-OS',
                'name' => 'OUTPUT SHAFT',
                'category' => 'Rotating Shaft / Poros Penggerak',
                'model_unit' => 'HT051 Machinery',
                'material' => 'AISI 4140 / SCM440 High Tensile Steel',
                'key_specs' => 'OD Ø79±0.25, Ø82±0.25, Ø100.01±0.008, Max TIR 0.20 mm',
                'form_doc' => 'FORM-QC-002 (Rev 04)',
                'status' => 'Aktif Digunakan'
            ],
            [
                'id' => 2,
                'part_no' => 'DWG-CYL-ROD-014',
                'name' => 'HYDRAULIC CYLINDER ROD',
                'category' => 'Hydraulic Cylinder & Actuator',
                'model_unit' => 'Excavator PC200 / PC300',
                'material' => 'CK45 Hard Chrome Plated',
                'key_specs' => 'OD Ø90 f7 (-0.030/-0.065), Straightness 0.1 mm/m',
                'form_doc' => 'FORM-QC-005 (Rev 02)',
                'status' => 'Aktif Digunakan'
            ],
            [
                'id' => 3,
                'part_no' => 'DWG-PIN-GR-089',
                'name' => 'DRIVE PINION SHAFT',
                'category' => 'Gears & Transmission',
                'model_unit' => 'Bulldozer D85ESS',
                'material' => 'SNCM439 Case Hardened (HRC 58-62)',
                'key_specs' => 'Spline Module 4, Runout TIR Max 0.05 mm',
                'form_doc' => 'FORM-QC-008 (Rev 01)',
                'status' => 'Aktif Digunakan'
            ],
            [
                'id' => 4,
                'part_no' => 'DWG-BJ-320',
                'name' => 'BEARING JOURNAL SLEEVE',
                'category' => 'Bushing & Journal',
                'model_unit' => 'Dump Truck HD785',
                'material' => 'Bronze / Cast Steel',
                'key_specs' => 'ID Ø120 H7 (+0.035/0), Roughness Ra 0.8',
                'form_doc' => 'FORM-QC-011 (Rev 03)',
                'status' => 'Aktif Digunakan'
            ]
        ];

        return view('parts.index', compact('parts'));
    }
}

