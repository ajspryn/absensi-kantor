<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class EmployeeTemplateExport implements FromArray, WithHeadings, WithColumnWidths, WithStyles
{
    public function array(): array
    {
        return [
            [
                'EMP001',
                'John Doe',
                'john.doe@email.com',
                'password',
                'IT',
                'Software Developer',
                '+6281234567890',
                'Jl. Contoh No. 123, Jakarta',
                '2024-01-15',
                '5000000',
                'aktif',
                'ya'
            ],
            [
                'EMP002',
                'Jane Smith',
                'jane.smith@email.com',
                'password',
                'HR',
                'HR Manager',
                '+6281234567891',
                'Jl. Contoh No. 456, Jakarta',
                '2024-02-01',
                '7000000',
                'aktif',
                'tidak'
            ]
        ];
    }

    public function headings(): array
    {
        return [
            'id_karyawan',
            'nama_lengkap',
            'email',
            'password',
            'departemen',
            'posisi',
            'telepon',
            'alamat',
            'tanggal_masuk',
            'gaji',
            'status_aktif',
            'remote_attendance'
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15,  // id_karyawan
            'B' => 25,  // nama_lengkap
            'C' => 30,  // email
            'D' => 15,  // password
            'E' => 20,  // departemen
            'F' => 25,  // posisi
            'G' => 20,  // telepon
            'H' => 40,  // alamat
            'I' => 15,  // tanggal_masuk
            'J' => 15,  // gaji
            'K' => 15,  // status_aktif
            'L' => 20,  // remote_attendance
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the header row
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4472C4'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
            // Style the data rows
            'A:L' => [
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                ],
            ],
        ];
    }
}
