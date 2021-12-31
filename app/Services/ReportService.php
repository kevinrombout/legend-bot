<?php

namespace App\Services;

use App\Entities\ReportItem;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ReportService
{
    public function generateReport(Carbon $startDate, Carbon $endDate): Collection
    {
        $transactions = $this->getTransactions($startDate, $endDate);

        $transactionsPerReceiver = $transactions->groupBy('receiver_id');
        
        return $transactionsPerReceiver->map(function (Collection $transactions) {
            return ReportItem::make(
                $transactions->first()->receiver->slack_user_name,
                $transactions->sum('count')
            );
        })->sortByDesc('count')->values();
    }

    protected function getTransactions(Carbon $startDate, Carbon $endDate): Collection
    {
        return Transaction::query()
            ->with(['sender', 'receiver'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();
    }
}
