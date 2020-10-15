<?php

namespace App\Controllers\Movies;

use App\Controllers\TransmissionRequestTrait;
use Framework\Auth\Auth;
use Framework\Database\Database;
use Framework\Pages\Pages;
use Framework\Request\Request;

class WatchController
{
    use TransmissionRequestTrait;

    public function watchStart(Request $request, $hash, $title, $id, $staticData)
    {
        $stmt = Database::$Connections['MySQL']->prepare("SELECT movie_id FROM movies WHERE hash = ?");
        $stmt->bind_param('s', $hash);
        $stmt->execute();
        $downloadedMovieId = $stmt->get_result()->fetch_row();

        if ($downloadedMovieId !== null) {
            $stmt = Database::$Connections['MySQL']->prepare("INSERT INTO user_movies (user_id, movie_id) VALUES (?, ?)");
            $stmt->bind_param('ss', Auth::$id, $downloadedMovieId[0]);
            $stmt->execute();
            header('location:/popcorn/'.$downloadedMovieId[0]);
            exit();
        }

        $magnet = 'magnet:?xt=urn:btih:'.$hash.'&dn='.urlencode($title).'&tr=udp://open.demonii.com:1337/announce&tr=udp://open.demonii.com:1337/announce&tr=udp://tracker.openbittorrent.com:80&tr=udp://tracker.coppersurfer.tk:6969&tr=udp://glotorrents.pw:6969/announce&tr=udp://tracker.opentrackr.org:1337/announce&tr=udp://torrent.gresille.org:80/announce&tr=udp://p4p.arenabg.com:1337&tr=udp://tracker.leechers-paradise.org:6969';

        $arguments = [
            'filename' => $magnet,
            'paused' => 'false'
        ];

        $response = $this->transmissionRequest('torrent-add', json_encode($arguments, JSON_UNESCAPED_SLASHES));

        $status = 'downloading';

        $stmt = Database::$Connections['MySQL']->prepare("INSERT INTO movies (hash, movie_name, official_id, status, static_data) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param('sssss',
                          $hash,
                          $title,
                          $id,
                          $status,
                          $staticData
        );
        $stmt->execute();

        $movieId = mysqli_insert_id(Database::$Connections['MySQL']);

        $stmt = Database::$Connections['MySQL']->prepare("INSERT INTO user_movies (user_id, movie_id) VALUES (?, ?)");
        $stmt->bind_param('ss',
                          Auth::$id,
                          $movieId
        );
        $stmt->execute();

        header('location:/downloads');
    }

    public function getDownloads()
    {
        $stmt = Database::$Connections['MySQL']->prepare("SELECT movies.movie_id, movie_name, static_data, status, hash FROM movies JOIN user_movies ON user_movies.movie_id = movies.movie_id WHERE user_id = ?");
        $stmt->bind_param('s', Auth::$id);
        $stmt->execute();
        $movies = $stmt->get_result()->fetch_all(MYSQLI_NUM);

        return Pages::view('default', 'movies/downloads', [
            'titleAppend' => 'Downloads',
            'CSS' => ['movies/download.css'],
            'movies' => $movies,
            'JS' => ['download.js']
        ], false);
    }

    public function getMovie(Request $request, $movieId)
    {
        $stmt = Database::$Connections['MySQL']->prepare("SELECT movies.*, user_movies.time FROM movies JOIN user_movies ON user_movies.movie_id = movies.movie_id WHERE user_id = ? AND movies.movie_id = ?");
        $stmt->bind_param('ss', Auth::$id, $movieId);
        $stmt->execute();
        $movie = $stmt->get_result()->fetch_row();



        $response = $this->transmissionRequest('torrent-get', json_encode(['ids' => [$movie[1]], 'fields' => ['name', 'files', 'percentDone']], JSON_UNESCAPED_SLASHES));

        $movieFormats = [
            'mp4', 'mov', 'wmv', 'flv', 'avi', 'webm', 'mkv'
        ];

        foreach ($response->arguments->torrents[0]->files as $file) {
            $filetype = strtolower(end(explode('.', $file->name)));
            if (in_array($filetype, $movieFormats)) {
                $movieDir = $file->name;
                $videoType = $filetype;
            }
        }

        $percentage = ($response->arguments->torrents[0]->percentDone * 100);

        return Pages::view('watch', 'movies/watch', [
            'titleAppend' => 'Watch ' . $movie[2],
            'CSS' => ['movies/watch.css'],
            'JS' => ['watch.js'],
            'movie' => $movie,
            'percentage' => $percentage,
            'file' => $movieDir,
            'filetype' => $videoType,
        ], false);
    }

    public function deleteMovie(Request $request, $movieId)
    {
        $stmt = Database::$Connections['MySQL']->prepare("SELECT movies.* FROM movies JOIN user_movies ON user_movies.movie_id = movies.movie_id WHERE movies.movie_id = ?");
        $stmt->bind_param('s', $movieId);
        $stmt->execute();
        $movieAndUsers = $stmt->get_result()->fetch_all(MYSQLI_NUM);

        $stmt = Database::$Connections['MySQL']->prepare("Delete from user_movies WHERE movie_id = ? AND user_id = ?");
        $stmt->bind_param('ss', $movieId, Auth::$id);
        $stmt->execute();

        if (count($movieAndUsers) === 1) {

            $this->transmissionRequest('torrent-remove', json_encode(['ids' => [$movieAndUsers[0][1]], 'delete-local-data' => 'true'], JSON_UNESCAPED_SLASHES));

            $stmt = Database::$Connections['MySQL']->prepare("Delete from movies WHERE movie_id = ?");
            $stmt->bind_param('s', $movieId);
            $stmt->execute();
        }

        header('location:/downloads');
    }
}