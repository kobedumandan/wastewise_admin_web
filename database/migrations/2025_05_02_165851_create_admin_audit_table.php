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
        Schema::create('admin_audit', function (Blueprint $table) {
            $table->id('adminaudit_id');
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->unsignedBigInteger('uf_id')->nullable();
            $table->unsignedBigInteger('id')->nullable();
            $table->string('action',7);
            $table->timestamps();

            $table->foreign('id')
            ->references('id')
            ->on('user')
            ->onDelete('cascade');

            $table->foreign('admin_id')
            ->references('admin_id')
            ->on('admin')
            ->onDelete('cascade');

            $table->foreign('uf_id')
            ->references('uf_id')
            ->on('user_fines')
            ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
  
};
