<?php

namespace App\Services;

use DateTime;
use Exception;
use App\Models\Artists\{
    Artist,
    Alias as ArtistAlias,
    Genre as ArtistGenre,
    Influenced as ArtistInfluenced,
    Members as ArtistMembers,
    Mood as ArtistMood,
    Related as ArtistRelated,
    Similar as ArtistSimilar,
    Style as ArtistStyle,
    Theme as ArtistTheme
};
use App\Models\Projects\{
    Project,
    Genre as ProjectGenre,
    Mood as ProjectMood,
    Style as ProjectStyle,
    Theme as ProjectTheme,
    Track as ProjectTrack,
    TracksComposer as ProjectTracksComposer,
    TracksFeature as ProjectTracksFeature,
    TracksPerformer as ProjectTracksPerformer
};

class AllMusicInsert
{
    /**
     * @var Artist
     */
    private $artist;
    /**
     * @var ArtistAlias
     */
    private $artistAlias;
    /**
     * @var ArtistGenre
     */
    private $artistGenre;
    /**
     * @var ArtistInfluenced
     */
    private $artistInfluenced;
    /**
     * @var ArtistMembers
     */
    private $artistMembers;
    /**
     * @var ArtistMood
     */
    private $artistMood;
    /**
     * @var ArtistRelated
     */
    private $artistRelated;
    /**
     * @var ArtistSimilar
     */
    private $artistSimilar;
    /**
     * @var ArtistStyle
     */
    private $artistStyle;
    /**
     * @var ArtistTheme
     */
    private $artistTheme;
    /**
     * @var Project
     */
    private $project;
    /**
     * @var ProjectGenre
     */
    private $projectGenre;
    /**
     * @var ProjectMood
     */
    private $projectMood;
    /**
     * @var ProjectStyle
     */
    private $projectStyle;
    /**
     * @var ProjectTheme
     */
    private $projectTheme;
    /**
     * @var ProjectTrack
     */
    private $projectTrack;
    /**
     * @var ProjectTracksComposer
     */
    private $tracksComposer;
    /**
     * @var ProjectTracksFeature
     */
    private $tracksFeature;
    /**
     * @var ProjectTracksPerformer
     */
    private $tracksPerformer;
    /**
     * @var \App\Services\AllMusicScrape
     */
    private $allMusicScrape;

    /**
     * AllMusic constructor.
     * @param Artist $artist
     * @param ArtistAlias $artistAlias
     * @param ArtistGenre $artistGenre
     * @param ArtistInfluenced $artistInfluenced
     * @param ArtistMembers $artistMembers
     * @param ArtistMood $artistMood
     * @param ArtistRelated $artistRelated
     * @param ArtistSimilar $artistSimilar
     * @param ArtistStyle $artistStyle
     * @param ArtistTheme $artistTheme
     * @param Project $project
     * @param ProjectGenre $projectGenre
     * @param ProjectMood $projectMood
     * @param ProjectStyle $projectStyle
     * @param ProjectTheme $projectTheme
     * @param ProjectTrack $projectTrack
     * @param ProjectTracksComposer $tracksComposer
     * @param ProjectTracksFeature $tracksFeature
     * @param ProjectTracksPerformer $tracksPerformer
     * @param \App\Services\AllMusicScrape $allMusicScrape
     */
    public function __construct(Artist $artist, ArtistAlias $artistAlias, ArtistGenre $artistGenre,
                                ArtistInfluenced $artistInfluenced, ArtistMembers $artistMembers, ArtistMood $artistMood,
                                ArtistRelated $artistRelated, ArtistSimilar $artistSimilar, ArtistStyle $artistStyle,
                                ArtistTheme $artistTheme, Project $project, ProjectGenre $projectGenre,
                                ProjectMood $projectMood, ProjectStyle $projectStyle, ProjectTheme $projectTheme,
                                ProjectTrack $projectTrack, ProjectTracksComposer $tracksComposer,
                                ProjectTracksFeature $tracksFeature, ProjectTracksPerformer $tracksPerformer, 
                                AllMusicScrape $allMusicScrape){

        $this->artist = $artist;
        $this->artistAlias = $artistAlias;
        $this->artistGenre = $artistGenre;
        $this->artistInfluenced = $artistInfluenced;
        $this->artistMembers = $artistMembers;
        $this->artistMood = $artistMood;
        $this->artistRelated = $artistRelated;
        $this->artistSimilar = $artistSimilar;
        $this->artistStyle = $artistStyle;
        $this->artistTheme = $artistTheme;
        $this->project = $project;
        $this->projectGenre = $projectGenre;
        $this->projectMood = $projectMood;
        $this->projectStyle = $projectStyle;
        $this->projectTheme = $projectTheme;
        $this->projectTrack = $projectTrack;
        $this->tracksComposer = $tracksComposer;
        $this->tracksFeature = $tracksFeature;
        $this->tracksPerformer = $tracksPerformer;
        $this->allMusicScrape = $allMusicScrape;
    }

