<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Session;

class Add2026SessionsToSessionsTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('sessions')) {
            $sessions = ['2026-2030', '2026-30'];
            foreach ($sessions as $name) {
                if (!Session::where('session_name', $name)->exists()) {
                    Session::create(['session_name' => $name]);
                }
            }
        }
    }

    public function down()
    {
        if (Schema::hasTable('sessions')) {
            Session::whereIn('session_name', ['2026-2030', '2026-30'])->delete();
        }
    }
}
