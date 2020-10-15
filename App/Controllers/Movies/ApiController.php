<?php

namespace App\Controllers\Movies;

use App\Controllers\TransmissionRequestTrait;
use Framework\Auth\Auth;
use Framework\Database\Database;
use Framework\Request\Request;

class ApiController
{
    use TransmissionRequestTrait;

    public function __construct()
    {
        header('Content-type: application/json');
    }

    public function getDownloadStatus()
    {
        $status = 'downloading';

        $stmt = Database::$Connections['MySQL']->prepare("SELECT movies.movie_id, hash FROM movies JOIN user_movies ON user_movies.movie_id = movies.movie_id WHERE user_id = ? AND status = ?");
        $stmt->bind_param('ss', Auth::$id, $status);
        $stmt->execute();
        $movies = $stmt->get_result()->fetch_all(MYSQLI_NUM);

        $hashesArray = [];

        foreach ($movies as $key => $movie) {
            $hashesArray[] = $movie[1];
        }

        $arguments = [
            'ids' => $hashesArray,
            'fields' => ['percentDone', 'hashString']
        ];

        $response = $this->transmissionRequest('torrent-get', json_encode($arguments, JSON_UNESCAPED_SLASHES));

        $jsonResponse = json_encode($response);

        foreach ($response->arguments->torrents as $torrent) {
            if ($torrent->percentDone === 1) {
                $this->setDownloadStatus('finished', $torrent->hashString);
            }
        }

        echo $jsonResponse;
    }

    public function setDownloadStatus(string $status, string $hash)
    {
        $stmt = Database::$Connections['MySQL']->prepare("UPDATE movies SET status = ? WHERE hash = ?");
        $stmt->bind_param('ss', $status, $hash);
        $stmt->execute();

        $this->transmissionRequest('torrent-stop', json_encode(['ids' => [$hash]], JSON_UNESCAPED_SLASHES));
    }

    public function getDownloadStatusSingle(Request $request, $movieId)
    {
        $stmt = Database::$Connections['MySQL']->prepare("SELECT movies.* FROM movies JOIN user_movies ON user_movies.movie_id = movies.movie_id WHERE user_id = ? AND movies.movie_id = ?");
        $stmt->bind_param('ss', Auth::$id, $movieId);
        $stmt->execute();
        $movie = $stmt->get_result()->fetch_row();

        $arguments = [
            'ids' => $movie[1],
            'fields' => ['percentDone']
        ];

        $response = $this->transmissionRequest('torrent-get', json_encode($arguments, JSON_UNESCAPED_SLASHES));

        $jsonResponse = json_encode($response);

        if ($response->arguments->torrents[0]->percentDone === 1) {
            $this->setDownloadStatus('finished', $movie[1]);
        }

        echo $jsonResponse;
    }

    public function setTime(Request $request, $movieId)
    {
        $stmt = Database::$Connections['MySQL']->prepare("UPDATE user_movies SET time = ? WHERE movie_id = ? AND user_id = ?");
        $stmt->bind_param('sss', $request->input('time'), $movieId, Auth::$id);
        $stmt->execute();
        return true;
    }
}