    public function insertArtistToDb($arrayParams){
        /* Check if Artist Already Isset in Table */
        $result = $this->getArtistByUrl($arrayParams["artists"]["url_allmusic"]);

        if (isset($result)) {
            $artistId = $result->artist_id;
        } else {
            /* Insert Data Into Artists Table */
            try {
                $objArtist = $this->artist->create([
                    "arena_id" => "",
                    "artist_name" => $arrayParams["artists"]["artist_name"],
                    "artist_active" => $arrayParams["artists"]["artist_active"],
                    "artist_born" => $arrayParams["artists"]["artist_born"],
                    "stamp_epoch" => time(),
                    "stamp_date" => date("Y-m-d"),
                    "stamp_time" => date("G:i:s"),
                    "url_allmusic" => $arrayParams["artists"]["url_allmusic"],
                    "url_amazon" => "",
                    "url_itunes" => "",
                    "url_lastfm" => "",
                    "url_spotify" => "",
                    "url_wikipedia" => "",
                    "flag_allmusic" => "Y",
                    "flag_amazon" => "N",
                    "flag_itunes" => "N",
                    "flag_lastfm" => "N",
                    "flag_spotify" => "N",
                    "flag_wikipedia" => "N",
                ]);

                $artistId = $objArtist->artist_id;
            } catch (Exception $e) {
                print "Error!: " . $e->getMessage() . "<br/>";
                die();
            }
        }

        /* Insert Data Into Artists Aliases Table */
        try {
            if ( !empty($arrayParams["artist_aliases"]) && $this->artistAlias->where("artist_id", $artistId)->get()->isEmpty() ) {
                foreach ($arrayParams["artist_aliases"] as $alias) {
                    $this->artistAlias->create([
                        "artist_id" => $artistId,
                        "artist_alias" => $alias,
                        "stamp_epoch" => time(),
                        "stamp_date" => date("Y-m-d"),
                        "stamp_time" => date("G:i:s"),
                        "stamp_source" => "ALLMUSIC"
                    ]);
                }
            }
        } catch (Exception $e) {
            print "Error!: " . $e->getMessage() . "<br/>";
        }

        /* Insert Data Into Artists Genres Table */
        try {
            if ( !empty($arrayParams["artist_genres"]) && $this->artistGenre->where("artist_id", $artistId)->get()->isEmpty() ) {
                foreach ($arrayParams["artist_genres"] as $genre) {
                    $this->artistGenre->create([
                        "artist_id" => $artistId,
                        "artist_genre" => $genre,
                        "stamp_epoch" => time(),
                        "stamp_date" => date("Y-m-d"),
                        "stamp_time" => date("G:i:s"),
                        "stamp_source" => "ALLMUSIC"
                    ]);
                }
            }
        } catch (Exception $e) {
            print "Error!: " . $e->getMessage() . "<br/>";
        }

        /* Insert Data Into Artists Influenced Table */
        try {
            if ( !empty($arrayParams["artist_influenced"]) && $this->artistInfluenced->where("artist_id", $artistId)->get()->isEmpty() ) {
                foreach ($arrayParams["artist_influenced"] as $influence) {
                    $this->artistInfluenced->create([
                        "artist_id" => $artistId,
                        "artist_influence" => $influence["artist_influence"],
                        "stamp_epoch" => time(),
                        "stamp_date" => date("Y-m-d"),
                        "stamp_time" => date("G:i:s"),
                        "stamp_source" => "ALLMUSIC",
                        "url_allmusic" => $influence["url_allmusic"]
                    ]);
                }
            }
        } catch (Exception $e) {
            print "Error!: " . $e->getMessage() . "<br/>";
        }

        /* Insert Data Into Artists Members Table */
        try {
            if ( !empty($arrayParams["artist_members"]) && $this->artistMembers->where("artist_id", $artistId)->get()->isEmpty() ) {
                foreach ($arrayParams["artist_members"] as $member) {
                    $this->artistMembers->create([
                        "artist_id" => $artistId,
                        "artist_member" => $member["artist_member"],
                        "stamp_epoch" => time(),
                        "stamp_date" => date("Y-m-d"),
                        "stamp_time" => date("G:i:s"),
                        "stamp_source" => "ALLMUSIC",
                        "url_allmusic" => $member["url_allmusic"]
                    ]);
                }
            }
        } catch (Exception $e) {
            print "Error!: " . $e->getMessage() . "<br/>";
        }

        /* Insert Data Into Artists Moods Table */
        try {
            if ( !empty($arrayParams["artist_moods"]) && $this->artistMood->where("artist_id", $artistId)->get()->isEmpty() ) {
                foreach ($arrayParams["artist_moods"] as $mood) {
                    $this->artistMood->create([
                        "artist_id" => $artistId,
                        "artist_mood" => $mood,
                        "stamp_epoch" => time(),
                        "stamp_date" => date("Y-m-d"),
                        "stamp_time" => date("G:i:s"),
                        "stamp_source" => "ALLMUSIC"
                    ]);
                }
            }
        } catch (Exception $e) {
            print "Error!: " . $e->getMessage() . "<br/>";
        }

        /* Insert Data Into Artists Related Table */
        try {
            if ( $this->artistRelated->where("artist_id", $artistId)->get()->isEmpty() ) {
                $arrRelated = array_merge((array)$arrayParams["artist_similar"], (array)$arrayParams["artist_influenced"]);
                foreach ($arrRelated as $related) {
                    $result = $this->getArtistByUrl($related["url_allmusic"]);
                    if (!isset($result)) {
                        $arrayRelatedArtistParams = $this->allMusicScrape->scrapeArtistPage($related["url_allmusic"]);
                        $relatedId = $this->insertArtistWithoutRelated($arrayRelatedArtistParams);
                    } else {
                        $relatedId = $result->artist_id;
                    }
                    $this->artistRelated->create([
                        "artist_id" => $artistId,
                        "related_id" => $relatedId,
                        "stamp_epoch" => time(),
                        "stamp_date" => date("Y-m-d"),
                        "stamp_time" => date("G:i:s"),
                        "stamp_source" => "ALLMUSIC",
                        "flag_influence" => $related["flag"] == "influenced" ? "Y" : "N",
                        "flag_similarity" => $related["flag"] == "similar" ? "Y" : "N"
                    ]);
                }
            }
        } catch (Exception $e) {
            print "Error!: " . $e->getMessage() . "<br/>";
        }

        /* Insert Data Into Artists Similar Table */
        try {
            if ( !empty($arrayParams["artist_similar"]) && $this->artistSimilar->where("artist_id", $artistId)->get()->isEmpty() ) {
                foreach ($arrayParams["artist_similar"] as $similar) {
                    $this->artistSimilar->create([
                        "artist_id" => $artistId,
                        "artist_similar" => $similar["artist_similar"],
                        "stamp_epoch" => time(),
                        "stamp_date" => date("Y-m-d"),
                        "stamp_time" => date("G:i:s"),
                        "stamp_source" => "ALLMUSIC",
                        "url_allmusic" => $similar["url_allmusic"]
                    ]);
                }
            }
        } catch (Exception $e) {
            print "Error!: " . $e->getMessage() . "<br/>";
        }

        /* Insert Data Into Artists Styles Table */
        try {
            if ( !empty($arrayParams["artist_styles"]) && $this->artistStyle->where("artist_id", $artistId)->get()->isEmpty() ) {
                foreach ($arrayParams["artist_styles"] as $style) {
                    $this->artistStyle->create([
                        "artist_id" => $artistId,
                        "artist_style" => $style,
                        "stamp_epoch" => time(),
                        "stamp_date" => date("Y-m-d"),
                        "stamp_time" => date("G:i:s"),
                        "stamp_source" => "ALLMUSIC",
                        "flag_status" => ""
                    ]);
                }
            }
        } catch (Exception $e) {
            print "Error!: " . $e->getMessage() . "<br/>";
        }

        /* Insert Data Into Artists Themes Table */
        try {
            if ( !empty($arrayParams["artist_themes"]) && $this->artistTheme->where("artist_id", $artistId)->get()->isEmpty() ) {
                foreach ($arrayParams["artist_themes"] as $theme) {
                    $this->artistTheme->create([
                        "artist_id" => $artistId,
                        "artist_theme" => $theme,
                        "stamp_epoch" => time(),
                        "stamp_date" => date("Y-m-d"),
                        "stamp_time" => date("G:i:s"),
                        "stamp_source" => "ALLMUSIC"
                    ]);
                }
            }
        } catch (Exception $e) {
            print "Error!: " . $e->getMessage() . "<br/>";
        }

        return ($artistId);
    }

