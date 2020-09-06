<?php

namespace App\Services;

use App\Models\Projects\{
    TracksComposer,
    TracksFeature,
    TracksPerformer
};

class AllMusicUpdateTracksTables
{
    /**
     * @var AllMusicInsert
     */
    private $allMusicInsert;
    /**
     * @var TracksComposer
     */
    private $tracksComposer;
    /**
     * @var TracksFeature
     */
    private $tracksFeature;
    /**
     * @var TracksPerformer
     */
    private $tracksPerformer;

    /**
     * AllMusicUpdateTracksTables constructor.
     * @param AllMusicInsert $allMusicInsert
     * @param TracksComposer $tracksComposer
     * @param TracksFeature $tracksFeature
     * @param TracksPerformer $tracksPerformer
     */
    public function __construct(AllMusicInsert $allMusicInsert, TracksComposer $tracksComposer,
                                TracksFeature $tracksFeature, TracksPerformer $tracksPerformer){
        $this->allMusicInsert = $allMusicInsert;
        $this->tracksComposer = $tracksComposer;
        $this->tracksFeature = $tracksFeature;
        $this->tracksPerformer = $tracksPerformer;
    }

    public function setupComposers(){
        $objCollection = $this->tracksComposer->where("artist_id", 0)->get();

        foreach ($objCollection as $row) {
            $result = $this->allMusicInsert->getArtistByUrl($row["url_allmusic"]);

            if (isset($result)) {
                $this->tracksComposer->where("composer_id", $row["composer_id"])->update([
                    "artist_id" => $result->artist_id
                ]);
            }
        }

        return;
    }

    public function setupFeatures(){
        $objCollection = $this->tracksFeature->where("artist_id", 0)->get();

        foreach ($objCollection as $row) {
            $result = $this->allMusicInsert->getArtistByUrl($row["url_allmusic"]);

            if (isset($result)) {
                $this->tracksFeature->where("featuring_id", $row["featuring_id"])->update([
                    "artist_id" => $result->artist_id
                ]);
            }
        }

        return;
    }

    public function setupPerformers(){
        $objCollection = $this->tracksPerformer->where("artist_id", 0)->get();

        foreach ($objCollection as $row) {
            $result = $this->allMusicInsert->getArtistByUrl($row["url_allmusic"]);

            if (isset($result)) {
                $this->tracksPerformer->where("performer_id", $row["performer_id"])->update([
                    "artist_id" => $result->artist_id
                ]);
            }
        }

        return;
    }
}
