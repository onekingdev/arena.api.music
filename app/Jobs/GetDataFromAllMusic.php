<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use App\Services\AllMusicScrape;
use App\Services\AllMusicInsert;
use KubAT\PhpSimple\HtmlDomParser;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Services\AllMusicUpdateTracksTables;

class GetDataFromAllMusic implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    public function __construct()
    {

    }

    /**
     * Execute the job.
     *
     * @param AllMusicScrape $allMusicScrape
     * @param AllMusicInsert $allMusicInsert
     * @param AllMusicUpdateTracksTables $allMusicUpdateTracksTables
     * @return void
     */
    public function handle(AllMusicScrape $allMusicScrape, AllMusicInsert $allMusicInsert,
                           AllMusicUpdateTracksTables $allMusicUpdateTracksTables)
    {
        $count = 1;

        while (true) {
            $objCurl = curl_init();
            curl_setopt_array($objCurl, [
                CURLOPT_URL => "https://www.allmusic.com/advanced-search/results/" . $count,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => "filters%5B%5D=releaseyear%3E%3A2017&sort=",
                CURLOPT_HTTPHEADER => array(
                    "user-agent: Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)",
                    "content-type: application/x-www-form-urlencoded;",
                    "referer: https://www.allmusic.com/advanced-search",
                ),
            ]);

            $strCurl = curl_exec($objCurl);
            curl_close($objCurl);

            $html = HtmlDomParser::str_get_html($strCurl);

            var_dump("Page - " . $count);
            if (!$html->find("table")) {
                break;
            }

            $arrayProjectParams = [];

            foreach ($html->find("tbody>tr") as $tr){
                if (count($tr->find(".artist>a")) > 1) {
                    $name = "";
                    foreach ($tr->find(".artist>a") as $artist) {
                        $name .= str_replace(" ", "", $artist->plaintext);
                    }

                    $path = "files/" . $name;

                    foreach ($tr->find(".artist>a") as $artistKey => $artist) {
                        Storage::put($path . "/artist" . $artistKey . ".html", $allMusicScrape->getArtistPage($artist->getAttribute("href")));
                    }
                    Storage::put($path . "/project.html", $allMusicScrape->getProjectPage($tr->find(".title>a", 0)->getAttribute("href")));
                } else {
                    /* Get Artist and Project Urls */
                    $projectUrl = $tr->find(".title>a", 0)->getAttribute("href");
                    $artistUrl = $tr->find(".artist>a", 0)->getAttribute("href");

                    /* Scrape Artist Page */
                    $arrayArtistParams = $allMusicScrape->scrapeArtistPage($artistUrl);

                    /* Scrape Project Page */
                    $arrayProjectParams = $allMusicScrape->scrapeProjectPage($projectUrl, $artistUrl);

                    /* Insert Artist Data */
                    $artistId = $allMusicInsert->insertArtistToDb($arrayArtistParams);

                    /* Insert Project Data */
                    $projectId = $allMusicInsert->insertProjectToDb($arrayProjectParams, $artistId);
                }
            }

            $count++;
        }

        $allMusicUpdateTracksTables->setupComposers();
        $allMusicUpdateTracksTables->setupFeatures();
        $allMusicUpdateTracksTables->setupPerformers();
    }
}
