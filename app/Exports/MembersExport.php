<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class MembersExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $data;
    protected $fields;
    protected $includeHeaders;

    public function __construct($data, $fields, $includeHeaders = true)
    {
        $this->data = $data;
        $this->fields = $fields;
        $this->includeHeaders = $includeHeaders;
    }

    public function collection()
    {
        return $this->data;
    }

    public function styles(Worksheet $sheet)
    {
        // Header style
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['argb' => 'FFFFFFFF'],
                'size' => 12,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF2F75B5'], // Dark blue
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ];

        // Body style
        $bodyStyle = [
            'alignment' => [
                'vertical' => Alignment::VERTICAL_TOP,
                'wrapText' => true,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FFD3D3D3'],
                ],
            ],
        ];

        if ($this->includeHeaders) {
            $headerRange = 'A1:' . $sheet->getHighestDataColumn() . '1';
            $sheet->getStyle($headerRange)->applyFromArray($headerStyle);
            $sheet->getRowDimension(1)->setRowHeight(25);
        }

        // Apply body styles
        $lastRow = $sheet->getHighestRow();
        if ($lastRow > 1) {
            $bodyStartRow = $this->includeHeaders ? 2 : 1;
            $bodyRange = 'A' . $bodyStartRow . ':' . $sheet->getHighestDataColumn() . $lastRow;
            $sheet->getStyle($bodyRange)->applyFromArray($bodyStyle);
            
            // Set row height for all rows
            for ($i = $bodyStartRow; $i <= $lastRow; $i++) {
                $sheet->getRowDimension($i)->setRowHeight(20);
            }
        }

        // Set column widths
        foreach (range('A', $sheet->getHighestDataColumn()) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
            $sheet->getStyle($col)->getAlignment()->setWrapText(true);
        }

        // Freeze the first row if headers are included
        if ($this->includeHeaders) {
            $sheet->freezePane('A2');
        }

        return [];
    }

    public function headings(): array
    {
        if (!$this->includeHeaders) {
            return [];
        }

        // Map field names to human-readable headings
        $headingsMap = [
            'surname' => 'Surname',
            'other_names' => 'Other Names',
            'id_number' => 'ID Number',
            'telephone' => 'Telephone',
            'email' => 'Email',
            'gender' => 'Gender',
            'date_of_birth' => 'Date of Birth',
            'county' => 'County',
            'constituency' => 'Constituency',
            'ward' => 'Ward',
            'polling_station' => 'Polling Station',
            'occupation' => 'Occupation',
            'education_level' => 'Education Level',
            'disability_status' => 'Disability Status',
            'ncpwd_number' => 'NCPWD Number',
            'created_at' => 'Registration Date'
        ];

        $headings = [];
        foreach ($this->fields as $field) {
            $headings[] = $headingsMap[$field] ?? ucfirst(str_replace('_', ' ', $field));
        }

        return $headings;
    }

    public function map($member): array
    {
        $row = [];
        foreach ($this->fields as $field) {
            $row[] = $member[$field] ?? '';
        }
        return $row;
    }
}