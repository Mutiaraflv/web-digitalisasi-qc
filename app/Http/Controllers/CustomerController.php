<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = [
            [
                'id' => 1,
                'code' => 'CUST-PAMA',
                'name' => 'PT Pamapersada Nusantara (PAMA)',
                'industry' => 'Mining Contractor & Heavy Equipment',
                'location' => 'Balikpapan / Sangatta Site',
                'pic_name' => 'Bambang Trianto',
                'pic_phone' => '+62 811-3452-980',
                'active_jobs' => 12,
                'status' => 'Akun Prioritas'
            ],
            [
                'id' => 2,
                'code' => 'CUST-UT',
                'name' => 'PT United Tractors Tbk',
                'industry' => 'Heavy Equipment Distributor & Workshop',
                'location' => 'Surabaya Branch / Cakung',
                'pic_name' => 'Hendro Setiawan',
                'pic_phone' => '+62 812-9876-123',
                'active_jobs' => 8,
                'status' => 'Akun Prioritas'
            ],
            [
                'id' => 3,
                'code' => 'CUST-BUMA',
                'name' => 'PT Bukit Makmur Mandiri Utama (BUMA)',
                'industry' => 'Coal Mining Services',
                'location' => 'Berau / Lati Site',
                'pic_name' => 'Rudy Kurniawan',
                'pic_phone' => '+62 813-4567-890',
                'active_jobs' => 5,
                'status' => 'Reguler'
            ],
            [
                'id' => 4,
                'code' => 'CUST-VALE',
                'name' => 'PT Vale Indonesia Tbk',
                'industry' => 'Nickel Mining & Processing',
                'location' => 'Sorowako Plant',
                'pic_name' => 'Agus Priyono',
                'pic_phone' => '+62 821-2345-678',
                'active_jobs' => 3,
                'status' => 'Reguler'
            ]
        ];

        return view('customers.index', compact('customers'));
    }
}

