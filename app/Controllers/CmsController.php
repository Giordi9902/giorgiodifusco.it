<?php

namespace App\Controllers;

use Core\Controller;
use App\Models\BlogPost;
use App\Models\BlogImage;
use App\Models\Course;
use App\Models\Subject;

class CmsController extends Controller
{
    public function index()
    {
        $this->requireAdmin();
        $postModel = new BlogPost();
        $stats = [
            'total'     => $postModel->countAll(),
            'published' => $postModel->countByStatus('published'),
            'drafts'    => $postModel->countByStatus('draft'),
            'recent'    => $postModel->findRecent(6),
        ];
        $this->view('cms/dashboard', ['stats' => $stats]);
    }

    public function posts()
    {
        $this->requireAdmin();
        $this->view('cms/blog');
    }

    public function postCreate()
    {
        $this->requireAdmin();
        $this->view('cms/blog_create');
    }

    public function areas()
    {
        $this->requireAdmin();
        $this->view('cms/areas');
    }

    public function postEdit($id)
    {
        $this->requireAdmin();
        $postModel = new BlogPost();
        $post = $postModel->findById($id);
        if (!$post) {
            $this->redirect('/cms/blog');
        }

        $featuredImage = null;
        if (!empty($post['featured_image_id'])) {
            $featuredImage = (new BlogImage())->findById($post['featured_image_id']);
        }

        $this->view('cms/blog_edit', ['post' => $post, 'featuredImage' => $featuredImage]);
    }
}
