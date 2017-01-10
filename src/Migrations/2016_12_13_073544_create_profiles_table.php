<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->string('country_id')->nullable();
            $table->boolean('is_available_for_hire')->default(false);
            $table->string('website')->nullable();
            $table->string('twitter_username')->nullable();
            $table->string('github_username')->nullable();
            $table->string('place_of_employment')->nullable();
            $table->string('job_title')->nullable();
            $table->string('hometown')->nullable();
            $table->string('avatar')->nullable();
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
        Schema::dropIfExists('profiles');
    }
}
