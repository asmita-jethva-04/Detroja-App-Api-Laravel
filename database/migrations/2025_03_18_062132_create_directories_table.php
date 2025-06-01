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
        Schema::create('directories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('relations');
            $table->string('age');
            $table->string('surname');
            $table->string('qualification');
            $table->string('business');
            $table->string('marital_status');
            $table->string('home_country');
            $table->string('village');
            $table->string('status')->default('0')->comment('0 = updated, 1 = deleted');
            $table->string('is_delete')->default(0)->comment('0=not_delete | 1=soft_delete');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('directories');
    }
};
