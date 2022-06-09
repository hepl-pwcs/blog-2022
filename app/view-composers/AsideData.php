<?php

namespace Blog\ViewComposers;

use Blog\Models\Post;
use Blog\Models\Author;
use Blog\Models\Category;

trait AsideData
{
    public function fetch_aside_data(): array
    {
        $authors = Author::all();
        $categories = Category::all();
        $most_recent_post = Post::latest('published_at')->first();

        return compact('authors', 'categories', 'most_recent_post');
    }
}