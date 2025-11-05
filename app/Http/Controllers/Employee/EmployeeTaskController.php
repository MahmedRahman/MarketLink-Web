<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\PlanTask;
use App\Models\PlanTaskFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class EmployeeTaskController extends Controller
{
    /**
     * Display the specified task.
     */
    public function show($taskId)
    {
        $employee = Auth::guard('employee')->user();
        
        if (!$employee) {
            return redirect()->route('employee.login')
                ->with('error', 'يرجى تسجيل الدخول للمتابعة');
        }
        
        $task = PlanTask::with(['monthlyPlan.project', 'goal', 'assignedEmployee', 'files', 'comments'])
            ->findOrFail($taskId);
        
        // تحميل الملفات إذا لم تكن محملة
        if (!$task->relationLoaded('files')) {
            $task->load('files');
        }

        // التحقق من الصلاحيات: إما المهمة مخصصة له أو هو مدير على المشروع
        $isAssigned = $task->assigned_to === $employee->id;
        
        // إعادة تحميل الموظف مع العلاقات
        $employee->load('projects');
        $isManager = false;
        if ($task->monthlyPlan && $task->monthlyPlan->project_id) {
            $isManager = $employee->isManagerOfProject($task->monthlyPlan->project_id);
        }

        if (!$isAssigned && !$isManager) {
            abort(403, 'ليس لديك صلاحية للوصول إلى هذه المهمة');
        }

        return view('employee.tasks.show', compact('task', 'isManager'));
    }

    /**
     * Show the form for editing the specified task.
     */
    public function edit($taskId)
    {
        $employee = Auth::guard('employee')->user();
        
        if (!$employee) {
            return redirect()->route('employee.login')
                ->with('error', 'يرجى تسجيل الدخول للمتابعة');
        }
        
        $task = PlanTask::with(['monthlyPlan.project', 'goal', 'assignedEmployee', 'files'])
            ->findOrFail($taskId);

        // التحقق من الصلاحيات: إما المهمة مخصصة له أو هو مدير على المشروع
        $isAssigned = $task->assigned_to === $employee->id;
        
        // إعادة تحميل الموظف مع العلاقات للتأكد من أن العلاقة projects محملة
        $employee->load('projects');
        $isManager = false;
        if ($task->monthlyPlan && $task->monthlyPlan->project_id) {
            $isManager = $employee->isManagerOfProject($task->monthlyPlan->project_id);
        }

        if (!$isAssigned && !$isManager) {
            abort(403, 'ليس لديك صلاحية لتعديل هذه المهمة');
        }

        // جلب الموظفين للمشروع إذا كان مدير
        $employees = collect();
        if ($isManager && $task->monthlyPlan) {
            $employees = $task->monthlyPlan->employees()->orderBy('name')->get();
        }

        return view('employee.tasks.edit', compact('task', 'isManager', 'employees'));
    }

    /**
     * Update the specified task.
     */
    public function update(Request $request, $taskId)
    {
        $employee = Auth::guard('employee')->user();
        
        if (!$employee) {
            return redirect()->route('employee.login')
                ->with('error', 'يرجى تسجيل الدخول للمتابعة');
        }
        
        $task = PlanTask::with('monthlyPlan')->findOrFail($taskId);

        // التحقق من الصلاحيات
        $isAssigned = $task->assigned_to === $employee->id;
        
        // إعادة تحميل الموظف مع العلاقات
        $employee->load('projects');
        $isManager = false;
        if ($task->monthlyPlan && $task->monthlyPlan->project_id) {
            $isManager = $employee->isManagerOfProject($task->monthlyPlan->project_id);
        }

        if (!$isAssigned && !$isManager) {
            abort(403, 'ليس لديك صلاحية لتعديل هذه المهمة');
        }

        // إذا كان مدير، يمكنه تعديل المزيد من الحقول
        if ($isManager) {
            $request->validate([
                'title' => 'sometimes|required|string|max:255',
                'status' => 'required|in:todo,in_progress,review,done',
                'description' => 'nullable|string',
                'assigned_to' => 'nullable|exists:employees,id',
                'due_date' => 'nullable|date',
                'links' => 'nullable|array',
                'links.*.title' => 'nullable|string|max:255',
                'links.*.url' => 'nullable|url|max:500',
                'attachments' => 'nullable|array',
                'attachments.*' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,zip,rar',
            ]);

            // تجهيز الروابط للـ task_data
            $taskData = $task->task_data ?? [];
            if ($request->has('links') && is_array($request->links)) {
                $links = [];
                foreach ($request->links as $link) {
                    if (!empty($link['url']) && filter_var($link['url'], FILTER_VALIDATE_URL)) {
                        $links[] = [
                            'title' => $link['title'] ?? '',
                            'url' => $link['url'],
                        ];
                    }
                }
                if (!empty($links)) {
                    $taskData['links'] = $links;
                } else {
                    unset($taskData['links']);
                }
            }

            $task->update([
                'title' => $request->title ?? $task->title,
                'status' => $request->status,
                'description' => $request->description,
                'assigned_to' => $request->has('assigned_to') ? ($request->assigned_to ?: null) : $task->assigned_to,
                'due_date' => $request->due_date ?: $task->due_date,
                'task_data' => !empty($taskData) ? $taskData : null,
            ]);

            // رفع المرفقات الجديدة إن وجدت
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    if ($file->isValid()) {
                        $fileName = $file->getClientOriginalName();
                        $filePath = $file->store('task_files/' . $task->id, 'public');
                        $fileType = $file->getClientMimeType();
                        $fileSize = $file->getSize();

                        PlanTaskFile::create([
                            'plan_task_id' => $task->id,
                            'file_name' => $fileName,
                            'file_path' => $filePath,
                            'file_type' => $fileType,
                            'file_size' => $fileSize,
                        ]);
                    }
                }
            }
        } else {
            // الموظف العادي يمكنه تعديل الحالة والوصف والروابط والمرفقات
            $request->validate([
                'status' => 'required|in:todo,in_progress,review,done',
                'description' => 'nullable|string',
                'links' => 'nullable|array',
                'links.*.title' => 'nullable|string|max:255',
                'links.*.url' => 'nullable|url|max:500',
                'attachments' => 'nullable|array',
                'attachments.*' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,zip,rar',
            ]);

            // تجهيز الروابط للـ task_data
            $taskData = $task->task_data ?? [];
            if ($request->has('links') && is_array($request->links)) {
                $links = [];
                foreach ($request->links as $link) {
                    if (!empty($link['url']) && filter_var($link['url'], FILTER_VALIDATE_URL)) {
                        $links[] = [
                            'title' => $link['title'] ?? '',
                            'url' => $link['url'],
                        ];
                    }
                }
                if (!empty($links)) {
                    $taskData['links'] = $links;
                } else {
                    unset($taskData['links']);
                }
            }

            $task->update([
                'status' => $request->status,
                'description' => $request->description,
                'task_data' => !empty($taskData) ? $taskData : null,
            ]);

            // رفع المرفقات الجديدة إن وجدت
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    if ($file->isValid()) {
                        $fileName = $file->getClientOriginalName();
                        $filePath = $file->store('task_files/' . $task->id, 'public');
                        $fileType = $file->getClientMimeType();
                        $fileSize = $file->getSize();

                        PlanTaskFile::create([
                            'plan_task_id' => $task->id,
                            'file_name' => $fileName,
                            'file_path' => $filePath,
                            'file_type' => $fileType,
                            'file_size' => $fileSize,
                        ]);
                    }
                }
            }
        }

        return redirect()->route('employee.dashboard')
            ->with('success', 'تم تحديث المهمة بنجاح');
    }

    /**
     * Delete the specified task.
     */
    public function destroy($taskId)
    {
        $employee = Auth::guard('employee')->user();
        
        $task = PlanTask::with('monthlyPlan')->findOrFail($taskId);

        // فقط المدير يمكنه حذف المهام
        $isManager = $task->monthlyPlan ? $employee->isManagerOfProject($task->monthlyPlan->project_id) : false;

        if (!$isManager) {
            abort(403, 'ليس لديك صلاحية لحذف هذه المهمة');
        }

        $task->delete();

        return redirect()->back()
            ->with('success', 'تم حذف المهمة بنجاح');
    }

    /**
     * Download a task file.
     */
    public function downloadFile($taskId, $fileId)
    {
        $employee = Auth::guard('employee')->user();
        
        if (!$employee) {
            return redirect()->route('employee.login')
                ->with('error', 'يرجى تسجيل الدخول للمتابعة');
        }
        
        $task = PlanTask::with('monthlyPlan')->findOrFail($taskId);
        $file = PlanTaskFile::findOrFail($fileId);

        // التحقق من أن الملف يتبع هذه المهمة
        if ($file->plan_task_id !== $task->id) {
            abort(404);
        }

        // التحقق من الصلاحيات
        $isAssigned = $task->assigned_to === $employee->id;
        $employee->load('projects');
        $isManager = false;
        if ($task->monthlyPlan && $task->monthlyPlan->project_id) {
            $isManager = $employee->isManagerOfProject($task->monthlyPlan->project_id);
        }

        if (!$isAssigned && !$isManager) {
            abort(403, 'ليس لديك صلاحية للوصول إلى هذا الملف');
        }

        if (!Storage::disk('public')->exists($file->file_path)) {
            abort(404, 'الملف غير موجود');
        }

        return Storage::disk('public')->download($file->file_path, $file->file_name);
    }

    /**
     * Delete a task file.
     */
    public function deleteFile($taskId, $fileId)
    {
        $employee = Auth::guard('employee')->user();
        
        if (!$employee) {
            return redirect()->route('employee.login')
                ->with('error', 'يرجى تسجيل الدخول للمتابعة');
        }
        
        $task = PlanTask::with('monthlyPlan')->findOrFail($taskId);
        $file = PlanTaskFile::findOrFail($fileId);

        // التحقق من أن الملف يتبع هذه المهمة
        if ($file->plan_task_id !== $task->id) {
            abort(404);
        }

        // التحقق من الصلاحيات - فقط المدير يمكنه حذف الملفات
        $employee->load('projects');
        $isManager = false;
        if ($task->monthlyPlan && $task->monthlyPlan->project_id) {
            $isManager = $employee->isManagerOfProject($task->monthlyPlan->project_id);
        }

        if (!$isManager) {
            abort(403, 'ليس لديك صلاحية لحذف هذا الملف');
        }

        // حذف الملف من التخزين
        if (Storage::disk('public')->exists($file->file_path)) {
            Storage::disk('public')->delete($file->file_path);
        }

        // حذف سجل الملف من قاعدة البيانات
        $file->delete();

        return redirect()->back()
            ->with('success', 'تم حذف الملف بنجاح');
    }

    /**
     * View a task file in browser.
     */
    public function viewFile($taskId, $fileId)
    {
        $employee = Auth::guard('employee')->user();
        
        if (!$employee) {
            return redirect()->route('employee.login')
                ->with('error', 'يرجى تسجيل الدخول للمتابعة');
        }
        
        $task = PlanTask::with('monthlyPlan')->findOrFail($taskId);
        $file = PlanTaskFile::findOrFail($fileId);

        // التحقق من أن الملف يتبع هذه المهمة
        if ($file->plan_task_id !== $task->id) {
            abort(404);
        }

        // التحقق من الصلاحيات
        $isAssigned = $task->assigned_to === $employee->id;
        $employee->load('projects');
        $isManager = false;
        if ($task->monthlyPlan && $task->monthlyPlan->project_id) {
            $isManager = $employee->isManagerOfProject($task->monthlyPlan->project_id);
        }

        if (!$isAssigned && !$isManager) {
            abort(403, 'ليس لديك صلاحية للوصول إلى هذا الملف');
        }

        if (!Storage::disk('public')->exists($file->file_path)) {
            abort(404, 'الملف غير موجود');
        }

        // عرض الملف في المتصفح (للصور و PDFs)
        $filePath = Storage::disk('public')->path($file->file_path);
        $mimeType = Storage::disk('public')->mimeType($file->file_path);
        
        return response()->file($filePath, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . $file->file_name . '"',
        ]);
    }
}
