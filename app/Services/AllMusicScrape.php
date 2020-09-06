<?php

namespace App\Services;

use Carbon\Carbon;
use DateTime;
use KubAT\PhpSimple\HtmlDomParser;
use App\Models\AllMusicContent;

class AllMusicScrape
{
    /**
     * @var AllMusicContent
     */
    private $allMusicContent;

    /**
     * AllMusicScrape constructor.
     * @param AllMusicContent $allMusicContent
     */
    public function __construct(AllMusicContent $allMusicContent){
        $this->allMusicContent = $allMusicContent;
    }

    public function scrapeArtistPage($artistUrl){
        $arrayArtistParams = [];
        sleep(rand(0, 5));

        /* Go to Artist Page */
        $objCurl = curl_init();
        curl_setopt_array($objCurl, [
            CURLOPT_URL => $artistUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => array(
                "user-agent: Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)",
            ),
        ]);

        $strArtistCurl = curl_exec($objCurl);
        curl_close($objCurl);

        $content = $this->allMusicContent->where("type", "artist")->where("href", $artistUrl)->first();
        if ( !isset($content) ) {
            $this->allMusicContent->create([
                "href" => $artistUrl,
                "content" => $strArtistCurl,
                "type" => "artist",
                "stamp_date" => Carbon::now()
            ]);
        }

        $artistHtml = HtmlDomParser::str_get_html($strArtistCurl);

        /* Get Data for Artists Table */
        $arrayArtistParams["artists"]["url_allmusic"] = $artistUrl;
        $arrayArtistParams["artists"]["artist_active"] = $artistHtml->find("div.active-dates>div", 0)->plaintext;
        $arrayArtistParams["artists"]["artist_name"] = trim($artistHtml->find("h1.artist-name", 0)->plaintext);
        $bornYear = $bornPlace = "";
        if ($artistHtml->find(".birth>div>a", 0)) {
            $bornYear = $artistHtml->find(".birth>div>a", 0)->plaintext;
        }
        if ($artistHtml->find(".birth>div>a", 1)) {
            $bornPlace = " in " . $artistHtml->find(".birth>div>a", 1)->plaintext;
        }

        $arrayArtistParams["artists"]["artist_born"] = $bornYear . $bornPlace;

        /* Get Data for Artists Aliases Table */
        foreach ($artistHtml->find(".aliases>div>div") as $key => $alias) {
            $arrayArtistParams["artist_aliases"][$key] = trim($alias->plaintext);
        }

        /* Get Data for Artists Genres Table */
        foreach ($artistHtml->find(".genre>div>a") as $key => $genre) {
            $arrayArtistParams["artist_genres"][$key] = $genre->plaintext;
        }

        /* Get Data for Artists Styles Table */
        foreach($artistHtml->find(".styles>div>a") as $key => $style){
            $arrayArtistParams["artist_styles"][$key] = $style->plaintext;
        }

        /* Get Data for Artists Members Table */
        foreach($artistHtml->find(".group-members>div>a") as $key => $member){
            $arrayArtistParams["artist_members"][$key]["artist_member"] = trim($member->find("span", 0)->plaintext);
            $arrayArtistParams["artist_members"][$key]["url_allmusic"] = "https://www.allmusic.com" . $member->getAttribute("href");
        }

        /* Get Data for Artists Moods Table */
        foreach ($artistHtml->find("section.moods>ul>li") as $key => $mood) {
            $arrayArtistParams["artist_moods"][$key] = $mood->find("a", 0)->plaintext;
        }

        /* Get Data for Artists Themes Table */
        foreach ($artistHtml->find("section.themes>ul>li") as $key => $theme) {
            $arrayArtistParams["artist_themes"][$key] = $theme->find("a", 0)->plaintext;
        }

        sleep(rand(0, 2));

        $objCurl = curl_init();
        curl_setopt_array($objCurl, [
            CURLOPT_URL => $artistUrl . "/related",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => array(
                "user-agent: Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)",
            ),
        ]);

        $strArtistRelatedCurl = curl_exec($objCurl);
        curl_close($objCurl);

        $artistRelatedHtml = HtmlDomParser::str_get_html($strArtistRelatedCurl);

        /* Get Data for Artists Similar Table */
        foreach ($artistRelatedHtml->find("section.similars>ul>li") as $key => $similar) {
            $arrayArtistParams["artist_similar"][$key]["artist_similar"] = $similar->find("a", 0)->plaintext;
            $arrayArtistParams["artist_similar"][$key]["url_allmusic"] = $similar->find("a", 0)->getAttribute("href");
            $arrayArtistParams["artist_similar"][$key]["flag"] = "similar";
        }

        /* Get Data for Artists Influenceds Table */
        foreach ($artistRelatedHtml->find("section.influencers>ul>li") as $key => $influence) {
            $arrayArtistParams["artist_influenced"][$key]["artist_influence"] = $influence->find("a", 0)->plaintext;
            $arrayArtistParams["artist_influenced"][$key]["url_allmusic"] = $influence->find("a", 0)->getAttribute("href");
            $arrayArtistParams["artist_influenced"][$key]["flag"] = "influenced";
        }

        return ($arrayArtistParams);
    }

