<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PartController extends Controller
{
    public function index()
    {
        $templates = [
            [
                'id' => 1,
                'part_no' => 'DWG-HT051-OS',
                'name' => 'OUTPUT SHAFT',
                'category' => 'Rotating Shaft / Poros Penggerak',
                'model_unit' => 'HT051 Machinery',
                'form_doc' => 'FORM-QC-002 REV 04',
                'division' => 'Divisi Machining & Shaft',
                'file_format' => 'template.xlsx (Tersedia)',
                'status' => 'Aktif Digunakan',
                'is_active' => true,
                'key_specs' => 'OD Ø79±0.25, Ø82±0.25, Ø100.01±0.008, Max TIR 0.20 mm'
            ],
            [
                'id' => 2,
                'part_no' => 'DWG-CYL-ROD-014',
                'name' => 'HYDRAULIC CYLINDER ROD',
                'category' => 'Hydraulic Cylinder & Actuator',
                'model_unit' => 'Excavator PC200 / PC300',
                'form_doc' => 'FORM-QC-005',
                'division' => 'Divisi Hidrolik',
                'file_format' => 'Belum Diunggah',
                'status' => 'Menunggu Template Divisi',
                'is_active' => false,
                'key_specs' => 'OD Ø90 f7 (-0.030/-0.065), Straightness 0.1 mm/m'
            ],
            [
                'id' => 3,
                'part_no' => 'DWG-PIN-GR-089',
                'name' => 'DRIVE PINION GEAR',
                'category' => 'Gears & Transmission',
                'model_unit' => 'Bulldozer D85ESS',
                'form_doc' => 'FORM-QC-008',
                'division' => 'Divisi Gear & Transmisi',
                'file_format' => 'Belum Diunggah',
                'status' => 'Menunggu Template Divisi',
                'is_active' => false,
                'key_specs' => 'Spline Module 4, Runout TIR Max 0.05 mm'
            ],
            [
                'id' => 4,
                'part_no' => 'DWG-BJ-320',
                'name' => 'BEARING JOURNAL SLEEVE',
                'category' => 'Bushing & Journal',
                'model_unit' => 'Dump Truck HD785',
                'form_doc' => 'FORM-QC-011',
                'division' => 'Divisi Bushing & Journal',
                'file_format' => 'Belum Diunggah',
                'status' => 'Menunggu Template Divisi',
                'is_active' => false,
                'key_specs' => 'ID Ø120 H7 (+0.035/0), Roughness Ra 0.8'
            ],
            [
                'id' => 5,
                'part_no' => 'DWG-CR-550',
                'name' => 'CONNECTING ROD',
                'category' => 'Engine & Piston Linkage',
                'model_unit' => 'Diesel Engine V16',
                'form_doc' => 'FORM-QC-014',
                'division' => 'Divisi Engine Rebuild',
                'file_format' => 'Belum Diunggah',
                'status' => 'Menunggu Template Divisi',
                'is_active' => false,
                'key_specs' => 'Center-to-Center 280±0.02, Big End Ø95 H6'
            ]
        ];

        return view('parts.index', compact('templates'));
    }
}
