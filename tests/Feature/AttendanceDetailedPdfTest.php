<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use DOMDocument;
use DOMXPath;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class AttendanceDetailedPdfTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Event::forget('composing: admin.*');
    }

    public function test_long_date_range_is_split_into_readable_tables_without_losing_dates_or_attendance(): void
    {
        $employee = new Employee(['employee_id' => 'EMP-01']);
        $employee->id = 1;
        $employee->setRelation('user', new User(['name' => 'Nama Karyawan']));
        $employee->setRelation('workSchedule', null);

        $data = [
            'startDate' => '2024-12-30',
            'endDate' => '2025-03-03',
            'employees' => collect([$employee]),
            'attendances' => collect([
                (object) ['employee_id' => 1, 'date' => '2025-01-02', 'check_in' => '07:45', 'check_out' => '17:00'],
                (object) ['employee_id' => 1, 'date' => '2025-02-17', 'check_in' => '08:15', 'check_out' => '17:30'],
            ]),
            'leaves' => collect([
                (object) ['employee_id' => 1, 'start_date' => '2025-03-03', 'end_date' => '2025-03-03', 'type' => 'annual'],
            ]),
        ];

        $html = view('admin.attendance.reports.pdf.detailed', $data)->render();
        $document = new DOMDocument;
        @$document->loadHTML($html);
        $xpath = new DOMXPath($document);
        $sections = $xpath->query('//div[contains(concat(" ", normalize-space(@class), " "), " detail-section ")]');
        $this->assertCount(5, $sections);

        $expectedDate = Carbon::parse($data['startDate']);
        foreach ($sections as $section) {
            $headers = $xpath->query('.//th[@class="date-col"]', $section);
            $this->assertLessThanOrEqual(14, $headers->length);
            foreach ($headers as $header) {
                $this->assertSame($expectedDate->format('d/m/y'), trim($header->textContent));
                $expectedDate->addDay();
            }
            $this->assertStringContainsString('Nama Karyawan', $section->textContent);
        }
        $this->assertSame('2025-03-04', $expectedDate->format('Y-m-d'));
        $this->assertStringContainsString('30-12-2024 s/d 12-01-2025', $sections->item(0)->textContent);
        $this->assertStringContainsString('24-02-2025 s/d 03-03-2025', $sections->item(4)->textContent);
        $this->assertStringContainsString('07:45', $sections->item(0)->textContent);
        $this->assertStringContainsString('08:15', $sections->item(3)->textContent);
        $this->assertStringContainsString('CUTI', $sections->item(4)->textContent);

        $pdf = Pdf::loadView('admin.attendance.reports.pdf.detailed', $data)->setPaper('A4', 'landscape');
        $this->assertStringStartsWith('%PDF-', $pdf->output());
        $this->assertLessThan(5, $pdf->getDomPDF()->getCanvas()->get_page_count());
    }

    public function test_single_day_without_employees_still_renders(): void
    {
        $html = view('admin.attendance.reports.pdf.detailed', [
            'startDate' => '2025-03-01',
            'endDate' => '2025-03-01',
            'employees' => collect(),
            'attendances' => collect(),
            'leaves' => collect(),
        ])->render();

        $this->assertStringContainsString('01-03-2025 s/d 01-03-2025', $html);
    }

    public function test_many_employees_are_included_in_each_date_section_and_pdf_spans_pages(): void
    {
        $employees = collect(range(1, 40))->map(function ($id) {
            $employee = new Employee(['employee_id' => 'EMP-'.$id]);
            $employee->id = $id;
            $employee->setRelation('user', new User(['name' => 'Karyawan '.$id]));
            $employee->setRelation('workSchedule', null);

            return $employee;
        });
        $data = [
            'startDate' => '2025-01-01',
            'endDate' => '2025-01-29',
            'employees' => $employees,
            'attendances' => collect(),
            'leaves' => collect(),
        ];

        $document = new DOMDocument;
        @$document->loadHTML(view('admin.attendance.reports.pdf.detailed', $data)->render());
        $xpath = new DOMXPath($document);
        $sections = $xpath->query('//div[contains(concat(" ", normalize-space(@class), " "), " detail-section ")]');
        $this->assertCount(3, $sections);
        foreach ($sections as $section) {
            $this->assertCount(40, $xpath->query('.//table/tbody/tr', $section));
        }

        $pdf = Pdf::loadView('admin.attendance.reports.pdf.detailed', $data)->setPaper('A4', 'landscape');
        $this->assertStringStartsWith('%PDF-', $pdf->output());
        $this->assertGreaterThan(3, $pdf->getDomPDF()->getCanvas()->get_page_count());
    }
}
