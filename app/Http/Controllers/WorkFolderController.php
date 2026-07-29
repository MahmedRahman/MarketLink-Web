<?php

namespace App\Http\Controllers;

use App\Models\WorkActivity;
use App\Models\WorkFolder;
use App\Support\WorkHub;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class WorkFolderController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        abort_unless(WorkHub::canManageFolders($request), 403);

        $orgId = WorkHub::organizationId($request);
        abort_unless($orgId, 403);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $order = (int) (WorkFolder::query()
            ->where('organization_id', $orgId)
            ->max('order') ?? 0);

        WorkFolder::create([
            'organization_id' => $orgId,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'order' => $order + 1,
        ]);

        return redirect()
            ->route(WorkHub::routeName('index'), ['view' => 'folder'])
            ->with('success', 'تم إنشاء الفولدر');
    }

    public function update(Request $request, WorkFolder $folder): RedirectResponse
    {
        abort_unless(WorkHub::canManageFolders($request), 403);
        WorkHub::authorizeOrganization($request, (int) $folder->organization_id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $folder->update([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
        ]);

        return redirect()
            ->route(WorkHub::routeName('index'), ['view' => 'folder'])
            ->with('success', 'تم تحديث الفولدر');
    }

    public function destroy(Request $request, WorkFolder $folder): RedirectResponse
    {
        abort_unless(WorkHub::canManageFolders($request), 403);
        WorkHub::authorizeOrganization($request, (int) $folder->organization_id);

        // الأنشطة تبقى بدون فولدر (nullOnDelete على FK)
        $folder->delete();

        return redirect()
            ->route(WorkHub::routeName('index'), ['view' => 'folder'])
            ->with('success', 'تم حذف الفولدر — الأنشطة رجعت بدون فولدر');
    }

    public function moveActivity(Request $request, WorkActivity $work): RedirectResponse|JsonResponse
    {
        abort_unless(WorkHub::canManageFolders($request), 403);
        WorkHub::authorizeOrganization($request, (int) $work->organization_id);

        $request->merge([
            'folder_id' => $request->filled('folder_id') ? $request->input('folder_id') : null,
        ]);

        $validated = $request->validate([
            'folder_id' => 'nullable|integer|exists:work_folders,id',
        ]);

        $folderId = $validated['folder_id'] ?? null;
        if ($folderId !== null) {
            $folder = WorkFolder::query()->findOrFail($folderId);
            abort_unless((int) $folder->organization_id === (int) $work->organization_id, 403);
        }

        $work->update(['folder_id' => $folderId]);

        $message = $folderId ? 'تم نقل النشاط للفولدر' : 'تم إزالة النشاط من الفولدر';

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'ok' => true,
                'folder_id' => $folderId,
                'message' => $message,
            ]);
        }

        $view = $request->input('return_view', 'folder');
        if (! in_array($view, ['title', 'month', 'folder'], true)) {
            $view = 'folder';
        }

        return redirect()
            ->route(WorkHub::routeName('index'), array_filter([
                'view' => $view === 'title' ? null : $view,
                'type' => $request->input('type'),
                'status' => $request->input('status'),
            ]))
            ->with('success', $message);
    }
}
