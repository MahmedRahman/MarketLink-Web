<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\User;
use App\Models\WorkActivity;
use App\Models\WorkIdea;
use App\Support\WorkHub;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class WorkIdeaController extends Controller
{
    public function index(Request $request)
    {
        $actor = WorkHub::actor($request);
        if (! $actor) {
            abort(403);
        }

        $orgId = $actor->organization_id;
        $types = WorkActivity::types();

        $suggested = WorkIdea::query()
            ->where('organization_id', $orgId)
            ->where('status', 'suggested')
            ->orderByDesc('created_at')
            ->get();

        $archived = WorkIdea::query()
            ->where('organization_id', $orgId)
            ->where('status', 'archived')
            ->orderByDesc('created_at')
            ->get();

        $isOwner = function (WorkIdea $idea) use ($actor): bool {
            return (string) $idea->creator_type === ($actor instanceof Employee ? 'employee' : 'web')
                && (int) $idea->creator_id === (int) $actor->id;
        };

        $isWebAdmin = $actor instanceof User && $actor->is_admin;
        $isHubAdmin = $actor instanceof Employee && $actor->isWorkHubAdmin();

        return view('ideas.index', [
            'suggestedIdeas' => $suggested,
            'archivedIdeas' => $archived,
            'types' => $types,
            'canConvertIdea' => $isWebAdmin,
            'actor' => $actor,
            'isOwner' => $isOwner,
            'canManage' => $isWebAdmin || $isHubAdmin,
            'ideasLayout' => $actor instanceof Employee ? 'layouts.employee' : 'layouts.dashboard',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $actor = WorkHub::actor($request);
        if (! $actor) {
            abort(403);
        }

        $orgId = $actor->organization_id;
        $allowedTypes = array_keys(WorkActivity::types());

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'nullable|in:'.implode(',', $allowedTypes),
        ]);

        $ideaType = $validated['type'] ?: null;

        $idea = WorkIdea::create([
            'organization_id' => $orgId,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'type' => $ideaType,
            'status' => 'suggested',
            'creator_type' => $actor instanceof Employee ? 'employee' : 'web',
            'creator_id' => $actor->id,
        ]);

        return redirect()
            ->route('ideas.index')
            ->with('success', 'تم إضافة الفكرة بنجاح');
    }

    public function edit(Request $request, WorkIdea $idea)
    {
        $actor = WorkHub::actor($request);
        if (! $actor) {
            abort(403);
        }

        $this->authorizeIdeaOwnerOrAdmin($actor, $idea);

        return view('ideas.edit', [
            'idea' => $idea,
            'types' => WorkActivity::types(),
            'ideasLayout' => $actor instanceof Employee ? 'layouts.employee' : 'layouts.dashboard',
        ]);
    }

    public function update(Request $request, WorkIdea $idea): RedirectResponse
    {
        $actor = WorkHub::actor($request);
        if (! $actor) {
            abort(403);
        }

        $this->authorizeIdeaOwnerOrAdmin($actor, $idea);

        $allowedTypes = array_keys(WorkActivity::types());

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'nullable|in:'.implode(',', $allowedTypes),
        ]);

        $idea->update([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'type' => ($validated['type'] ?: null),
        ]);

        return redirect()
            ->route('ideas.index')
            ->with('success', 'تم تحديث الفكرة');
    }

    public function destroy(Request $request, WorkIdea $idea): RedirectResponse
    {
        $actor = WorkHub::actor($request);
        if (! $actor) {
            abort(403);
        }

        $this->authorizeIdeaOwnerOrAdmin($actor, $idea);

        $idea->delete();

        return redirect()
            ->route('ideas.index')
            ->with('success', 'تم حذف الفكرة');
    }

    public function archive(Request $request, WorkIdea $idea): RedirectResponse
    {
        $actor = WorkHub::actor($request);
        if (! $actor) {
            abort(403);
        }

        $this->authorizeIdeaOwnerOrAdmin($actor, $idea);

        $idea->forceFill(['status' => 'archived'])->save();

        return redirect()
            ->route('ideas.index')
            ->with('success', 'تم تأجيل الفكرة إلى الأرشيف');
    }

    /**
     * زر "تحويل لمشروع": يفتح فورم نشاط جديد الموجود في `/work` prefilled.
     * الفكرة بتتشال بعد حفظ النشاط (في WorkActivityController@store) لو تم إرسال idea_id.
     */
    public function convertToWork(Request $request, WorkIdea $idea): RedirectResponse
    {
        $actor = WorkHub::actor($request);
        if (! $actor) {
            abort(403);
        }

        // الأدمن اللي ظهر في Issue هو أدمن الويب (is_admin=true)
        abort_unless($actor instanceof User && $actor->is_admin, 403);
        abort_unless((int) $idea->organization_id === (int) $actor->organization_id, 403);

        $types = WorkActivity::types();
        $prefillType = $idea->type;

        return redirect()->route('work.index', [
            'open_new_activity' => 1,
            'idea_id' => $idea->id,
            'prefill_title' => $idea->title,
            'prefill_description' => $idea->description,
            'prefill_type' => $prefillType,
            // لو النوع فاضي، نخلي الفورم يطلب اختيار النوع من الأدمن
            'prefill_force_select_type' => $prefillType ? 0 : 1,
            // نخلي checkbox إنشاء التاسكات القياسية (حسب منطق الفورم نفسه)
            'with_template' => 1,
            'types_hint' => array_key_exists($prefillType ?? '', $types) ? $prefillType : null,
        ]);
    }

    private function authorizeIdeaOwnerOrAdmin(User|Employee $actor, WorkIdea $idea): void
    {
        $isWebAdmin = $actor instanceof User && $actor->is_admin;
        $isHubAdmin = $actor instanceof Employee && $actor->isWorkHubAdmin();
        if ($isWebAdmin || $isHubAdmin) {
            abort_unless((int) $idea->organization_id === (int) $actor->organization_id, 403);
            return;
        }

        $ownerType = $actor instanceof Employee ? 'employee' : 'web';
        abort_unless((string) $idea->creator_type === $ownerType && (int) $idea->creator_id === (int) $actor->id, 403);
    }
}

