<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('notes', function (Blueprint $table) {
            $table->renameColumn('name', 'poster_name');
        });

        Schema::table('notes', function (Blueprint $table) {
            $table->string('poster_name')->nullable()->default('Anonymous')->change();
            $table->string('section')->nullable()->change();
            $table->boolean('is_censored')->default(false);
        });
    }

    public function down(): void
    {
        DB::table('notes')->whereNull('poster_name')->update(['poster_name' => 'Anonymous']);
        DB::table('notes')->whereNull('section')->update(['section' => '']);

        Schema::table('notes', function (Blueprint $table) {
            $table->dropColumn('is_censored');
            $table->renameColumn('poster_name', 'name');
        });

        Schema::table('notes', function (Blueprint $table) {
            $table->string('name')->nullable(false)->change();
            $table->string('section')->nullable(false)->change();
        });
    }
};
