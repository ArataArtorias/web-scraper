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

Route::get('/data', function() {

    $crawler = Goutte::request('GET', 'https://resources.workable.com/job-descriptions/');

    $data = $crawler->filter('li')->each(function ($node) {

        $url = '';
        if (substr($node->filter('a')->attr('href'), -15) == 'job-description') {
            $url = $node->filter('a')->attr('href');
        }
        return array(
            'url' => $url,
        );
    });

    // $URLs = array_filter($data, function($e) {
    //     return $e['url'] != '';
    // });

    // dd($URLs);

    $URLs = [
        // 'url' => 'https://resources.workable.com/staff-accountant-job-description',
        'url' => 'https://resources.workable.com/night-auditor-job-description',
    ];

    foreach($URLs as $u){

        $crawler1 = Goutte::request('GET', $u);

        $new = $crawler1->filter('.article-content')->each(function  ($node1)  {

            return array(
                'test 1' => $node1->filterXPath('//ul/li')->text(),
            );
        });
    }//end of foreach

    dd($new);
});
