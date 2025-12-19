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
        Schema::create('admin_logs', function (Blueprint $table) {
            $table->id('adminlog_id'); // primary key for logs
            $table->timestamp('admin_timein')->useCurrent();
            $table->timestamp('admin_timeout')->nullable();
            $table->unsignedBigInteger('admin_id');
            $table->timestamps();

            $table->foreign('admin_id')
              ->references('admin_id')
              ->on('admin')
              ->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     */
   
};