    public function insertProjectToDb($arrayParams, $artistId){
        /* Check if PROJECT Already Isset in Table */
        $result = $this->getProjectByUrl($arrayParams["projects"]["url_allmusic"]);

        if (isset($result)) {
            $projectId = $result->project_id;
        } else {
            /* Insert Data Into Projects Table */
            try {
                $date = new DateTime($arrayParams["projects"]["project_date"]);
                $projectDate = $date->format("Y-m-d");

                $objProject = $this->project->create([
                    "artist_id" => $artistId,
                    "project_type" => $arrayParams["projects"]["project_type"],
                    "project_date" => $projectDate,
                    "project_year" => $arrayParams["projects"]["project_year"],
                    "project_name" => $arrayParams["projects"]["project_name"],
                    "project_label" => $arrayParams["projects"]["project_label"],
                    "project_duration" => $arrayParams["projects"]["project_duration"],
                    "stamp_epoch" => time(),
                    "stamp_date" => date("Y-m-d"),
                    "stamp_time" => date("G:i:s"),
                    "stamp_source" => "ALLMUSIC",
                    "url_allmusic" => $arrayParams["projects"]["url_allmusic"],
                    "url_amazon" => "",
                    "url_itunes" => "",
                    "url_spotify" => "",
                    "flag_allmusic" => "Y"
                ]);

                $projectId = $objProject->project_id;
            } catch (Exception $e) {
                print "Error!: " . $e->getMessage() . "<br/>";
            }
        }

        /* Insert Data Into Projects Genres Table */
        try {
            if ( !empty($arrayParams["projects_genre"]) && $this->projectGenre->where("project_id", $projectId)->get()->isEmpty() ) {
                foreach ($arrayParams["projects_genre"] as $genre) {
                    $this->projectGenre->create([
                        "project_id" => $projectId,
                        "project_genre" => $genre,
                        "stamp_epoch" => time(),
                        "stamp_date" => date("Y-m-d"),
                        "stamp_time" => date("G:i:s"),
                        "stamp_source" => "ALLMUSIC"
                    ]);
                }
            }
        } catch (Exception $e) {
            print "Error!: " . $e->getMessage() . "<br/>";
        }

        /* Insert Data Into Projects Moods Table */
        try {
            if ( !empty($arrayParams["projects_moods"]) && $this->projectMood->where("project_id", $projectId)->get()->isEmpty() ) {
                foreach ($arrayParams["projects_moods"] as $mood) {
                    $this->projectMood->create([
                        "project_id" => $projectId,
                        "project_mood" => $mood,
                        "stamp_epoch" => time(),
                        "stamp_date" => date("Y-m-d"),
                        "stamp_time" => date("G:i:s"),
                        "stamp_source" => "ALLMUSIC"
                    ]);
                }
            }
        } catch (Exception $e) {
            print "Error!: " . $e->getMessage() . "<br/>";
        }

        /* Insert Data Into Projects Styles Table */
        try {
            if ( !empty($arrayParams["projects_styles"]) && $this->projectStyle->where("project_id", $projectId)->get()->isEmpty() ) {
                foreach ($arrayParams["projects_styles"] as $style) {
                    $this->projectStyle->create([
                        "project_id" => $projectId,
                        "project_style" => $style,
                        "stamp_epoch" => time(),
                        "stamp_date" => date("Y-m-d"),
                        "stamp_time" => date("G:i:s"),
                        "stamp_source" => "ALLMUSIC"
                    ]);
                }
            }
        } catch (Exception $e) {
            print "Error!: " . $e->getMessage() . "<br/>";
        }

        /* Insert Data Into Projects Themes Table */
        try {
            if ( !empty($arrayParams["projects_themes"]) && $this->projectTheme->where("project_id", $projectId)->get()->isEmpty() ) {
                foreach ($arrayParams["projects_themes"] as $theme) {
                    $this->projectTheme->create([
                        "project_id" => $projectId,
                        "project_theme" => $theme,
                        "stamp_epoch" => time(),
                        "stamp_date" => date("Y-m-d"),
                        "stamp_time" => date("G:i:s"),
                        "stamp_source" => "ALLMUSIC"
                    ]);
                }
            }
        } catch (Exception $e) {
            print "Error!: " . $e->getMessage() . "<br/>";
        }

        /* Insert Data Into Projects Tracks Table */
        try {
            if ( $this->projectTrack->where("project_id", $projectId)->get()->isEmpty() ) {
                foreach ($arrayParams["projects_tracks"] as $trackNum => $trackValue) {
                    foreach ($arrayParams["projects_tracks"][$trackNum]["stream"] as $stream) {
                        $amazonUrl = $stream["platform"] == "amazon" ? $stream["url"] : "";
                        $spotifyUrl = $stream["platform"] == "spotify" ? $stream["url"] : "";
                    }

                    $objProjectTrack = $this->projectTrack->create([
                        "project_id" => $projectId,
                        "disc_number" => intval($trackValue["disc_number"]) + 1,
                        "track_number" => $trackNum,
                        "track_name" => $trackValue["track_name"],
                        "track_duration" => $trackValue["track_duration"],
                        "stamp_epoch" => time(),
                        "stamp_date" => date("Y-m-d"),
                        "stamp_time" => date("G:i:s"),
                        "stamp_source" => "ALLMUSIC",
                        "url_allmusic" => $trackValue["url_allmusic"],
                        "url_amazon" => $amazonUrl,
                        "url_spotify" => $spotifyUrl,
                        "flag_allmusic" => "Y"
                    ]);

                    $trackId = $objProjectTrack->track_id;

                    /* Insert Data Into Projects Tracks Composers Table */
                    if (isset($trackValue["projects_tracks_composers"])) {
                        foreach ($trackValue["projects_tracks_composers"] as $composer) {
                            $this->tracksComposer->create([
                                "project_id" => $projectId,
                                "track_id" => $trackId,
                                "admin_id" => 0,
                                "artist_id" => 0,
                                "stamp_epoch" => time(),
                                "stamp_date" => date("Y-m-d"),
                                "stamp_time" => date("G:i:s"),
                                "stamp_source" => "ALLMUSIC",
                                "url_allmusic" => $composer
                            ]);
                        }
                    }

                    /* Insert Data Into Projects Tracks Features Table */
                    if (isset($trackValue["projects_tracks_features"])) {
                        foreach ($trackValue["projects_tracks_features"] as $feature) {
                            $this->tracksFeature->create([
                                "project_id" => $projectId,
                                "track_id" => $trackId,
                                "admin_id" => 0,
                                "artist_id" => 0,
                                "stamp_epoch" => time(),
                                "stamp_date" => date("Y-m-d"),
                                "stamp_time" => date("G:i:s"),
                                "stamp_source" => "ALLMUSIC",
                                "url_allmusic" => $feature
                            ]);
                        }
                    }

                    /* Insert Data Into Projects Tracks Performers Table */
                    if (isset($trackValue["projects_tracks_performers"])) {
                        foreach ($trackValue["projects_tracks_performers"] as $performer) {
                            $this->tracksPerformer->create([
                                "project_id" => $projectId,
                                "track_id" => $trackId,
                                "admin_id" => 0,
                                "artist_id" => 0,
                                "stamp_epoch" => time(),
                                "stamp_date" => date("Y-m-d"),
                                "stamp_time" => date("G:i:s"),
                                "stamp_source" => "ALLMUSIC",
                                "url_allmusic" => $performer
                            ]);
                        }
                    }
                }
            }
        } catch (Exception $e) {
            print "Error!: " . $e->getMessage() . "<br/>";
        }

        return ($projectId);
    }

