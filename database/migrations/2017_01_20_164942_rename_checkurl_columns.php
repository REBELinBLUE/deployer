<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use REBELinBLUE\Deployer\CheckUrl;

class RenameCheckurlColumns extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // Has to be multiple Closures otherwise it doesn't work with sqlite
        Schema::table('check_urls', function (Blueprint $table) {
            $table->renameColumn('title', 'name');
        });

        Schema::table('check_urls', function (Blueprint $table) {
            $table->unsignedInteger('status')->nullable();
        });

        CheckUrl::withTrashed()->chunk(100, function (Collection $urls) {
            foreach ($urls as $url) {
                if (is_null($url->status)) {
                    $url->status = CheckUrl::UNTESTED;
                    $url->last_seen = $url->updated_at;
                } elseif ($url->status === CheckUrl::UNTESTED) {
                    $url->status = CheckUrl::OFFLINE;
                }

                $url->save();
            }
        });

        Schema::table('check_urls', function (Blueprint $table) {
            $table->unsignedInteger('status')->nullable(false)->default(0)->change();
        });

        Schema::table('check_urls', function (Blueprint $table) {
            $table->dropColumn('last_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('check_urls', function (Blueprint $table) {
            $table->renameColumn('name', 'title');
        });

        Schema::table('check_urls', function (Blueprint $table) {
            $table->boolean('last_status')->nullable();
        });

        CheckUrl::withTrashed()->chunk(100, function (Collection $urls) {
            foreach ($urls as $url) {
                if ($url->status === CheckUrl::UNTESTED) {
                    $url->last_status = null;
                } elseif ($url->status === CheckUrl::OFFLINE) {
                    $url->last_status = 1;
                }

                $url->save();
            }
        });

        Schema::table('check_urls', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
}
