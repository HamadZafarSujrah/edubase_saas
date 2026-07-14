<?php

namespace App\Exports;

use App\Models\Student\Student;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class StudentsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $tenant_id;

    public function __construct($tenant_id)
    {
        $this->tenant_id = $tenant_id;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Student::where('tenant_id', $this->tenant_id)
            ->with(['schoolClass', 'section'])
            ->get();
    }

    public function headings(): array
    {
        return [
            'Admission No',
            'First Name',
            'Last Name',
            'Email',
            'Father Name',
            'Father Phone',
            'Class',
            'Section',
            'Status',
            'Admission Date',
        ];
    }

    /**
    * @var Student $student
    */
    public function map($student): array
    {
        return [
            $student->admission_no,
            $student->first_name,
            $student->last_name,
            $student->email,
            $student->father_name,
            $student->father_phone,
            $student->schoolClass->name ?? 'N/A',
            $student->section->name ?? 'N/A',
            $student->is_active ? 'Active' : 'Inactive',
            $student->admission_date ? $student->admission_date->format('d-M-Y') : '',
        ];
    }
}
