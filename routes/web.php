<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function() {

    $crawler = Goutte::request('GET', 'https://www.jobsinyangon.com/app/job-search?send=1&reset_filtr=1');

    $data = $crawler->filter('.accordion-toggle')->each(function ($node) {

        $info = explode(',', ($node->filter('.seda-t')) ? trim($node->filter('.seda-t')->text()) : '');

        // $test = preg_replace("/[^a-zA-Z0-9\s]/", "", $info[1]);
        return array(
            'title' => $node->filter('.nazev-inzeratu-vypis')->text(),
            'category' => $node->filter('p span')->text(),
            'company' => $node->filter('.col-lg-3 a')->first()->text(),
            'location' => $node->filter('.col-lg-3 a')->last()->text(),
            'posted' => $node->filter('.seda-t span')->text(),
            'employment_type' => !empty($info) ? trim($info[1]) : '',
            'job_field' => !empty($info) ? $info[2] : '',
            'detail_link' => $node->filter('.plna-sirka-na-mobilu')->attr('href'),
        );
    });

    dd($data);
});
