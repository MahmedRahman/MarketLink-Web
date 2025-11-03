<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // في SQLite، نحتاج إلى إعادة إنشاء الجدول لتعديل enum
        if (DB::getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys=off;');
            
            // إنشاء جدول جديد بالبنية المحدثة
            DB::statement("
                CREATE TABLE plan_tasks_new (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    monthly_plan_id INTEGER NOT NULL,
                    assigned_to INTEGER NULL,
                    title VARCHAR(255) NOT NULL,
                    description TEXT NULL,
                    status VARCHAR(20) NOT NULL DEFAULT 'todo',
                    list_type VARCHAR(20) NOT NULL DEFAULT 'tasks' CHECK(list_type IN ('tasks', 'employee', 'ready', 'publish')),
                    `order` INTEGER NOT NULL DEFAULT 0,
                    due_date DATE NULL,
                    task_data TEXT NULL,
                    color VARCHAR(7) NULL,
                    created_at TIMESTAMP NULL,
                    updated_at TIMESTAMP NULL,
                    FOREIGN KEY (monthly_plan_id) REFERENCES monthly_plans(id) ON DELETE CASCADE,
                    FOREIGN KEY (assigned_to) REFERENCES employees(id) ON DELETE SET NULL
                )
            ");
            
            // نسخ البيانات
            DB::statement('INSERT INTO plan_tasks_new SELECT * FROM plan_tasks');
            
            // حذف الجدول القديم
            DB::statement('DROP TABLE plan_tasks');
            
            // إعادة تسمية الجدول الجديد
            DB::statement('ALTER TABLE plan_tasks_new RENAME TO plan_tasks');
            
            DB::statement('PRAGMA foreign_keys=on;');
        } else {
            // MySQL/MariaDB
            DB::statement("ALTER TABLE plan_tasks MODIFY COLUMN list_type ENUM('tasks', 'employee', 'ready', 'publish') DEFAULT 'tasks'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys=off;');
            
            // إنشاء جدول جديد بالبنية الأصلية
            DB::statement("
                CREATE TABLE plan_tasks_new (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    monthly_plan_id INTEGER NOT NULL,
                    assigned_to INTEGER NULL,
                    title VARCHAR(255) NOT NULL,
                    description TEXT NULL,
                    status VARCHAR(20) NOT NULL DEFAULT 'todo',
                    list_type VARCHAR(20) NOT NULL DEFAULT 'tasks' CHECK(list_type IN ('tasks', 'employee')),
                    `order` INTEGER NOT NULL DEFAULT 0,
                    due_date DATE NULL,
                    task_data TEXT NULL,
                    color VARCHAR(7) NULL,
                    created_at TIMESTAMP NULL,
                    updated_at TIMESTAMP NULL,
                    FOREIGN KEY (monthly_plan_id) REFERENCES monthly_plans(id) ON DELETE CASCADE,
                    FOREIGN KEY (assigned_to) REFERENCES employees(id) ON DELETE SET NULL
                )
            ");
            
            // نسخ البيانات (إزالة المهام التي ليست tasks أو employee)
            DB::statement("INSERT INTO plan_tasks_new SELECT * FROM plan_tasks WHERE list_type IN ('tasks', 'employee')");
            
            // حذف الجدول القديم
            DB::statement('DROP TABLE plan_tasks');
            
            // إعادة تسمية الجدول الجديد
            DB::statement('ALTER TABLE plan_tasks_new RENAME TO plan_tasks');
            
            DB::statement('PRAGMA foreign_keys=on;');
        } else {
            // MySQL/MariaDB
            DB::statement("ALTER TABLE plan_tasks MODIFY COLUMN list_type ENUM('tasks', 'employee') DEFAULT 'tasks'");
        }
    }
};
