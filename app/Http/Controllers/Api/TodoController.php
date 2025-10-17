<?php

namespace App\Http\Controllers\Api;

use App\Models\Todo;
use App\Exports\TodosExport;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\TodoResource;
use Maatwebsite\Excel\Facades\Excel;

class TodoController extends Controller
{
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'assignee' => 'nullable|string|max:255',
            'due_date' => [
                'required',
                'date',
                Rule::unless($request->input('status') === 'completed', 'after_or_equal:today')
            ],
            'time_tracked' => 'sometimes|numeric|min:0',
            'status' => ['sometimes', Rule::in(['pending', 'open', 'in_progress', 'completed'])],
            'priority' => ['required', Rule::in(['low', 'medium', 'high'])],
        ]);
        $todo = Todo::create($validatedData);
        return (new TodoResource($todo))->response()->setStatusCode(201);
    }

    public function generateExcelReport(Request $request)
    {
        $request->validate([
            'start' => 'nullable|date',
            'end' => 'nullable|date|after_or_equal:start',
            'min' => 'nullable|numeric|min:0',
            'max' => 'nullable|numeric|gte:min',
        ]);
        $query = Todo::query();
        $query->when($request->filled('title'), fn($q) => $q->where('title', 'like', '%' . $request->input('title') . '%'));
        $query->when($request->filled('assignee'), fn($q) => $q->whereIn('assignee', explode(',', $request->input('assignee'))));
        $query->when($request->filled('start') && $request->filled('end'), fn($q) => $q->whereBetween('due_date', [$request->input('start'), $request->input('end')]));
        $query->when($request->filled('min') && $request->filled('max'), fn($q) => $q->whereBetween('time_tracked', [$request->input('min'), $request->input('max')]));
        $query->when($request->filled('status'), fn($q) => $q->whereIn('status', explode(',', $request->input('status'))));
        $query->when($request->filled('priority'), fn($q) => $q->whereIn('priority', explode(',', $request->input('priority'))));
        return Excel::download(new TodosExport($query), 'todos_report.xlsx');
    }

    public function getChartData(Request $request)
    {
        $request->validate(['type' => 'required|in:status,priority,assignee']);
        $type = $request->input('type');
        $data = [];
        if ($type === 'status') {
            $summary = Todo::query()->select('status', DB::raw('count(*) as total'))->groupBy('status')->pluck('total', 'status');
            $data = ['status_summary' => $summary];
        } elseif ($type === 'priority') {
            $summary = Todo::query()->select('priority', DB::raw('count(*) as total'))->groupBy('priority')->pluck('total', 'priority');
            $data = ['priority_summary' => $summary];
        } elseif ($type === 'assignee') {
            $assignees = Todo::query()->select('assignee')->whereNotNull('assignee')->distinct()->pluck('assignee');
            $summary = [];
            foreach ($assignees as $assignee) {
                $summary[$assignee] = [
                    'total_todos' => Todo::where('assignee', $assignee)->count(),
                    'total_pending_todos' => Todo::where('assignee', $assignee)->where('status', 'pending')->count(),
                    'total_timetracked_completed_todos' => (float) Todo::where('assignee', $assignee)->where('status', 'completed')->sum('time_tracked'),
                ];
            }
            $data = ['assignee_summary' => $summary];
        }
        return response()->json($data);
    }
}
