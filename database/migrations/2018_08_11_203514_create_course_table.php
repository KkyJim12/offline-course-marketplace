<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCourseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('course', function (Blueprint $table) {
            $table->increments('course_id');
            $table->integer('user_id');
            $table->integer('category_id');
            $table->string('course_img');
            $table->string('course_name');
            $table->date('course_start_date');
            $table->date('course_end_date');
            $table->date('course_expire_date');
            $table->float('course_price');
            $table->longText('course_detail');
            $table->string('course_teacher_name');
            $table->string('course_teacher_school');
            $table->string('course_teacher_college');
            $table->string('course_teacher_awards');
            $table->string('course_teacher_skill');
            $table->integer('course_rank')->nullable();
            $table->string('course_comment')->nullable();
            $table->integer('course_phone');
            $table->string('email');
            $table->string('course_website');
            $table->string('course_line')->nullable();
            $table->string('course_facebook')->nullable();
            $table->integer('course_verify')->nullable();
            $table->integer('course_other_img_id')->nullable();
            $table->integer('course_approve')->nullable();
            $table->integer('course_ban')->nullable();
            $table->integer('course_suggest')->nullable();
            $table->integer('course_sell');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('course');
    }
}
