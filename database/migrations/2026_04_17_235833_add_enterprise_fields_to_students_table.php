<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            // Bio Info
            $table->string('birth_city')->nullable();
            $table->string('caste')->nullable();
            $table->string('identification_mark')->nullable();
            $table->string('bio_id')->nullable();
            
            // Family Extended
            $table->string('whatsapp_no')->nullable();
            $table->string('father_qualification')->nullable();
            $table->string('mother_phone')->nullable();
            $table->string('mother_qualification')->nullable();
            
            // Previous Education
            $table->string('prev_degree')->nullable();
            $table->string('prev_board')->nullable();
            $table->string('prev_roll_no')->nullable();
            $table->string('prev_total_marks')->nullable();
            $table->string('prev_obtained_marks')->nullable();
            $table->string('prev_grade')->nullable();

            // Board Info (Current)
            $table->string('board_reg_no')->nullable();
            $table->string('board_roll_no')->nullable();
            $table->string('board_total_marks')->nullable();
            $table->string('board_obtained_marks')->nullable();

            // Office / Logistics
            $table->string('house')->nullable();
            $table->string('class_of_admission')->nullable();
            $table->text('attachments')->nullable();

            // SMS Toggles
            $table->boolean('send_branded_sms')->default(true);
            $table->boolean('send_whatsapp_sms')->default(false);
            $table->boolean('send_app_notification')->default(true);
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn([
                'birth_city', 'caste', 'identification_mark', 'bio_id',
                'whatsapp_no', 'father_qualification', 'mother_phone', 'mother_qualification',
                'prev_degree', 'prev_board', 'prev_roll_no', 'prev_total_marks', 'prev_obtained_marks', 'prev_grade',
                'board_reg_no', 'board_roll_no', 'board_total_marks', 'board_obtained_marks',
                'house', 'class_of_admission', 'attachments',
                'send_branded_sms', 'send_whatsapp_sms', 'send_app_notification'
            ]);
        });
    }
};
