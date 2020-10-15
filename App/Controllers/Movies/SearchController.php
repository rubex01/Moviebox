<?php

namespace App\Controllers\Movies;

use Framework\Pages\Pages;

class SearchController
{
    public function search()
    {
        $query = $_GET['q'];
        $page = '';

        if (isset($_GET['page'])) {
            $page = $_GET['page'];
        }

        $response = file_get_contents('https://yts.mx/api/v2/list_movies.json?query_term='.urlencode($query = $_GET['q']).'&page='.$page);
        $response = json_decode($response);

        $maxPages = ceil($response->data->movie_count / 20);

        return Pages::view('default', 'movies/result', [
            'CSS' => ['movies/search.css'],
            'titleAppend' => 'Search Results',
            'results' => $response,
            'query' => $query,
            'maxPage' => $maxPages
        ], false);
    }
}