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
       Schema::table('assets', function (Blueprint $table) {
        $table->integer('depreciation_years')->nullable()->after('purchase_price');
        $table->decimal('yearly_depreciation', 10, 2)->nullable()->after('depreciation_years');
    });
}

public function down()
{
    Schema::table('assets', function (Blueprint $table) {
        $table->dropColumn('depreciation_years');
        $table->dropColumn('yearly_depreciation');
    });
    }
};
