@extends('layouts.employee')

@section('title', 'المشاريع')
@section('page-title', 'المشاريع')
@section('page-description', 'المشاريع التي تعمل عليها')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="card p-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="w-12 h-12 logo-gradient rounded-xl flex items-center justify-center shadow-lg ml-3">
                    <span class="material-icons text-white text-xl">folder</span>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">المشاريع</h2>
                    <p class="text-gray-600">المشاريع التي تعمل عليها</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="card p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">إجمالي المشاريع</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">{{ $projects->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <span class="material-icons text-blue-600">folder</span>
                </div>
            </div>
        </div>

        <div class="card p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">نشطة</p>
                    <p class="text-2xl font-bold text-green-600 mt-1">{{ $projects->where('status', 'active')->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <span class="material-icons text-green-600">check_circle</span>
                </div>
            </div>
        </div>

        <div class="card p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">في الانتظار</p>
                    <p class="text-2xl font-bold text-yellow-600 mt-1">{{ $projects->where('status', 'pending')->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <span class="material-icons text-yellow-600">schedule</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Projects List -->
    @if($projects->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($projects as $project)
                <div class="card p-6 hover:shadow-lg transition-shadow">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex-1">
                            <h3 class="text-lg font-bold text-gray-800 mb-1">{{ $project->business_name }}</h3>
                            @if($project->client)
                                <p class="text-sm text-gray-600">{{ $project->client->name }}</p>
                            @endif
                        </div>
                        <span class="px-3 py-1 text-xs rounded-full bg-{{ $project->status_color }}-100 text-{{ $project->status_color }}-800">
                            {{ $project->status_badge }}
                        </span>
                    </div>

                    @if($project->business_description)
                        <p class="text-sm text-gray-600 mb-4 line-clamp-2">{{ Str::limit($project->business_description, 100) }}</p>
                    @endif

                    <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                        <div class="flex items-center text-xs text-gray-500">
                            <span class="material-icons text-xs ml-1">calendar_today</span>
                            {{ $project->created_at->format('Y-m-d') }}
                        </div>
                        <a href="{{ route('employee.projects.show', $project->id) }}" 
                           class="text-purple-600 hover:text-purple-700 text-sm font-medium flex items-center">
                            عرض التفاصيل
                            <span class="material-icons text-sm mr-1">arrow_forward</span>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="card p-12 text-center">
            <span class="material-icons text-gray-400 text-6xl mb-4">folder_open</span>
            <p class="text-gray-600 text-lg">لا توجد مشاريع مخصصة لك</p>
        </div>
    @endif
</div>
@endsection

