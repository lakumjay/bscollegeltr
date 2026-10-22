<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

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
        if (Schema::hasTable('sessions')) {
            // 1. Fetch all existing sessions
            $sessions = DB::table('sessions')->orderBy('id')->get();
            $grouped = [];

            foreach ($sessions as $s) {
                // Normalize session name by trimming and collapsing whitespace around dashes
                $normalized = trim(preg_replace('/\s*-\s*/', '-', (string)$s->session_name));
                $normalized = trim(preg_replace('/\s+/', ' ', $normalized));
                $key = strtolower($normalized);

                if (!isset($grouped[$key])) {
                    $grouped[$key] = [
                        'primary_id' => $s->id,
                        'name' => $normalized,
                        'duplicate_ids' => [],
                    ];
                    // Update primary with normalized name if different
                    if ($s->session_name !== $normalized) {
                        DB::table('sessions')->where('id', $s->id)->update(['session_name' => $normalized]);
                    }
                } else {
                    $grouped[$key]['duplicate_ids'][] = $s->id;
                }
            }

            // 2. Re-point foreign keys and delete duplicates
            foreach ($grouped as $group) {
                $primaryId = $group['primary_id'];
                foreach ($group['duplicate_ids'] as $dupId) {
                    $tables = [
                        'students',
                        'student_courses',
                        'fee_records',
                        'fee_payments',
                        'fee_structures',
                        'incomes',
                        'expenses',
                        'marks',
                        'grading_scales',
                        'exams',
                    ];

                    foreach ($tables as $tbl) {
                        if (Schema::hasTable($tbl) && Schema::hasColumn($tbl, 'session_id')) {
                            try {
                                DB::table($tbl)->where('session_id', $dupId)->update(['session_id' => $primaryId]);
                            } catch (\Throwable $e) {}
                        }
                    }

                    if (Schema::hasTable('users') && Schema::hasColumn('users', 'active_session_id')) {
                        try {
                            DB::table('users')->where('active_session_id', $dupId)->update(['active_session_id' => $primaryId]);
                        } catch (\Throwable $e) {}
                    }

                    DB::table('sessions')->where('id', $dupId)->delete();
                }
            }

            // 3. Add UNIQUE index on session_name to prevent future duplicate sessions
            Schema::table('sessions', function (Blueprint $table) {
                if (!$this->hasIndex('sessions', 'sessions_session_name_unique')) {
                    $table->unique('session_name', 'sessions_session_name_unique');
                }
            });
        }

        // 4. Performance indexes for database speed
        if (Schema::hasTable('students')) {
            Schema::table('students', function (Blueprint $table) {
                if (!$this->hasIndex('students', 'students_roll_no_idx')) {
                    $table->index('roll_no', 'students_roll_no_idx');
                }
                if (!$this->hasIndex('students', 'students_student_id_idx')) {
                    $table->index('student_id', 'students_student_id_idx');
                }
                if (!$this->hasIndex('students', 'students_session_id_idx')) {
                    $table->index('session_id', 'students_session_id_idx');
                }
            });
        }

        if (Schema::hasTable('fee_records')) {
            Schema::table('fee_records', function (Blueprint $table) {
                if (!$this->hasIndex('fee_records', 'fee_records_student_id_idx')) {
                    $table->index('student_id', 'fee_records_student_id_idx');
                }
                if (!$this->hasIndex('fee_records', 'fee_records_session_id_idx')) {
                    $table->index('session_id', 'fee_records_session_id_idx');
                }
            });
        }

        if (Schema::hasTable('fee_payments')) {
            Schema::table('fee_payments', function (Blueprint $table) {
                if (!$this->hasIndex('fee_payments', 'fee_payments_student_id_idx')) {
                    $table->index('student_id', 'fee_payments_student_id_idx');
                }
                if (!$this->hasIndex('fee_payments', 'fee_payments_session_id_idx')) {
                    $table->index('session_id', 'fee_payments_session_id_idx');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('sessions')) {
            Schema::table('sessions', function (Blueprint $table) {
                if ($this->hasIndex('sessions', 'sessions_session_name_unique')) {
                    $table->dropUnique('sessions_session_name_unique');
                }
            });
        }
    }
};
