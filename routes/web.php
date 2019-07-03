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

        $url = ''; $title = '';
        if (substr($node->filter('a')->attr('href'), -15) == 'job-description') {
            $url = $node->filter('a')->attr('href'); // get detail link
            $title = $node->filter('a')->text(); // job title
        }
        return [
            'url' => $url, 'title' => $title,
        ];
    });

    $URLs = array_filter($data, function($e) {
        return $e['url'] != '';
    });

    // $URLs = [
    //     [
    //         'url' => 'https://resources.workable.com/night-auditor-job-description',
    //         'title' => 'Test',
    //     ]
    // ];

    $chunk_URLs = array_chunk($URLs, 10); //separate array by 10 chunk

    foreach ($chunk_URLs as $chunk) { // chunk to prevent memory exhausting

        foreach($chunk as $key => $url){

            $crawler1 = Goutte::request('GET', $url['url']);

            $new = $crawler1->filter('.article-content')->each(function  ($node1) use($url) {

                return array(
                    'title' => trim($url['title']),
                    'summary' => $node1->filter('ul')->first()->html(),
                    'responsibilities' => $node1->filter('ul')->eq(1)->html(),
                    'requirements' => $node1->filter('ul')->eq(2)->html(),
                );
            });

            $job = new \App\Models\JobPlaceholder;
            $job->title = $new[0]['title'];
            $job->summary = $new[0]['summary'];
            $job->responsibilities = $new[0]['responsibilities'];
            $job->requirements = $new[0]['requirements'];
            $job->save();
        }
    }

});
