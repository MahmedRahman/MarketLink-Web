@extends('layouts.dashboard')

@section('title', 'لوحة التحكم')
@section('page-title', 'لوحة التحكم')
@section('page-description', 'مرحباً بك في نظام إدارة شركات التسويق الإلكتروني')

@section('content')
<div class="space-y-6">
    <!-- Welcome Card -->
    <div class="card rounded-2xl p-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 mb-2">مرحباً بك، {{ Auth::user()->name }}!</h2>
                <p class="text-gray-600">نظام إدارة شركات التسويق الإلكتروني جاهز للاستخدام</p>
            </div>
            <div class="w-16 h-16 bg-gradient-to-r from-purple-500 to-blue-500 rounded-2xl flex items-center justify-center">
                <span class="material-icons text-white text-2xl">dashboard</span>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Clients -->
        <div class="card rounded-2xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">إجمالي العملاء</p>
                    <p class="text-3xl font-bold text-gray-800">0</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <span class="material-icons text-blue-600">people</span>
                </div>
            </div>
        </div>

        <!-- Total Companies -->
        <div class="card rounded-2xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">إجمالي الشركات</p>
                    <p class="text-3xl font-bold text-gray-800">0</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                    <span class="material-icons text-green-600">business</span>
                </div>
            </div>
        </div>

        <!-- Total Pages -->
        <div class="card rounded-2xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">إجمالي الصفحات</p>
                    <p class="text-3xl font-bold text-gray-800">0</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                    <span class="material-icons text-purple-600">web</span>
                </div>
            </div>
        </div>

        <!-- Active Projects -->
        <div class="card rounded-2xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">المشاريع النشطة</p>
                    <p class="text-3xl font-bold text-gray-800">0</p>
                </div>
                <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center">
                    <span class="material-icons text-orange-600">work</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Clients -->
        <div class="card rounded-2xl p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800">العملاء الأخيرين</h3>
                <a href="#" class="text-sm text-blue-600 hover:text-blue-500">عرض الكل</a>
            </div>
            <div class="space-y-3">
                <div class="text-center py-8 text-gray-500">
                    <span class="material-icons text-4xl mb-2">people_outline</span>
                    <p>لا يوجد عملاء بعد</p>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="card rounded-2xl p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800">النشاط الأخير</h3>
                <a href="#" class="text-sm text-blue-600 hover:text-blue-500">عرض الكل</a>
            </div>
            <div class="space-y-3">
                <div class="text-center py-8 text-gray-500">
                    <span class="material-icons text-4xl mb-2">timeline</span>
                    <p>لا يوجد نشاط بعد</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="card rounded-2xl p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">الإجراءات السريعة</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="{{ route('clients.index') }}" class="btn-primary text-white px-6 py-3 rounded-xl flex items-center justify-center hover:no-underline">
                <span class="material-icons text-sm mr-2">person_add</span>
                إدارة العملاء
            </a>
            <a href="#" class="bg-gray-100 text-gray-700 px-6 py-3 rounded-xl flex items-center justify-center hover:bg-gray-200 transition-colors">
                <span class="material-icons text-sm mr-2">business</span>
                إدارة الشركات
            </a>
            <a href="#" class="bg-gray-100 text-gray-700 px-6 py-3 rounded-xl flex items-center justify-center hover:bg-gray-200 transition-colors">
                <span class="material-icons text-sm mr-2">web</span>
                إدارة الصفحات
            </a>
        </div>
    </div>
</div>
@endsection
