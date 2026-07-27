<?php

namespace App\Http\Controllers;

use App\Models\WorkActivity;
use App\Models\WorkTask;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicWorkShareController extends Controller
{
    public function show(string $token): View
    {
        $activity = $this->findSharedActivity($token);
        $activity->load(['tasks.contentWriter', 'tasks.designer']);

        $pipelineStages = [];
        foreach (WorkTask::pipelineStages() as $key => $label) {
            $stageTasks = $activity->tasks->where('pipeline_stage', $key)->values();
            $pipelineStages[] = [
                'key' => $key,
                'label' => $label,
                    'icon' => match ($key) {
                    'design' => 'palette',
                    'ready_to_publish' => 'schedule_send',
                    'published' => 'check_circle',
                    default => 'edit_note',
                },
                'tasks' => $stageTasks,
                'count' => $stageTasks->count(),
            ];
        }

        $contentCounts = [
            'total' => $activity->tasks->count(),
            'post' => $activity->tasks->where('content_type', 'post')->count(),
            'reels' => $activity->tasks->where('content_type', 'reels')->count(),
            'carousel' => $activity->tasks->where('content_type', 'carousel')->count(),
        ];

        return view('public.work.show', [
            'activity' => $activity,
            'pipelineStages' => $pipelineStages,
            'contentCounts' => $contentCounts,
            'shareToken' => $token,
        ]);
    }

    public function showTask(string $token, WorkTask $task): View
    {
        $activity = $this->findSharedActivity($token);
        abort_unless($task->work_activity_id === $activity->id, 404);

        $task->load(['contentWriter', 'designer', 'assignedEmployee', 'files']);

        return view('public.work.task', [
            'activity' => $activity,
            'task' => $task,
            'shareToken' => $token,
        ]);
    }

    private function findSharedActivity(string $token): WorkActivity
    {
        return WorkActivity::where('share_token', $token)->firstOrFail();
    }
}
