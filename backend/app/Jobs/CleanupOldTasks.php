<?php

namespace App\Jobs;

use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CleanupOldTasks implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // This is the main thing that happens when the job runs
    public function handle(): void
    {
        // Figure out what date was 30 days ago from now
        $cutoffDate = Carbon::now()->subDays(30);

        // Find all tasks that were created more than 30 days ago
        $oldTasks = Task::where('created_at', '<', $cutoffDate)->get();

        // Write in the log that we're starting the cleanup, and how many tasks will be deleted
        Log::info('Starting task cleanup job', [
            'cutoff_date' => $cutoffDate->toDateTimeString(),
            'tasks_to_delete' => $oldTasks->count(),
        ]);

        // Go through each old task, write in the log, and then delete it
        foreach ($oldTasks as $task) {
            Log::info('Deleting old task', [
                'task_id' => $task->id,
                'task_title' => $task->title,
                'user_id' => $task->user_id,
                'created_at' => $task->created_at->toDateTimeString(),
            ]);

            $task->delete();
        }

        // Write in the log that the cleanup is done and how many tasks were deleted
        Log::info('Task cleanup job completed', [
            'tasks_deleted' => $oldTasks->count(),
        ]);
    }
}
