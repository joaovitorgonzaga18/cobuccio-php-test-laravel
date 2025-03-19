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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->uuid('transaction_uuid')->unique();
            $table->double('amount');
            $table->binary('type')->comment = '0 = Money Transfer, 1 = Deposit';
            $table->bigInteger('user_sender', false, true)->nullable()->comment('Null if transaction is a deposit');
            $table->bigInteger('user_reciever', false, true);
            $table->index('user_sender');
            $table->index('user_reciever');
            $table->foreign('user_sender')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('user_reciever')->references('id')->on('users')->onDelete('cascade');
            $table->dateTime('date_time');
            $table->boolean('cancelled')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
