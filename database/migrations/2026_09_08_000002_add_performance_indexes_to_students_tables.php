<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private function hasIndex(string $table, string $indexName): bool
    {
        try {
            $driver = DB::getDriverName();
            if ($driver === 'sqlite') {
                $indexes = DB::select("PRAGMA index_list('{$table}')");
                return collect($indexes)->pluck('name')->contains($indexName);
            }
            $indexes = DB::select("SHOW INDEX FROM `{$table}` WHERE Key_name = ?", [$indexName]);
            return !empty($indexes);
        } catch (\Throwable $e) {
            return false;
        }
    }

    public function up(): void
    {
        if (Schema::hasTable('students')) {
            Schema::table('students', function (Blueprint $table) {
                if (!$this->hasIndex('students', 'students_course_index')) {
                    $table->index('course', 'students_course_index');
                }
                if (!$this->hasIndex('students', 'students_batch_index')) {
                    $table->index('batch', 'students_batch_index');
                }
                if (!$this->hasIndex('students', 'students_status_index')) {
                    $table->index('status', 'students_status_index');
                }
            });
        }

        if (Schema::hasTable('student_courses')) {
            Schema::table('student_courses', function (Blueprint $table) {
                if (!$this->hasIndex('student_courses', 'student_courses_session_student_idx')) {
                    $table->index(['session_id', 'student_id'], 'student_courses_session_student_idx');
                }
                if (!$this->hasIndex('student_courses', 'student_courses_student_session_idx')) {
                    $table->index(['student_id', 'session_id'], 'student_courses_student_session_idx');
                }
            });
        }

        if (Schema::hasTable('student_certificate')) {
            Schema::table('student_certificate', function (Blueprint $table) {
                if (!$this->hasIndex('student_certificate', 'student_certificate_student_type_idx')) {
                    $table->index(['student_id', 'type'], 'student_certificate_student_type_idx');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('students')) {
            Schema::table('students', function (Blueprint $table) {
                if ($this->hasIndex('students', 'students_course_index')) {
                    $table->dropIndex('students_course_index');
                }
                if ($this->hasIndex('students', 'students_batch_index')) {
                    $table->dropIndex('students_batch_index');
                }
                if ($this->hasIndex('students', 'students_status_index')) {
                    $table->dropIndex('students_status_index');
                }
            });
        }

        if (Schema::hasTable('student_courses')) {
            Schema::table('student_courses', function (Blueprint $table) {
                if ($this->hasIndex('student_courses', 'student_courses_session_student_idx')) {
                    $table->dropIndex('student_courses_session_student_idx');
                }
                if ($this->hasIndex('student_courses', 'student_courses_student_session_idx')) {
                    $table->dropIndex('student_courses_student_session_idx');
                }
            });
        }

        if (Schema::hasTable('student_certificate')) {
            Schema::table('student_certificate', function (Blueprint $table) {
                if ($this->hasIndex('student_certificate', 'student_certificate_student_type_idx')) {
                    $table->dropIndex('student_certificate_student_type_idx');
                }
            });
        }
    }
};
