<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\Social;

class CreateSocialsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('socials', function (Blueprint $table) {
            $table->increments('id');
            $table->enum('provider', [Social::FACEBOOK, Social::GOOGLE, Social::TWITTER]);
            $table->string('provider_id');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('cascade');
            $table->string('user_name')->nullable();
            $table->string('user_email')->nullable();;
            $table->string('user_avatar')->nullable();
            $table->string('user_avatar_original')->nullable();
            $table->string('user_gender')->nullable();
            $table->timestamps();

            /**
             * add unique on both columns which represent the state that user can have multiple social accounts, but
             * for example if user have linked his Facebook account, he cant link another facebook account
             */
            $table->unique(['provider', 'user_id', 'provider_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('socials');
    }
}
