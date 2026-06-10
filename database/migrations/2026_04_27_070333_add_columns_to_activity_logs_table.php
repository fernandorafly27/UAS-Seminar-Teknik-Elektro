<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('activity_logs')) {
            return;
        }

        Schema::table('activity_logs', function (Blueprint $table) {
            if (! Schema::hasColumn('activity_logs', 'type')) {
                $table->string('type')->after('id');
            }

            if (! Schema::hasColumn('activity_logs', 'message')) {
                $table->string('message')->after('type');
            }

            if (! Schema::hasColumn('activity_logs', 'logged_at') && ! Schema::hasColumn('activity_logs', 'occurred_at')) {
                $table->timestamp('logged_at')->after('message');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('activity_logs')) {
            return;
        }

        Schema::table('activity_logs', function (Blueprint $table) {
            $columns = array_filter(['type', 'message', 'logged_at'], fn (string $column) => Schema::hasColumn('activity_logs', $column));

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};
