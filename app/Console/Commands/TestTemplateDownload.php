<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Exports\EmployeeTemplateExport;
use Maatwebsite\Excel\Facades\Excel;

class TestTemplateDownload extends Command
{
    protected $signature = 'test:template-download';
    protected $description = 'Test template download functionality';

    public function handle()
    {
        $this->info('Testing template download...');

        try {
            // Test simple array creation first
            $this->info('Testing array creation...');
            $export = new EmployeeTemplateExport;
            $this->info('EmployeeTemplateExport created successfully');

            $array = $export->array();
            $headings = $export->headings();

            $this->info('Array rows: ' . count($array));
            $this->info('Headings count: ' . count($headings));
            $this->info('First heading: ' . $headings[0]);

            // Check if storage directory exists
            $storagePath = storage_path('app/public');
            if (!is_dir($storagePath)) {
                mkdir($storagePath, 0755, true);
                $this->info('Created storage directory');
            }

            $this->info('Storage path: ' . $storagePath);
            $this->info('Storage writable: ' . (is_writable($storagePath) ? 'YES' : 'NO'));

            // Try simpler approach
            $this->info('Attempting Excel store...');
            $result = Excel::store($export, 'test_template.xlsx', 'public');
            $this->info('Excel store result: ' . var_export($result, true));

            $filePath = storage_path('app/public/test_template.xlsx');
            $this->info('File exists: ' . (file_exists($filePath) ? 'YES' : 'NO'));

            if (file_exists($filePath)) {
                $this->info('File size: ' . filesize($filePath) . ' bytes');
            }

            $this->info('Template test completed successfully!');
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            $this->error('File: ' . $e->getFile() . ' Line: ' . $e->getLine());

            // Get previous exception if any
            if ($e->getPrevious()) {
                $this->error('Previous: ' . $e->getPrevious()->getMessage());
            }
        }
    }
}