    protected function insertArtistWithoutRelated($arrayParams){
        /* Insert Data Into Artists Table */
        try {
            $objArtist = $this->artist->create([
                "arena_id" => "",
                "artist_name" => $arrayParams["artists"]["artist_name"],
                "artist_active" => $arrayParams["artists"]["artist_active"],
                "artist_born" => $arrayParams["artists"]["artist_born"],
                "stamp_epoch" => time(),
                "stamp_date" => date("Y-m-d"),
                "stamp_time" => date("G:i:s"),
                "url_allmusic" => $arrayParams["artists"]["url_allmusic"],
                "url_amazon" => "",
                "url_itunes" => "",
                "url_lastfm" => "",
                "url_spotify" => "",
                "url_wikipedia" => "",
                "flag_allmusic" => "Y",
                "flag_amazon" => "N",
                "flag_itunes" => "N",
                "flag_lastfm" => "N",
                "flag_spotify" => "N",
                "flag_wikipedia" => "N",
            ]);

            $artistId = $objArtist->artist_id;
        } catch (Exception $e) {
            print "Error!: " . $e->getMessage() . "<br/>";
            die;
        }

        /* Insert Data Into Artists Aliases Table */
        try {
            if ( !empty($arrayParams["artist_aliases"]) && $this->artistAlias->where("artist_id", $artistId)->get()->isEmpty() ) {
                foreach ($arrayParams["artist_aliases"] as $alias) {
                    $this->artistAlias->create([
                        "artist_id" => $artistId,
                        "artist_alias" => $alias,
                        "stamp_epoch" => time(),
                        "stamp_date" => date("Y-m-d"),
                        "stamp_time" => date("G:i:s"),
                        "stamp_source" => "ALLMUSIC"
                    ]);
                }
            }
        } catch (Exception $e) {
            print "Error!: " . $e->getMessage() . "<br/>";
        }

        /* Insert Data Into Artists Genres Table */
        try {
            if ( !empty($arrayParams["artist_genres"]) && $this->artistGenre->where("artist_id", $artistId)->get()->isEmpty() ) {
                foreach ($arrayParams["artist_genres"] as $genre) {
                    $this->artistGenre->create([
                        "artist_id" => $artistId,
                        "artist_genre" => $genre,
                        "stamp_epoch" => time(),
                        "stamp_date" => date("Y-m-d"),
                        "stamp_time" => date("G:i:s"),
                        "stamp_source" => "ALLMUSIC"
                    ]);
                }
            }
        } catch (Exception $e) {
            print "Error!: " . $e->getMessage() . "<br/>";
        }

        /* Insert Data Into Artists Influenced Table */
        try {
            if ( !empty($arrayParams["artist_influenced"]) && $this->artistInfluenced->where("artist_id", $artistId)->get()->isEmpty() ) {
                foreach ($arrayParams["artist_influenced"] as $influence) {
                    $this->artistInfluenced->create([
                        "artist_id" => $artistId,
                        "artist_influence" => $influence["artist_influence"],
                        "stamp_epoch" => time(),
                        "stamp_date" => date("Y-m-d"),
                        "stamp_time" => date("G:i:s"),
                        "stamp_source" => "ALLMUSIC",
                        "url_allmusic" => $influence["url_allmusic"]
                    ]);
                }
            }
        } catch (Exception $e) {
            print "Error!: " . $e->getMessage() . "<br/>";
        }

        /* Insert Data Into Artists Members Table */
        try {
            if (isset($arrayParams["artist_members"]) && $this->artistMembers->where("artist_id", $artistId)->get()->isEmpty() ) {
                foreach ($arrayParams["artist_members"] as $member) {
                    $this->artistMembers->create([
                        "artist_id" => $artistId,
                        "artist_member" => $member["artist_member"],
                        "stamp_epoch" => time(),
                        "stamp_date" => date("Y-m-d"),
                        "stamp_time" => date("G:i:s"),
                        "stamp_source" => "ALLMUSIC",
                        "url_allmusic" => $member["url_allmusic"]
                    ]);
                }
            }
        } catch (Exception $e) {
            print "Error!: " . $e->getMessage() . "<br/>";
        }

        /* Insert Data Into Artists Moods Table */
        try {
            if ( !empty($arrayParams["artist_moods"]) && $this->artistMood->where("artist_id", $artistId)->get()->isEmpty() ) {
                foreach ($arrayParams["artist_moods"] as $mood) {
                    $this->artistMood->create([
                        "artist_id" => $artistId,
                        "artist_mood" => $mood,
                        "stamp_epoch" => time(),
                        "stamp_date" => date("Y-m-d"),
                        "stamp_time" => date("G:i:s"),
                        "stamp_source" => "ALLMUSIC"
                    ]);
                }
            }
        } catch (Exception $e) {
            print "Error!: " . $e->getMessage() . "<br/>";
        }

        /* Insert Data Into Artists Similar Table */
        try {
            if ( !empty($arrayParams["artist_similar"]) && $this->artistSimilar->where("artist_id", $artistId)->get()->isEmpty() ) {
                foreach ($arrayParams["artist_similar"] as $similar) {
                    $this->artistSimilar->create([
                        "artist_id" => $artistId,
                        "artist_similar" => $similar["artist_similar"],
                        "stamp_epoch" => time(),
                        "stamp_date" => date("Y-m-d"),
                        "stamp_time" => date("G:i:s"),
                        "stamp_source" => "ALLMUSIC",
                        "url_allmusic" => $similar["url_allmusic"]
                    ]);
                }
            }
        } catch (Exception $e) {
            print "Error!: " . $e->getMessage() . "<br/>";
        }

        /* Insert Data Into Artists Styles Table */
        try {
            if ( !empty($arrayParams["artist_styles"]) && $this->artistStyle->where("artist_id", $artistId)->get()->isEmpty() ) {
                foreach ($arrayParams["artist_styles"] as $style) {
                    $this->artistStyle->create([
                        "artist_id" => $artistId,
                        "artist_style" => $style,
                        "stamp_epoch" => time(),
                        "stamp_date" => date("Y-m-d"),
                        "stamp_time" => date("G:i:s"),
                        "stamp_source" => "ALLMUSIC",
                        "flag_status" => ""
                    ]);
                }
            }
        } catch (Exception $e) {
            print "Error!: " . $e->getMessage() . "<br/>";
        }

        /* Insert Data Into Artists Themes Table */
        try {
            if ( !empty($arrayParams["artist_themes"]) && $this->artistTheme->where("artist_id", $artistId)->get()->isEmpty() ) {
                foreach ($arrayParams["artist_themes"] as $theme) {
                    $this->artistTheme->create([
                        "artist_id" => $artistId,
                        "artist_theme" => $theme,
                        "stamp_epoch" => time(),
                        "stamp_date" => date("Y-m-d"),
                        "stamp_time" => date("G:i:s"),
                        "stamp_source" => "ALLMUSIC"
                    ]);
                }
            }
        } catch (Exception $e) {
            print "Error!: " . $e->getMessage() . "<br/>";
        }

        return ($artistId);
    }

    public function getArtistByUrl($strArtistUrl){
        return ($this->artist->where("url_allmusic", $strArtistUrl)->first());
    }

    public function getProjectByUrl($strProjectUrl){
        return ($this->project->where("url_allmusic", $strProjectUrl)->first());
    }
}
