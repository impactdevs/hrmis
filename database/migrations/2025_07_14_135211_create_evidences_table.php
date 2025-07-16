<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEvidencesTable extends Migration
{
    public function up()
    {
        Schema::create('evidences', function (Blueprint $table) {
            $table->uuid('evidence_id')->primary();
            $table->uuid('whistleblower_id');
            $table->string('witness_name')->nullable();
            $table->string('document')->nullable();
            $table->string('email')->nullable();
            $table->timestamps();

            $table->foreign('whistleblower_id')->references('whistleblower_id')->on('whistleblowers')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('evidences');
    }
}
