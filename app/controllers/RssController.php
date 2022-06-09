<?php

namespace Blog\Controllers;

use Blog\Models\Post;

class RssController
{

    public function index(): array
    {
        $posts = Post::with(['author', 'categories'])
            ->orderBy('published_at', DEFAULT_SORT_ORDER)
            ->get();

        // Rendering
        $view_data = [];
        $view_data['view'] = 'rss/index.php';
        $view_data['data'] = compact('posts');
        header('Content-Type: application/rss+xml');
        return $view_data;
    }
}