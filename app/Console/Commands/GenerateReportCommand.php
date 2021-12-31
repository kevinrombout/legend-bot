<?php

namespace App\Console\Commands;

use App\Services\ReportService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GenerateReportCommand extends Command
{
    protected $signature = 'legendbot:report {--start-date=} {--end-date=}';

    protected $description = 'Generate report for given period';

    public function __construct(
        protected ReportService $reportService
    ) {
        parent::__construct();
    }

    public function handle()
    {
        $startDate = $this->option('start-date')
            ? Carbon::parse($this->option('start-date'))->startOfDay()
            : now()->startOfMonth();

        $endDate = $this->option('end-date')
            ? Carbon::parse($this->option('end-date'))->endOfDay()
            : now()->endOfMonth();

        $report = $this->reportService->generateReport($startDate, $endDate);

        $this->table(
            ['Name', 'Count'],
            $report
        );

        return Command::SUCCESS;
    }
}
