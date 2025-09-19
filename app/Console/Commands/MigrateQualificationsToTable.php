<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Employee;

class MigrateQualificationsToTable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'qualifications:migrate
                            {--dry-run : Run in dry-run mode to see what would be migrated}
                            {--force : Force migration even if qualifications already exist in table}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate existing employee qualifications from JSON column to employee_qualifications table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $force = $this->option('force');
        
        $this->info('Starting qualifications migration...');
        
        $employees = Employee::whereNotNull('qualifications_details')
            ->where('qualifications_details', '!=', '[]')
            ->where('qualifications_details', '!=', '{}')
            ->get();
            
        $this->info("Found {$employees->count()} employees with qualification data to migrate.");
        
        $migratedCount = 0;
        $skippedCount = 0;
        
        foreach ($employees as $employee) {
            // Check if employee already has qualifications in the table
            if (!$force && $employee->qualifications()->exists()) {
                $this->warn("Employee {$employee->full_name} already has qualifications in table. Skipping.");
                $skippedCount++;
                continue;
            }
            
            $qualifications = $employee->qualifications_details;
            if (empty($qualifications)) {
                continue;
            }
            
            $this->info("Processing employee: {$employee->full_name}");
            
            if ($dryRun) {
                $this->line("  Would migrate " . count($qualifications) . " qualification(s):");
                foreach ($qualifications as $index => $qual) {
                    $qualName = $qual['qualification'] ?? $qual['title'] ?? 'Unknown';
                    $institution = $qual['institution'] ?? 'Unknown Institution';
                    $year = $qual['year_obtained'] ?? 'Unknown Year';
                    $this->line("    - {$qualName} from {$institution} ({$year})");
                }
            } else {
                try {
                    $employee->syncQualificationsToTable();
                    $this->info("  Successfully migrated " . count($qualifications) . " qualification(s)");
                    $migratedCount++;
                } catch (\Exception $e) {
                    $this->error("  Failed to migrate qualifications: {$e->getMessage()}");
                }
            }
        }
        
        if ($dryRun) {
            $this->info('\nDry run completed. No data was actually migrated.');
        } else {
            $this->info("\nMigration completed!");
            $this->info("Migrated: {$migratedCount} employees");
            $this->info("Skipped: {$skippedCount} employees");
        }
        
        return 0;
    }
}
