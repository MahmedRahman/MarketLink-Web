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
        $driver = DB::getDriverName();
        
        if ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys=off;');
            
            // Drop temporary table if exists
            DB::statement('DROP TABLE IF EXISTS plan_tasks_new');
            
            // Create new table with updated status enum including 'publish'
            DB::statement("
                CREATE TABLE plan_tasks_new (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    monthly_plan_id INTEGER NOT NULL,
                    goal_id INTEGER NULL,
                    assigned_to INTEGER NULL,
                    title VARCHAR(255) NOT NULL,
                    description TEXT NULL,
                    status VARCHAR(20) NOT NULL DEFAULT 'todo' CHECK(status IN ('todo', 'in_progress', 'review', 'done', 'publish', 'archived')),
                    list_type VARCHAR(20) NOT NULL DEFAULT 'tasks' CHECK(list_type IN ('tasks', 'employee', 'ready', 'publish')),
                    `order` INTEGER NOT NULL DEFAULT 0,
                    due_date DATE NULL,
                    task_data TEXT NULL,
                    color VARCHAR(7) NULL,
                    created_at TIMESTAMP NULL,
                    updated_at TIMESTAMP NULL,
                    FOREIGN KEY (monthly_plan_id) REFERENCES monthly_plans(id) ON DELETE CASCADE,
                    FOREIGN KEY (assigned_to) REFERENCES employees(id) ON DELETE SET NULL,
                    FOREIGN KEY (goal_id) REFERENCES monthly_plan_goals(id) ON DELETE SET NULL
                )
            ");
            
            // Copy data from old table to new table
            // Ensure status values are valid before copying
            DB::statement("
                INSERT INTO plan_tasks_new 
                SELECT 
                    id, monthly_plan_id, goal_id, assigned_to, title, description,
                    CASE 
                        WHEN status IN ('todo', 'in_progress', 'review', 'done', 'publish', 'archived') THEN status
                        ELSE 'todo'
                    END as status,
                    list_type, `order`, due_date, task_data, color, created_at, updated_at
                FROM plan_tasks
            ");
            
            // Drop old table
            DB::statement('DROP TABLE plan_tasks');
            
            // Rename new table
            DB::statement('ALTER TABLE plan_tasks_new RENAME TO plan_tasks');
            
            DB::statement('PRAGMA foreign_keys=on;');
        } else {
            // MySQL/MariaDB support MODIFY COLUMN
            DB::statement("ALTER TABLE plan_tasks MODIFY COLUMN status ENUM('todo', 'in_progress', 'review', 'done', 'publish', 'archived') DEFAULT 'todo'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::getDriverName();
        
        if ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys=off;');
            
            // Create new table without 'publish' status
            DB::statement("
                CREATE TABLE plan_tasks_new (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    monthly_plan_id INTEGER NOT NULL,
                    goal_id INTEGER NULL,
                    assigned_to INTEGER NULL,
                    title VARCHAR(255) NOT NULL,
                    description TEXT NULL,
                    status VARCHAR(20) NOT NULL DEFAULT 'todo' CHECK(status IN ('todo', 'in_progress', 'review', 'done', 'archived')),
                    list_type VARCHAR(20) NOT NULL DEFAULT 'tasks' CHECK(list_type IN ('tasks', 'employee', 'ready', 'publish')),
                    `order` INTEGER NOT NULL DEFAULT 0,
                    due_date DATE NULL,
                    task_data TEXT NULL,
                    color VARCHAR(7) NULL,
                    created_at TIMESTAMP NULL,
                    updated_at TIMESTAMP NULL,
                    FOREIGN KEY (monthly_plan_id) REFERENCES monthly_plans(id) ON DELETE CASCADE,
                    FOREIGN KEY (assigned_to) REFERENCES employees(id) ON DELETE SET NULL,
                    FOREIGN KEY (goal_id) REFERENCES monthly_plan_goals(id) ON DELETE SET NULL
                )
            ");
            
            // Copy data (convert publish tasks to 'done')
            DB::statement("
                INSERT INTO plan_tasks_new 
                SELECT 
                    id, monthly_plan_id, goal_id, assigned_to, title, description,
                    CASE WHEN status = 'publish' THEN 'done' ELSE status END as status,
                    list_type, `order`, due_date, task_data, color, created_at, updated_at
                FROM plan_tasks
            ");
            
            DB::statement('DROP TABLE plan_tasks');
            DB::statement('ALTER TABLE plan_tasks_new RENAME TO plan_tasks');
            
            DB::statement('PRAGMA foreign_keys=on;');
        } else {
            // MySQL/MariaDB
            DB::statement("ALTER TABLE plan_tasks MODIFY COLUMN status ENUM('todo', 'in_progress', 'review', 'done', 'archived') DEFAULT 'todo'");
        }
    }
};
