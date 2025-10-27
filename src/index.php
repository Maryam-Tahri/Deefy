<?php

require_once 'vendor/autoload.php';
use iutnc\deefy\dispatch\Dispatcher;
use iutnc\deefy\repository\DeefyRepository;

try{
    DeefyRepository::setConfig('db.config.ini');
    echo "✅ Configuration chargée !<br>";
}catch(Exception $e){
    echo $e->getMessage();
}
session_start();

$dispatcher = new Dispatcher();

$dispatcher->run();

//$repo = DeefyRepository::getInstance();
//$playlists = $repo->findAllPlaylists();
//foreach ($playlists as $pl) {
//    print "playlist  : " . $pl->nom . ":". $pl->id . "<br>";
//}


//$pl = new PlayList('test');
//$pl = $repo->saveEmptyPlaylist($pl);
//print "playlist  : " . $pl->nom . ":". $pl->id . "\n";

//$track = new PodcastTrack('test', 'test.mp3', 'auteur', '2021-01-01', 10, 'genre');
//$track = $repo->savePodcastTrack($track);
//print "track 2 : " . $track->titre . ":". get_class($track). "\n";
//$repo->addTrackToPlaylist($pl->id, $track->id);

