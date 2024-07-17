<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->foreignUuid('project_id')
                ->nullable()
                ->constrained(
                    table: 'projects',
                    column: 'id',
                    indexName: 'task_project_id'
                )
                ->nullOnDelete()
            ;
        });
    }

    /**
     *
    $table->foreign('category_id')
    ->references('id')
    ->on('categories')
    ->onDelete('cascade')
     */

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->removeColumn('project_id');
        });
    }
};
