<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

use App\Template;

class TemplateTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('templates')->delete();

        Template::create([
            'name' => 'Laravel'
        ]);

        Template::create([
            'name' => 'Wordpress'
        ]);
    }
}
