<?php
// File: database/migrations/2026_03_01_000000_create_radiant_cash_pickups_table.php
// Run: php artisan migrate

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('radiant_cash_pickups', function (Blueprint $table) {
            $table->id();
            $table->integer('sno')->nullable();
            $table->string('state_name')->nullable();
            $table->string('pickup_date')->nullable();          // stored as string d/m/Y or Y-m-d
            $table->date('pickup_date_parsed')->nullable();     // parsed date for filtering
            $table->string('region')->nullable();
            $table->string('location')->nullable();
            $table->string('customer_name')->nullable();
            $table->text('pickup_address')->nullable();
            $table->string('pickup_point_code')->nullable();
            $table->string('client_code')->nullable();
            $table->string('deposit_mode')->nullable();
            $table->string('frequency')->nullable();
            $table->decimal('cash_limit', 15, 2)->nullable();
            $table->string('hci_slip_no')->nullable();
            $table->decimal('pickup_amount', 15, 2)->nullable();
            $table->string('deposit_slip_no')->nullable();
            $table->string('seal_tag_no')->nullable();
            // Denomination columns
            $table->decimal('denom_2000', 15, 2)->default(0);
            $table->decimal('denom_1000', 15, 2)->default(0);
            $table->decimal('denom_500',  15, 2)->default(0);
            $table->decimal('denom_200',  15, 2)->default(0);
            $table->decimal('denom_100',  15, 2)->default(0);
            $table->decimal('denom_50',   15, 2)->default(0);
            $table->decimal('denom_20',   15, 2)->default(0);
            $table->decimal('denom_10',   15, 2)->default(0);
            $table->decimal('denom_5',    15, 2)->default(0);
            $table->decimal('coins',      15, 2)->default(0);
            $table->decimal('total',      15, 2)->nullable();
            $table->decimal('difference', 15, 2)->nullable();
            $table->string('remarks')->nullable();
            $table->string('ccv')->nullable();
            $table->string('point_id')->nullable();
            // Upload tracking
            $table->string('upload_batch_id')->nullable()->index();
            $table->string('uploaded_file_name')->nullable();
            $table->unsignedBigInteger('uploaded_by')->nullable();
            $table->timestamps();

            $table->index(['state_name']);
            $table->index(['region']);
            $table->index(['location']);
            $table->index(['pickup_date_parsed']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('radiant_cash_pickups');
    }
};