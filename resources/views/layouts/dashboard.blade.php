<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'لوحة التحكم') - MarketLink</title>
    
    <!-- Cairo Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    
    <!-- Material Symbols -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">
    
    @yield('styles')
    
    <!-- Tailwind Config -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'cairo': ['Cairo', 'sans-serif'],
                    },
                    colors: {
                        primary: '#6366f1',
                        secondary: '#8b5cf6',
                        accent: '#06b6d4',
                    }
                }
            }
        }
    </script>
    
    <style>
        * {
            font-family: 'Cairo', sans-serif;
            font-display: swap;
        }
        
        .material-icons {
            font-family: 'Material Icons';
            font-weight: normal;
            font-style: normal;
            font-size: 24px;
            line-height: 1;
            letter-spacing: normal;
            text-transform: none;
            display: inline-block;
            white-space: nowrap;
            word-wrap: normal;
            direction: ltr;
            -webkit-font-feature-settings: 'liga';
            -webkit-font-smoothing: antialiased;
        }
        
        .material-symbols-outlined {
            font-family: 'Material Symbols Outlined';
            font-weight: normal;
            font-style: normal;
            font-size: 24px;
            line-height: 1;
            letter-spacing: normal;
            text-transform: none;
            display: inline-block;
            white-space: nowrap;
            word-wrap: normal;
            direction: ltr;
            -webkit-font-feature-settings: 'liga';
            -webkit-font-smoothing: antialiased;
        }
        
        :root {
            --primary: #6366f1;
            --primary-dark: #4f46e5;
            --secondary: #8b5cf6;
            --accent: #06b6d4;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --gray-50: #f8fafc;
            --gray-100: #f1f5f9;
            --gray-200: #e2e8f0;
            --gray-300: #cbd5e1;
            --gray-400: #94a3b8;
            --gray-500: #64748b;
            --gray-600: #475569;
            --gray-700: #334155;
            --gray-800: #1e293b;
            --gray-900: #0f172a;
        }
        
        body {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        }
        
        .sidebar {
            background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
            border-right: 1px solid var(--gray-200);
            box-shadow: 0 0 0 1px rgba(0, 0, 0, 0.05);
        }
        
        .sidebar-transition {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .sidebar-item {
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            border-radius: 12px;
            margin: 4px 8px;
        }
        
        .sidebar-item:hover {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            transform: translateX(4px);
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
        }
        
        .sidebar-item.active {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
        }
        
        .main-content {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .card {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 
                0 1px 3px rgba(0, 0, 0, 0.1),
                0 1px 2px rgba(0, 0, 0, 0.06);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .card:hover {
            transform: translateY(-2px);
            box-shadow: 
                0 4px 6px rgba(0, 0, 0, 0.1),
                0 2px 4px rgba(0, 0, 0, 0.06);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 2px 4px rgba(99, 102, 241, 0.3);
        }
        
        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(99, 102, 241, 0.4);
        }
        
        .table-row:hover {
            background: linear-gradient(90deg, rgba(99, 102, 241, 0.05) 0%, rgba(139, 92, 246, 0.05) 100%);
        }
        
        .header {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--gray-200);
        }
        
        .footer {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(20px);
            border-top: 1px solid var(--gray-200);
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            width: 100%;
            z-index: 100;
        }
        
        .main-content-area {
            margin-bottom: 80px; /* Space for fixed footer */
        }
        
        .logo-gradient {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        }
        
        .status-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }
        
        .status-active {
            background: rgba(16, 185, 129, 0.1);
            color: var(--success);
        }
        
        .status-inactive {
            background: rgba(239, 68, 68, 0.1);
            color: var(--danger);
        }
        
        .status-pending {
            background: rgba(245, 158, 11, 0.1);
            color: var(--warning);
        }
        
        .glass-effect {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .floating-shapes {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: -1;
        }
        
        .shape {
            position: absolute;
            border-radius: 50%;
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.1) 0%, rgba(139, 92, 246, 0.1) 100%);
            animation: float 20s ease-in-out infinite;
        }
        
        .shape:nth-child(1) {
            width: 100px;
            height: 100px;
            top: 10%;
            left: 10%;
            animation-delay: 0s;
        }
        
        .shape:nth-child(2) {
            width: 150px;
            height: 150px;
            top: 60%;
            right: 10%;
            animation-delay: 7s;
        }
        
        .shape:nth-child(3) {
            width: 80px;
            height: 80px;
            top: 80%;
            left: 20%;
            animation-delay: 14s;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-30px) rotate(180deg); }
        }
        
        .notification-dot {
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        
        /* Select2 Custom Styling */
        .select2-container--default .select2-selection--single {
            height: 48px !important;
            border: 2px solid #d1d5db !important;
            border-radius: 12px !important;
            padding: 0 12px !important;
            background: rgba(255, 255, 255, 0.9) !important;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
        }
        
        .select2-container--default .select2-selection--single:focus {
            border-color: #6366f1 !important;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1) !important;
            background: rgba(255, 255, 255, 1) !important;
        }
        
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 44px !important;
            padding-right: 20px !important;
            color: #374151 !important;
            font-family: 'Cairo', sans-serif !important;
        }
        
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 44px !important;
            right: 8px !important;
        }
        
        .select2-container--default .select2-selection--single .select2-selection__arrow b {
            border-color: #6b7280 transparent transparent transparent !important;
            border-width: 6px 6px 0 6px !important;
        }
        
        .select2-dropdown {
            border: 2px solid #e5e7eb !important;
            border-radius: 12px !important;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1) !important;
            background: white !important;
        }
        
        .select2-container--default .select2-results__option {
            padding: 12px 16px !important;
            font-family: 'Cairo', sans-serif !important;
            color: #374151 !important;
        }
        
        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%) !important;
            color: white !important;
        }
        
        .select2-container--default .select2-results__option[aria-selected=true] {
            background: rgba(99, 102, 241, 0.1) !important;
            color: #6366f1 !important;
        }
        
        .select2-container--default .select2-search--dropdown .select2-search__field {
            border: 2px solid #e5e7eb !important;
            border-radius: 8px !important;
            padding: 8px 12px !important;
            font-family: 'Cairo', sans-serif !important;
        }
        
        .select2-container--default .select2-search--dropdown .select2-search__field:focus {
            border-color: #6366f1 !important;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1) !important;
        }
        
        /* DataTables Custom Styling */
        .dataTables_wrapper {
            font-family: 'Cairo', sans-serif;
        }
        
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter,
        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_processing,
        .dataTables_wrapper .dataTables_paginate {
            color: #374151;
            font-family: 'Cairo', sans-serif;
        }
        
        .dataTables_wrapper .dataTables_length select {
            border: 2px solid #d1d5db;
            border-radius: 8px;
            padding: 4px 8px;
            font-family: 'Cairo', sans-serif;
        }
        
        .dataTables_wrapper .dataTables_filter input {
            border: 2px solid #d1d5db;
            border-radius: 8px;
            padding: 8px 12px;
            font-family: 'Cairo', sans-serif;
            margin-right: 8px;
        }
        
        .dataTables_wrapper .dataTables_filter input:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
            outline: none;
        }
        
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            border-radius: 8px;
            margin: 0 2px;
            padding: 8px 12px;
            font-family: 'Cairo', sans-serif;
        }
        
        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            color: white;
            border: none;
        }
        
        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            color: white;
            border: none;
        }
        
        .dataTables_wrapper table.dataTable {
            border-collapse: collapse;
            border-spacing: 0;
            width: 100%;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            overflow: hidden;
        }
        
        .dataTables_wrapper table.dataTable thead th {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            color: #374151;
            font-weight: 600;
            padding: 16px 12px;
            border-bottom: 2px solid #e5e7eb;
            font-family: 'Cairo', sans-serif;
        }
        
        .dataTables_wrapper table.dataTable tbody td {
            padding: 12px;
            border-bottom: 1px solid #f3f4f6;
            font-family: 'Cairo', sans-serif;
        }
        
        .dataTables_wrapper table.dataTable tbody tr:hover {
            background: linear-gradient(90deg, rgba(99, 102, 241, 0.05) 0%, rgba(139, 92, 246, 0.05) 100%);
        }
        
        .dataTables_wrapper .dataTables_processing {
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            color: #6366f1;
            font-family: 'Cairo', sans-serif;
        }
        
        .dataTables_wrapper .dataTables_empty {
            text-align: center;
            padding: 40px;
            color: #6b7280;
            font-family: 'Cairo', sans-serif;
        }
        
        /* Buttons Styling */
        .dt-buttons {
            margin-bottom: 20px;
        }
        
        .dt-buttons .btn {
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            border: none;
            border-radius: 8px;
            color: white;
            padding: 8px 16px;
            margin-left: 8px;
            font-family: 'Cairo', sans-serif;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .dt-buttons .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.4);
        }
        
        /* Content Container */
        .container {
            max-width: 100%;
            margin: 0 auto;
            padding: 0 1rem;
        }
        
        /* Ensure content stays within bounds */
        .main-content-wrapper {
            position: relative;
            z-index: 1;
        }
        
        /* Prevent content overflow */
        .content-container {
            overflow: hidden;
            position: relative;
        }
        
        /* Enhanced Action Buttons */
        .action-button {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            transform: translateY(0);
        }
        
        .action-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }
        
        .action-button:active {
            transform: translateY(0);
        }
        
        /* Primary Button Enhanced */
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            position: relative;
            overflow: hidden;
        }
        
        .btn-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }
        
        .btn-primary:hover::before {
            left: 100%;
        }
        
        /* Cancel Button Enhanced */
        .cancel-button {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border: 2px solid #dee2e6;
            color: #495057;
        }
        
        .cancel-button:hover {
            background: linear-gradient(135deg, #e9ecef 0%, #dee2e6 100%);
            border-color: #adb5bd;
            color: #343a40;
        }
        
        /* RTL Support for Action Buttons */
        .action-buttons-container {
            direction: rtl;
            text-align: center;
        }
        
        .action-buttons-container .action-button {
            margin: 0 0.5rem;
        }
        
        /* RTL Button Icons */
        .action-button i {
            margin-left: 0.75rem;
            margin-right: 0;
        }
        
        /* RTL Button Text */
        .action-button {
            text-align: center;
            font-family: 'Cairo', sans-serif;
        }
        
        /* Enhanced RTL Spacing */
        .rtl-spacing {
            gap: 2rem;
        }
        
        /* RTL Button Hover Effects */
        .action-button:hover {
            transform: translateY(-2px) translateX(2px);
        }
        
        /* RTL Button Active Effects */
        .action-button:active {
            transform: translateY(0) translateX(0);
        }
        
        /* Enhanced Layout Improvements */
        .page-header {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border: 1px solid #dee2e6;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }
        
        .page-header .logo-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }
        
        .page-header .logo-gradient:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
        }
        
        /* Icon Spacing Improvements */
        .icon-spacing {
            margin-right: 1rem;
            margin-left: 0;
        }
        
        .icon-spacing i {
            margin-right: 0.5rem;
            margin-left: 0;
        }
        
        /* Enhanced Card Design */
        .card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }
        
        .card:hover {
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
            transform: translateY(-2px);
        }
        
        /* Form Layout Improvements */
        .form-section {
            background: rgba(255, 255, 255, 0.8);
            border-radius: 1rem;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        
        .form-section h3 {
            color: #495057;
            font-weight: 600;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #e9ecef;
        }
        
        /* RTL Icon Spacing Fix */
        .rtl-icon {
            margin-right: 0.5rem;
            margin-left: 0;
        }
        
        .rtl-icon-sm {
            margin-right: 0.25rem;
            margin-left: 0;
        }
        
        .rtl-icon-lg {
            margin-right: 0.75rem;
            margin-left: 0;
        }
        
        /* RTL Button Spacing */
        .rtl-button {
            direction: rtl;
            text-align: right;
        }
        
        .rtl-button i {
            margin-right: 0.5rem;
            margin-left: 0;
        }
        
        /* RTL Action Buttons */
        .rtl-actions {
            direction: rtl;
        }
        
        .rtl-actions .action-item {
            margin-left: 0.5rem;
            margin-right: 0;
        }
        
        /* RTL Space Reverse */
        .rtl-space-reverse {
            direction: rtl;
        }
        
        .rtl-space-reverse > * + * {
            margin-right: 0.5rem;
            margin-left: 0;
        }
        
        /* Role Badges */
        .role-badge {
            @apply inline-flex items-center px-3 py-1 rounded-full text-xs font-medium;
        }
        
        .role-blue {
            @apply bg-blue-100 text-blue-800;
        }
        
        .role-green {
            @apply bg-green-100 text-green-800;
        }
        
        .role-purple {
            @apply bg-purple-100 text-purple-800;
        }
        
        .role-red {
            @apply bg-red-100 text-red-800;
        }
        
        .role-yellow {
            @apply bg-yellow-100 text-yellow-800;
        }
        
        .role-indigo {
            @apply bg-indigo-100 text-indigo-800;
        }
        
        .role-pink {
            @apply bg-pink-100 text-pink-800;
        }
        
        .role-teal {
            @apply bg-teal-100 text-teal-800;
        }
        
        .role-gray {
            @apply bg-gray-100 text-gray-800;
        }
        
        /* DataTables Pagination Fix */
        .dataTables_paginate {
            margin-top: 1rem !important;
            text-align: center !important;
        }
        
        .dataTables_paginate .paginate_button {
            display: inline-block !important;
            margin: 0 2px !important;
            padding: 8px 12px !important;
            border: 1px solid #d1d5db !important;
            border-radius: 8px !important;
            background: white !important;
            color: #374151 !important;
            text-decoration: none !important;
            font-size: 14px !important;
            font-weight: 500 !important;
            transition: all 0.3s ease !important;
        }
        
        .dataTables_paginate .paginate_button:hover {
            background: #f3f4f6 !important;
            border-color: #9ca3af !important;
            color: #111827 !important;
        }
        
        .dataTables_paginate .paginate_button.current {
            background: #3b82f6 !important;
            border-color: #3b82f6 !important;
            color: white !important;
        }
        
        .dataTables_paginate .paginate_button.disabled {
            background: #f9fafb !important;
            border-color: #e5e7eb !important;
            color: #9ca3af !important;
            cursor: not-allowed !important;
        }
        
        .dataTables_paginate .paginate_button.disabled:hover {
            background: #f9fafb !important;
            border-color: #e5e7eb !important;
            color: #9ca3af !important;
        }
        
        /* DataTables Info */
        .dataTables_info {
            margin-top: 1rem !important;
            text-align: center !important;
            color: #6b7280 !important;
            font-size: 14px !important;
        }
        
        /* DataTables Length */
        .dataTables_length {
            margin-bottom: 1rem !important;
        }
        
        .dataTables_length select {
            padding: 8px 12px !important;
            border: 1px solid #d1d5db !important;
            border-radius: 8px !important;
            background: white !important;
            color: #374151 !important;
            font-size: 14px !important;
        }
        
        /* DataTables Search */
        .dataTables_filter {
            margin-bottom: 1rem !important;
        }
        
        .dataTables_filter input {
            padding: 8px 12px !important;
            border: 1px solid #d1d5db !important;
            border-radius: 8px !important;
            background: white !important;
            color: #374151 !important;
            font-size: 14px !important;
            width: 250px !important;
        }
        
        .dataTables_filter input:focus {
            outline: none !important;
            border-color: #3b82f6 !important;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1) !important;
        }
    </style>
</head>
<body>
    <!-- Floating Background Shapes -->
    <div class="floating-shapes">
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
    </div>
    
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div id="sidebar" class="sidebar sidebar-transition w-64 flex flex-col h-screen">
            <!-- Logo -->
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center">
                    <div class="w-12 h-12 logo-gradient rounded-2xl flex items-center justify-center shadow-lg ml-4">
                        <span class="material-icons text-white text-xl">business</span>
                    </div>
                    <div class="ml-4">
                        <h1 class="text-xl font-bold text-gray-800">MarketLink</h1>
                        <p class="text-xs text-gray-500">نظام إدارة الشركات</p>
                    </div>
                </div>
            </div>
            
            <!-- Navigation -->
            <nav class="mt-6 flex-1 overflow-y-auto">
                <div class="px-4 space-y-1">
                    <!-- Dashboard -->
                    <a href="{{ route('dashboard') }}" class="sidebar-item flex items-center px-4 py-3 text-gray-700 rounded-xl {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="fas fa-tachometer-alt text-lg ml-3"></i>
                        <span class="font-medium">لوحة التحكم</span>
                    </a>
                    
                    <!-- Divider -->
                    <div class="my-6 mx-4 border-t border-gray-200"></div>
                    
                    <!-- Basic Data Section -->
                    <div class="px-2 mb-3 mt-3">
                        <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-4 py-2.5 rounded-xl border-r-4 border-primary shadow-sm">
                            <h3 class="text-sm font-bold text-gray-700 tracking-wide">بيانات أساسية</h3>
                        </div>
                    </div>
                    
                    <!-- Clients -->
                    <a href="{{ route('clients.index') }}" class="sidebar-item flex items-center px-4 py-3 text-gray-700 rounded-xl {{ request()->routeIs('clients.*') ? 'active' : '' }}">
                        <i class="fas fa-users text-lg ml-3"></i>
                        <span class="font-medium">العملاء</span>
                    </a>
                    
                    <!-- Employees -->
                    <a href="{{ route('employees.index') }}" class="sidebar-item flex items-center px-4 py-3 text-gray-700 rounded-xl {{ request()->routeIs('employees.*') ? 'active' : '' }}">
                        <i class="fas fa-users-cog text-lg ml-3"></i>
                        <span class="font-medium">الموظفين</span>
                    </a>
                    
                    <!-- Projects -->
                    <a href="{{ route('projects.index') }}" class="sidebar-item flex items-center px-4 py-3 text-gray-700 rounded-xl {{ request()->routeIs('projects.*') ? 'active' : '' }}">
                        <i class="fas fa-project-diagram text-lg ml-3"></i>
                        <span class="font-medium">المشاريع</span>
                    </a>
                    
                    <!-- Divider -->
                    <div class="my-6 mx-4 border-t border-gray-200"></div>
                    
                    <!-- Monthly Plans -->
                    <a href="{{ route('monthly-plans.index') }}" class="sidebar-item flex items-center px-4 py-3 text-gray-700 rounded-xl {{ request()->routeIs('monthly-plans.*') ? 'active' : '' }}">
                        <span class="material-icons text-lg ml-3">calendar_month</span>
                        <span class="font-medium">الخطط الشهرية</span>
                    </a>
                    
                    <!-- Divider -->
                    <div class="my-6 mx-4 border-t border-gray-200"></div>
                    
                    <!-- Reports Section -->
                    <div class="px-2 mb-3 mt-3">
                        <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-4 py-2.5 rounded-xl border-r-4 border-primary shadow-sm">
                            <h3 class="text-sm font-bold text-gray-700 tracking-wide">التقارير</h3>
                        </div>
                    </div>
                    
                    <!-- Receivables Report -->
                    <a href="{{ route('reports.receivables') }}" class="sidebar-item flex items-center px-4 py-3 text-gray-700 rounded-xl {{ request()->routeIs('reports.receivables') ? 'active' : '' }}">
                        <i class="fas fa-file-invoice-dollar text-lg ml-3"></i>
                        <span class="font-medium">المديونية</span>
                    </a>
                    
                    <!-- Profits Report -->
                    <a href="{{ route('reports.profits') }}" class="sidebar-item flex items-center px-4 py-3 text-gray-700 rounded-xl {{ request()->routeIs('reports.profits') ? 'active' : '' }}">
                        <i class="fas fa-chart-pie text-lg ml-3"></i>
                        <span class="font-medium">الأرباح</span>
                    </a>
                    
                    <!-- Employee Financial Report -->
                    <a href="{{ route('reports.employee-financial') }}" class="sidebar-item flex items-center px-4 py-3 text-gray-700 rounded-xl {{ request()->routeIs('reports.employee-financial') ? 'active' : '' }}">
                        <i class="fas fa-user-tie text-lg ml-3"></i>
                        <span class="font-medium">مالي الموظفين</span>
                    </a>
                    
                    <!-- Total Employees Financial Report -->
                    <a href="{{ route('reports.total-employees-financial') }}" class="sidebar-item flex items-center px-4 py-3 text-gray-700 rounded-xl {{ request()->routeIs('reports.total-employees-financial') ? 'active' : '' }}">
                        <i class="fas fa-users-cog text-lg ml-3"></i>
                        <span class="font-medium">إجمالي الموظفين</span>
                    </a>
                    
                    <!-- Employees Data Report -->
                    <a href="{{ route('reports.employees-data') }}" class="sidebar-item flex items-center px-4 py-3 text-gray-700 rounded-xl {{ request()->routeIs('reports.employees-data') ? 'active' : '' }}">
                        <i class="fas fa-user-circle text-lg ml-3"></i>
                        <span class="font-medium">بيانات الموظفين</span>
                    </a>
                    
                    <!-- Divider -->
                    <div class="my-6 mx-4 border-t border-gray-200"></div>
                    
                    @if(Auth::check() && Auth::user()->is_admin)
                    <!-- Admin Panel -->
                    <a href="{{ route('admin.dashboard') }}" class="sidebar-item flex items-center px-4 py-3 rounded-xl {{ request()->routeIs('admin.*') ? 'active' : '' }}" style="background: linear-gradient(135deg, #f59e0b 0%, #f97316 100%); color: white;">
                        <i class="fas fa-shield-alt text-lg ml-3"></i>
                        <span class="font-medium">لوحة تحكم المدير</span>
                    </a>
                    @endif
                    
                    <!-- Divider -->
                    <div class="my-6 mx-4 border-t border-gray-200"></div>
                    
                    <!-- Settings -->
                    <a href="{{ route('profile.edit') }}" class="sidebar-item flex items-center px-4 py-3 text-gray-700 rounded-xl {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                        <i class="fas fa-cog text-lg ml-3"></i>
                        <span class="font-medium">الإعدادات</span>
                    </a>
                </div>
            </nav>
        </div>
        
        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col overflow-hidden main-content-area">
            <!-- Header -->
            <header class="header">
                <div class="flex items-center justify-between px-6 py-4">
                    <!-- Page Title -->
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">@yield('page-title', 'لوحة التحكم')</h1>
                        <p class="text-sm text-gray-500">@yield('page-description', 'مرحباً بك في نظام إدارة شركات التسويق الإلكتروني')</p>
                    </div>
                    
                    <!-- User Menu -->
                    <div class="flex items-center space-x-4">
                        <!-- Notifications -->
                        <button class="relative p-3 text-gray-400 hover:text-gray-600 transition-colors rounded-xl hover:bg-gray-100">
                            <i class="fas fa-bell"></i>
                            <span class="absolute -top-1 -right-1 w-3 h-3 bg-red-500 rounded-full notification-dot"></span>
                        </button>
                        
                        <!-- User Profile -->
                        <a href="{{ route('profile.edit') }}" class="flex items-center space-x-3 bg-gray-50 rounded-xl p-2 hover:bg-gray-100 transition-colors">
                            <div class="w-10 h-10 logo-gradient rounded-xl flex items-center justify-center shadow-md ml-4">
                                <i class="fas fa-user text-white text-sm"></i>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium text-gray-800">{{ Auth::user()->name }}</p>
                                <p class="text-xs text-gray-500">مدير النظام</p>
                            </div>
                        </a>
                        
                        <!-- Logout -->
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="p-3 text-gray-400 hover:text-red-600 transition-colors rounded-xl hover:bg-red-50" title="تسجيل الخروج">
                                <i class="fas fa-sign-out-alt"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </header>
            
            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto bg-gray-50 main-content-wrapper">
                <div class="p-6 content-container">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>
    
    <!-- Footer - Fixed at bottom, full width -->
    <footer class="footer px-6 py-4">
        <div class="flex items-center justify-between">
            <div class="text-sm text-gray-500">
                © 2024 MarketLink. جميع الحقوق محفوظة.
            </div>
            <div class="text-sm text-gray-500">
                نظام إدارة شركات التسويق الإلكتروني
            </div>
        </div>
    </footer>
    
    <!-- JavaScript Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- DataTables JavaScript -->
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    
    <!-- JavaScript -->
    <script>
        // Initialize Select2
        $(document).ready(function() {
            $('.select2').select2({
                placeholder: 'اختر من القائمة',
                allowClear: true,
                dir: 'rtl',
                width: '100%',
                language: {
                    noResults: function() {
                        return 'لا توجد نتائج';
                    },
                    searching: function() {
                        return 'جاري البحث...';
                    }
                }
            });
        });
        
        // Custom Select2 styling
        $('.select2-container').addClass('select2-custom');
        
        // Sidebar toggle functionality
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('-translate-x-full');
        }
        
        // Mobile responsive
        if (window.innerWidth < 768) {
            document.getElementById('sidebar').classList.add('-translate-x-full');
        }
        
        // SweetAlert2 configuration
        window.Swal = Swal;
        
        // Global delete confirmation
        function confirmDelete(url, title = 'تأكيد الحذف', text = 'هل أنت متأكد من حذف هذا العنصر؟') {
            Swal.fire({
                title: title,
                text: text,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'نعم، احذف',
                cancelButtonText: 'إلغاء',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Use form submission with a hidden form to avoid browser security warnings
                    const csrfToken = document.querySelector('meta[name="csrf-token"]');
                    const token = csrfToken ? csrfToken.getAttribute('content') : '';
                    
                    // Create a hidden form
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = url;
                    form.style.display = 'none';
                    
                    // Add CSRF token
                    if (token) {
                        const csrfInput = document.createElement('input');
                        csrfInput.type = 'hidden';
                        csrfInput.name = '_token';
                        csrfInput.value = token;
                        form.appendChild(csrfInput);
                    }
                    
                    // Add method spoofing
                    const methodInput = document.createElement('input');
                    methodInput.type = 'hidden';
                    methodInput.name = '_method';
                    methodInput.value = 'DELETE';
                    form.appendChild(methodInput);
                    
                    // Add to body and submit
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }
        
        // Success message
        function showSuccess(message) {
            Swal.fire({
                title: 'تم بنجاح!',
                text: message,
                icon: 'success',
                confirmButtonText: 'حسناً'
            });
        }
        
        // Error message
        function showError(message) {
            Swal.fire({
                title: 'خطأ!',
                text: message,
                icon: 'error',
                confirmButtonText: 'حسناً'
            });
        }
    </script>
    
    @yield('scripts')
</body>
</html>
