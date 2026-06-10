<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('system_controls')) {
            return;
        }

        Schema::table('system_controls', function (Blueprint $table) {
            if (! Schema::hasColumn('system_controls', 'control_key')) {
                $table->string('control_key')->nullable()->after('control_name');
            }

            if (! Schema::hasColumn('system_controls', 'last_triggered_at')) {
                $table->dateTime('last_triggered_at')->nullable()->after('description');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('system_controls')) {
            return;
        }

        Schema::table('system_controls', function (Blueprint $table) {
            $columns = array_filter(
                ['last_triggered_at', 'control_key'],
                fn (string $column) => Schema::hasColumn('system_controls', $column),
            );

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};