    public function scrapeProjectPage($projectUrl, $artistUrl){
        $arrayProjectParams = [];
        sleep(rand(0, 3));

        /* Go to Project Page */
        $objCurl = curl_init();
        curl_setopt_array($objCurl, [
            CURLOPT_URL => $projectUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => array(
                "user-agent: Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)",
            ),
        ]);

        $strProjectCurl = curl_exec($objCurl);
        curl_close($objCurl);

        $content = $this->allMusicContent->where("type", "project")->where("href", $projectUrl)->first();
        if ( !isset($content) ) {
            $this->allMusicContent->create([
                "href" => $projectUrl,
                "content" => $strProjectCurl,
                "type" => "project",
                "stamp_date" => Carbon::now()
            ]);
        }

        $projectHtml = HtmlDomParser::str_get_html($strProjectCurl);

        /* Get Project Data for Projects Table */
        $arrayProjectParams["projects"]["project_date"] = $projectHtml->find("section.basic-info>div.release-date>span", 0)->plaintext;
        $date = new DateTime($arrayProjectParams["projects"]["project_date"]);
        $arrayProjectParams["projects"]["project_year"] = $date->format("Y");
        $arrayProjectParams["projects"]["project_duration"] = $projectHtml->find("div.duration>span", 0)->plaintext;
        $arrayProjectParams["projects"]["url_allmusic"] = $projectUrl;
        $arrayProjectParams["projects"]["project_name"] = trim($projectHtml->find("h1.album-title", 0)->plaintext);

        /* Get Artist Discography */
        $objCurl = curl_init();
        curl_setopt_array($objCurl, [
            CURLOPT_URL => $artistUrl . "/discography/all",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => array(
                "user-agent: Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)",
            ),
        ]);

        $strArtistDiscCurl = curl_exec($objCurl);
        curl_close($objCurl);

        $artistDiscHtml = HtmlDomParser::str_get_html($strArtistDiscCurl);

        /* Get Project Type and Project Label */
        foreach ($artistDiscHtml->find("tbody>tr") as $row) {
            if ( $row->find("td.title>a", 0)->plaintext == $arrayProjectParams["projects"]["project_name"] ) {
                if ( trim($row->find("td.cover")->plaintext) == trim(strip_tags($row->find("td.cover"))) ) {
                    $arrayProjectParams["projects"]["project_type"] = trim($row->find("td.cover")->plaintext);
                } else {
                    $arrayProjectParams["projects"]["project_type"] = "Album";
                }
                $labels = [];
                foreach ($row->find("td.label>a") as $label) {
                    array_push($labels, $label->plaintext);
                }
                $arrayProjectParams["projects"]["project_label"] = implode("/", $labels);
            }
        }

        /* Get Project Data for Project Genre Table */
        foreach ($projectHtml->find("section.basic-info div.genre>div>a") as $key => $genre) {
            $arrayProjectParams["projects_genre"][$key] = $genre->plaintext;
        }

        /* Get Project Data for Project Styles Table */
        foreach ($projectHtml->find("section.basic-info div.styles>div>a") as $key => $style) {
            $arrayProjectParams["projects_styles"][$key] = $style->plaintext;
        }

        /* Get Project Data for Project Moods Table */
        foreach ($projectHtml->find("section.moods>div>span") as $key => $mood) {
            $arrayProjectParams["projects_moods"][$key] = $mood->find("a", 0)->plaintext;
        }

        /* Get Project Data for Project Themes Table */
        foreach ($projectHtml->find("section.themes>div>span") as $key => $theme) {
            $arrayProjectParams["projects_themes"][$key] = $theme->find("a", 0)->plaintext;
        }

        /* Get Project Data for Project Tracks Table */
        foreach ($projectHtml->find("section.track-listing>div.disc") as $discKey => $disk) {
            foreach ($disk->find("table>tbody>tr") as $track) {
                $trackNum = trim($track->find("td.tracknum", 0)->plaintext);
                $arrayProjectParams["projects_tracks"][$trackNum]["disc_number"] = $discKey;
                $arrayProjectParams["projects_tracks"][$trackNum]["track_name"] = $track->find("td.title-composer>div.title>a", 0)->plaintext;
                $arrayProjectParams["projects_tracks"][$trackNum]["url_allmusic"] = $track->find("td.title-composer>div.title>a", 0)->getAttribute("href");
                $arrayProjectParams["projects_tracks"][$trackNum]["track_duration"] = trim($track->find("td.time", 0)->plaintext);

                /* Get Info for Track Stream */
                foreach ($track->find("td.stream>a") as $streamKey => $stream) {
                    $arrayProjectParams["projects_tracks"][$trackNum]["stream"][$streamKey]["platform"] = $stream->getAttribute("class");
                    $arrayProjectParams["projects_tracks"][$trackNum]["stream"][$streamKey]["url"] = $stream->getAttribute("href");
                }

                /* Get Info for Track Performer */
                foreach ($track->find("td.performer div.primary>a") as $performerKey => $performer) {
                    $arrayProjectParams["projects_tracks"][$trackNum]["projects_tracks_performers"][$performerKey] = $performer->getAttribute("href");
                }

                /* Get Info for Track Features */
                foreach ($track->find("td.performer div.featuring a") as $featuringKey => $featuring) {
                    $arrayProjectParams["projects_tracks"][$trackNum]["projects_tracks_features"][$featuringKey] = $featuring->getAttribute("href");
                }

                /* Get Project Data for Projects Tracks Composers Table */
                foreach ($track->find("td.title-composer>div.composer>a") as $key => $composer) {
                    $arrayProjectParams["projects_tracks"][$trackNum]["projects_tracks_composers"][$key] = $composer->getAttribute("href");
                }
            }
        }

        return ($arrayProjectParams);
    }

    public function getArtistPage($artistUrl){
        $objCurl = curl_init();
        curl_setopt_array($objCurl, [
            CURLOPT_URL => $artistUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => array(
                "user-agent: Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)",
            ),
        ]);

        $strArtistCurl = curl_exec($objCurl);

        return ($strArtistCurl);
    }

    public function getProjectPage($projectUrl){
        $objCurl = curl_init();
        curl_setopt_array($objCurl, [
            CURLOPT_URL => $projectUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => array(
                "user-agent: Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)",
            ),
        ]);

        $strProjectCurl = curl_exec($objCurl);

        return ($strProjectCurl);
    }
}
