<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Illuminate\Database\Eloquent\Builder;

class TodosExport implements FromQuery, WithHeadings, WithMapping, WithEvents
{ 
    protected $query;
    protected $totalTodos;
    protected $totalTimeTracked;

    public function __construct(Builder $query)
    {
        $this->query = $query;
        $summaryQuery = clone $query;
        $this->totalTodos = $summaryQuery->count();
        $this->totalTimeTracked = $summaryQuery->sum('time_tracked');
    }

    public function query()
    {
        return $this->query;
    }

    public function headings(): array
    {
        return ['Title', 'Assignee', 'Due Date', 'Time Tracked (hours)', 'Status', 'Priority'];
    }

    public function map($todo): array
    {
        return [
            $todo->title,
            $todo->assignee,
            $todo->due_date,
            $todo->time_tracked,
            $todo->status,
            $todo->priority,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $lastRow = $this->totalTodos + 2;
                $event->sheet->getDelegate()->setCellValue("A{$lastRow}", 'Total Todos:');
                $event->sheet->getDelegate()->setCellValue("B{$lastRow}", $this->totalTodos);

                $lastRow++;
                $event->sheet->getDelegate()->setCellValue("A{$lastRow}", 'Total Time Tracked:');
                $event->sheet->getDelegate()->setCellValue("B{$lastRow}", $this->totalTimeTracked);
            },
        ];
    }
}
