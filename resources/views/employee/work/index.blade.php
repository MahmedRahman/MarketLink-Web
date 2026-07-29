@extends('layouts.employee')

@section('title', 'مساحة العمل')
@section('page-title', 'مساحة العمل')
@section('page-description', 'الأنشطة اللي فيها مهام مطلوبة منك')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl flex items-center">
            <span class="material-icons ml-2">check_circle</span>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl flex items-center">
            <span class="material-icons ml-2">error</span>
            {{ session('error') }}
        </div>
    @endif

    {{-- شريط المتابعة --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="card rounded-2xl p-4 flex items-center gap-3">
            <div class="w-11 h-11 rounded-xl bg-red-100 text-red-600 flex items-center justify-center">
                <span class="material-icons">warning_amber</span>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-800">{{ $follow['overdue']->count() }}</p>
                <p class="text-xs text-gray-500">متأخرة</p>
            </div>
        </div>
        <div class="card rounded-2xl p-4 flex items-center gap-3">
            <div class="w-11 h-11 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center">
                <span class="material-icons">bolt</span>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-800">{{ $follow['in_progress']->count() }}</p>
                <p class="text-xs text-gray-500">قيد التنفيذ</p>
            </div>
        </div>
        <div class="card rounded-2xl p-4 flex items-center gap-3">
            <div class="w-11 h-11 rounded-xl bg-yellow-100 text-yellow-600 flex items-center justify-center">
                <span class="material-icons">rate_review</span>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-800">{{ $follow['review']->count() }}</p>
                <p class="text-xs text-gray-500">بانتظار مراجعة</p>
            </div>
        </div>
        <div class="card rounded-2xl p-4 flex items-center gap-3">
            <div class="w-11 h-11 rounded-xl bg-gray-100 text-gray-600 flex items-center justify-center">
                <span class="material-icons">pending_actions</span>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-800">{{ $follow['todo']->count() }}</p>
                <p class="text-xs text-gray-500">لم تبدأ</p>
            </div>
        </div>
    </div>

    {{-- فلاتر --}}
    <div class="card rounded-2xl p-4">
        <form method="GET" class="flex flex-wrap items-center gap-2">
            <select name="type" onchange="this.form.submit()" class="px-3 py-2 rounded-xl border-2 border-gray-200 text-sm focus:border-primary focus:outline-none">
                <option value="">كل الأنواع</option>
                @foreach($types as $key => $label)
                    <option value="{{ $key }}" @selected($filterType === $key)>{{ $label }}</option>
                @endforeach
            </select>
            <select name="status" onchange="this.form.submit()" class="px-3 py-2 rounded-xl border-2 border-gray-200 text-sm focus:border-primary focus:outline-none">
                <option value="">كل الحالات</option>
                @foreach($statuses as $key => $label)
                    <option value="{{ $key }}" @selected($filterStatus === $key)>{{ $label }}</option>
                @endforeach
            </select>
            @if($filterType || $filterStatus)
                <a href="{{ route('employee.tasks.index') }}" class="text-sm text-gray-500 hover:text-gray-700 px-2">مسح</a>
            @endif
        </form>
    </div>

    {{-- فولدرات الأنشطة --}}
    @if($activities->isEmpty())
        <div class="card rounded-2xl p-12 text-center">
            <span class="material-icons text-6xl text-gray-300">folder_open</span>
            <h3 class="text-lg font-bold text-gray-700 mt-4">لا توجد أنشطة بمهام لك</h3>
            <p class="text-gray-500 text-sm mt-1">لما يتعمل لك طلب في مساحة العمل هيظهر الفولدر هنا</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($activities as $activity)
                <a href="{{ route('employee.work.activity', $activity) }}" class="card rounded-2xl p-5 block hover:no-underline hover:shadow-md transition-shadow">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex items-center gap-3">
                            <div class="w-11 h-11 rounded-xl bg-indigo-50 text-primary flex items-center justify-center">
                                <span class="material-icons">{{ $activity->type_icon }}</span>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-800 leading-tight">{{ $activity->title }}</h3>
                                <span class="text-xs text-gray-500">{{ $activity->type_label }}</span>
                            </div>
                        </div>
                        <span class="role-badge role-{{ $activity->status_color }}">{{ $activity->status_label }}</span>
                    </div>

                    @if($activity->event_date)
                        <p class="text-xs text-gray-500 flex items-center gap-1 mb-3">
                            <span class="material-icons text-sm">event</span>
                            {{ $activity->event_date->format('Y/m/d') }}
                        </p>
                    @endif

                    <div class="mt-2">
                        <div class="flex items-center justify-between text-xs text-gray-500 mb-1">
                            <span>{{ $activity->my_done_tasks_count }} / {{ $activity->my_tasks_count }} مهمة لك</span>
                            <span>{{ $activity->my_progress }}%</span>
                        </div>
                        <div class="w-full h-2 bg-gray-100 rounded-full overflow-hidden">
                            <div class="h-full bg-gradient-to-l from-indigo-500 to-purple-500 rounded-full" style="width: {{ $activity->my_progress }}%"></div>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    @endif

    @if(($planTasks ?? collect())->count() > 0)
        <div>
            <h3 class="font-bold text-gray-800 mb-3 flex items-center gap-2">
                <span class="material-icons text-indigo-600">calendar_month</span>
                مهام الخطط الشهرية
            </h3>
            @foreach($planTasks as $pt)
                <a href="{{ route('employee.tasks.show', $pt) }}"
                   class="card p-4 mb-3 flex items-center justify-between gap-3 hover:shadow-md transition-shadow">
                    <div class="min-w-0 flex-1">
                        <p class="font-semibold text-gray-800">{{ $pt->title }}</p>
                        <p class="text-xs text-gray-500 mt-0.5 truncate">
                            {{ $pt->monthlyPlan->project->business_name ?? '—' }}
                        </p>
                    </div>
                    <span class="material-icons text-gray-400 shrink-0">chevron_left</span>
                </a>
            @endforeach
        </div>
    @endif
</div>
@endsection
