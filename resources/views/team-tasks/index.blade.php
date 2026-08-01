@extends('layouts.dashboard')

@section('title', 'متابعة مهام الفريق')
@section('page-title', 'متابعة مهام الفريق')
@section('page-description', 'المهام الحالية المسندة لكل موظف حسب مرحلتها الآن')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    {{-- KPIs --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="card rounded-2xl p-4 flex items-center gap-3">
            <div class="w-11 h-11 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center">
                <span class="material-icons">bolt</span>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-800">{{ $kpis['active_total'] }}</p>
                <p class="text-xs text-gray-500">مهام نشطة</p>
            </div>
        </div>
        <div class="card rounded-2xl p-4 flex items-center gap-3">
            <div class="w-11 h-11 rounded-xl bg-red-100 text-red-600 flex items-center justify-center">
                <span class="material-icons">warning_amber</span>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-800">{{ $kpis['overdue_total'] }}</p>
                <p class="text-xs text-gray-500">متأخرة</p>
            </div>
        </div>
        <div class="card rounded-2xl p-4 flex items-center gap-3">
            <div class="w-11 h-11 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center">
                <span class="material-icons">trending_up</span>
            </div>
            <div class="min-w-0">
                <p class="text-sm font-bold text-gray-800 truncate">
                    @if($kpis['busiest'])
                        {{ $kpis['busiest']['employee']->name ?? '—' }}
                    @else
                        —
                    @endif
                </p>
                <p class="text-xs text-gray-500">
                    الأكثر تحميلًا
                    @if($kpis['busiest'])
                        ({{ $kpis['busiest']['active_count'] }})
                    @endif
                </p>
            </div>
        </div>
        <div class="card rounded-2xl p-4 flex items-center gap-3">
            <div class="w-11 h-11 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center">
                <span class="material-icons">person_off</span>
            </div>
            <div class="min-w-0">
                <p class="text-sm font-bold text-gray-800 truncate">
                    {{ $kpis['idle']?->name ?? 'لا يوجد' }}
                </p>
                <p class="text-xs text-gray-500">
                    فاضي
                    @if($kpis['idle_count'] > 1)
                        (+{{ $kpis['idle_count'] - 1 }})
                    @endif
                </p>
            </div>
        </div>
    </div>

    {{-- فلاتر --}}
    <form method="GET" action="{{ route('team-tasks.index') }}" class="card rounded-2xl p-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-3">
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">المرحلة</label>
                <select name="stage" onchange="this.form.submit()" class="w-full px-3 py-2 rounded-xl border-2 border-gray-200 text-sm focus:border-primary focus:outline-none">
                    <option value="">كل المراحل</option>
                    @foreach($stages as $key => $label)
                        <option value="{{ $key }}" @selected(($filters['stage'] ?? '') === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">الحالة</label>
                <select name="state" onchange="this.form.submit()" class="w-full px-3 py-2 rounded-xl border-2 border-gray-200 text-sm focus:border-primary focus:outline-none">
                    <option value="" @selected(empty($filters['state']))>كل الحالات</option>
                    <option value="active" @selected(($filters['state'] ?? '') === 'active')>نشطة فقط</option>
                    <option value="overdue" @selected(($filters['state'] ?? '') === 'overdue')>متأخرة فقط</option>
                    <option value="done" @selected(($filters['state'] ?? '') === 'done')>منجزة</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">النشاط</label>
                <select name="activity_id" onchange="this.form.submit()" class="w-full px-3 py-2 rounded-xl border-2 border-gray-200 text-sm focus:border-primary focus:outline-none">
                    <option value="">كل الأنشطة</option>
                    @foreach($activities as $activity)
                        <option value="{{ $activity->id }}" @selected((int) ($filters['activity_id'] ?? 0) === (int) $activity->id)>{{ $activity->title }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">الموظف</label>
                <select name="employee_id" onchange="this.form.submit()" class="w-full px-3 py-2 rounded-xl border-2 border-gray-200 text-sm focus:border-primary focus:outline-none">
                    <option value="">كل الموظفين</option>
                    @foreach($employees as $emp)
                        <option value="{{ $emp->id }}" @selected((int) ($filters['employee_id'] ?? 0) === (int) $emp->id)>{{ $emp->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">الدور الوظيفي</label>
                <select name="role" onchange="this.form.submit()" class="w-full px-3 py-2 rounded-xl border-2 border-gray-200 text-sm focus:border-primary focus:outline-none">
                    <option value="">الكل</option>
                    @foreach($roleLabels as $key => $label)
                        <option value="{{ $key }}" @selected(($filters['role'] ?? '') === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">بحث</label>
                <div class="flex gap-2">
                    <input type="text" name="q" value="{{ $filters['q'] ?? '' }}"
                           placeholder="عنوان تاسك أو نشاط..."
                           class="flex-1 px-3 py-2 rounded-xl border-2 border-gray-200 text-sm focus:border-primary focus:outline-none">
                    <button type="submit" class="btn-primary text-white px-3 py-2 rounded-xl flex items-center">
                        <span class="material-icons text-lg">search</span>
                    </button>
                </div>
            </div>
        </div>

        @if(request()->hasAny(['stage', 'state', 'activity_id', 'employee_id', 'role', 'q']))
            <div class="mt-3">
                <a href="{{ route('team-tasks.index') }}" class="text-sm text-gray-500 hover:text-gray-700 inline-flex items-center gap-1">
                    <span class="material-icons text-base">filter_alt_off</span>
                    مسح الفلاتر
                </a>
            </div>
        @endif
    </form>

    {{-- مجموعات الموظفين --}}
    @if($groups->isEmpty())
        <div class="card rounded-2xl p-12 text-center">
            <span class="material-icons text-6xl text-gray-300">supervisor_account</span>
            <h3 class="text-lg font-bold text-gray-700 mt-4">لا توجد مهام مطابقة</h3>
            <p class="text-gray-500 text-sm mt-1">جرّب تغيير الفلاتر أو تأكد من وجود تاسكات معيّنة للموظفين</p>
        </div>
    @else
        <div class="space-y-5">
            @foreach($groups as $group)
                @php
                    /** @var \App\Models\Employee|null $employee */
                    $employee = $group['employee'];
                @endphp
                <section class="card rounded-2xl overflow-hidden {{ $group['overdue_count'] > 0 ? 'ring-1 ring-red-200' : '' }}">
                    <div class="px-5 py-4 bg-gradient-to-l {{ $group['overdue_count'] > 0 ? 'from-red-50 to-white' : 'from-slate-50 to-white' }} border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-11 h-11 rounded-xl bg-indigo-600 text-white flex items-center justify-center shrink-0 font-bold text-lg">
                                {{ mb_substr($employee->name ?? '؟', 0, 1) }}
                            </div>
                            <div class="min-w-0">
                                <h2 class="font-bold text-gray-900 text-lg leading-tight truncate">
                                    @if($employee)
                                        <a href="{{ route('employees.show', $employee) }}" class="hover:text-indigo-700 hover:underline">
                                            {{ $employee->name }}
                                        </a>
                                    @else
                                        موظف محذوف
                                    @endif
                                </h2>
                                <p class="text-xs text-gray-500 mt-0.5">{{ $employee->role_badge ?? '—' }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 flex-wrap">
                            <span class="text-sm text-gray-600">
                                <span class="font-bold text-gray-800">{{ $group['active_count'] }}</span> مهمة نشطة
                                <span class="mx-1 text-gray-300">·</span>
                                <span class="font-bold {{ $group['overdue_count'] > 0 ? 'text-red-600' : 'text-gray-800' }}">{{ $group['overdue_count'] }}</span>
                                <span class="{{ $group['overdue_count'] > 0 ? 'text-red-600' : 'text-gray-600' }}">متأخرة</span>
                            </span>
                            @if($employee)
                                <a href="{{ route('employees.show', $employee) }}"
                                   class="inline-flex items-center gap-1 text-sm font-medium text-indigo-700 hover:text-indigo-900 px-3 py-1.5 rounded-lg bg-indigo-50 hover:bg-indigo-100 transition-colors">
                                    <span class="material-icons text-base">person</span>
                                    عرض التفاصيل
                                </a>
                            @endif
                        </div>
                    </div>

                    <div class="p-4 space-y-3">
                        @forelse($group['rows'] as $row)
                            @php
                                /** @var \App\Models\WorkTask $task */
                                $task = $row['task'];
                                $activity = $task->activity;
                            @endphp
                            <div class="rounded-xl border-2 {{ $task->is_overdue ? 'border-red-200 bg-red-50/40' : 'border-gray-100 bg-white' }} p-4">
                                <div class="flex flex-col md:flex-row md:items-start justify-between gap-3">
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-start gap-2 flex-wrap">
                                            @if($activity)
                                                <a href="{{ route('work.tasks.show', [$activity, $task]) }}" class="font-bold text-gray-900 leading-snug hover:text-indigo-700 hover:underline">
                                                    {{ $task->title }}
                                                </a>
                                            @else
                                                <h3 class="font-bold text-gray-900 leading-snug">{{ $task->title }}</h3>
                                            @endif
                                            @if($task->content_type_label)
                                                <span class="text-[11px] font-semibold px-2 py-0.5 rounded-full bg-slate-100 text-slate-700">{{ $task->content_type_label }}</span>
                                            @elseif($task->kind_label)
                                                <span class="text-[11px] font-semibold px-2 py-0.5 rounded-full bg-slate-100 text-slate-700">{{ $task->kind_label }}</span>
                                            @endif
                                            @if($task->is_overdue)
                                                <span class="text-[11px] font-bold px-2 py-0.5 rounded-full bg-red-600 text-white">متأخرة</span>
                                            @endif
                                        </div>

                                        @if($activity)
                                            <a href="{{ route('work.show', $activity) }}"
                                               class="text-sm text-indigo-700 mt-1.5 truncate inline-flex items-center gap-1 hover:underline"
                                               onclick="event.stopPropagation()">
                                                <span class="material-icons text-sm">folder_open</span>
                                                {{ $activity->title }}
                                            </a>
                                        @endif

                                        <div class="flex flex-wrap items-center gap-2 mt-2.5 text-xs text-gray-600">
                                            <span class="role-badge role-{{ $task->pipeline_stage_color }} inline-flex items-center gap-1">
                                                <span class="material-icons text-sm">{{ $task->pipeline_stage_icon }}</span>
                                                {{ $task->pipeline_stage_label }}
                                            </span>
                                            <span class="role-badge role-indigo inline-flex items-center gap-1">
                                                <span class="material-icons text-sm">badge</span>
                                                {{ $row['task_role_label'] }}
                                            </span>
                                            @if($task->status_label ?? null)
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-gray-100 text-gray-700">
                                                    {{ $task->status_label }}
                                                </span>
                                            @endif
                                            @if($task->due_date)
                                                <span class="inline-flex items-center gap-1 {{ $task->is_overdue ? 'text-red-600 font-semibold' : '' }}">
                                                    <span class="material-icons text-sm">event</span>
                                                    تسليم: {{ $task->due_date->format('Y/m/d') }}
                                                </span>
                                            @endif
                                            @if($task->publish_date)
                                                <span class="inline-flex items-center gap-1">
                                                    <span class="material-icons text-sm">publish</span>
                                                    نشر: {{ $task->publish_date->format('Y/m/d') }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    @if($activity)
                                        <a href="{{ route('work.tasks.show', [$activity, $task]) }}"
                                           class="inline-flex items-center gap-1 text-sm font-medium text-primary hover:underline shrink-0">
                                            فتح التاسك
                                            <span class="material-icons text-base">arrow_back</span>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="rounded-xl border-2 border-dashed border-emerald-200 bg-emerald-50/40 px-4 py-6 text-center">
                                <p class="text-sm font-semibold text-emerald-800">مفيش مهام حالية مع الموظف ده</p>
                                <p class="text-xs text-emerald-700/80 mt-1">فاضي في المرحلة الحالية</p>
                            </div>
                        @endforelse
                    </div>
                </section>
            @endforeach
        </div>
    @endif

    @if(($showIdleSection ?? false) && ($idleEmployees ?? collect())->isNotEmpty() && empty($filters['employee_id']) && empty($filters['q']) && empty($filters['activity_id']) && empty($filters['stage']) && empty($filters['state']) && empty($filters['role']))
        <div class="card rounded-2xl p-5">
            <h3 class="text-sm font-bold text-gray-700 mb-3 flex items-center gap-2">
                <span class="material-icons text-emerald-600">hourglass_empty</span>
                موظفون بدون مهام ظاهرة ({{ $idleEmployees->count() }})
            </h3>
            <div class="flex flex-wrap gap-2">
                @foreach($idleEmployees as $idle)
                    <a href="{{ route('employees.show', $idle) }}"
                       class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl border border-gray-200 text-sm text-gray-700 hover:bg-gray-50">
                        {{ $idle->name }}
                        <span class="text-xs text-gray-400">{{ $idle->role_badge }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
