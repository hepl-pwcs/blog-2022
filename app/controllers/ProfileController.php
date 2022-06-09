<?php

namespace Blog\Controllers;

use Blog\Models\Author;
use JetBrains\PhpStorm\NoReturn;
use Blog\ViewComposers\AsideData;
use Intervention\Image\ImageManagerStatic;
use Blog\Request\Validators\UpdateProfileRequest;

class ProfileController
{
    use AsideData;
    use UpdateProfileRequest;

    private Author $author;

    public function __construct()
    {
        if (!isset($_SESSION['connected_author'])) {
            header('Location: http://blog.test/?action=login&resource=auth');
            exit;
        }
        $this->author = unserialize($_SESSION['connected_author']);
    }

    public function edit(): array
    {
        $view_data = [];
        $view_data['view'] = 'profile/edit_form.php';
        $view_data['data'] = array_merge(['author' => $this->author], $this->fetch_aside_data());

        return $view_data;
    }

    #[NoReturn] public function update(): void
    {
        if (!$this->has_validation_errors()) {
            $author = unserialize($_SESSION['connected_author']);

            $email = empty($_POST['email']) ? $author->email : $_POST['email'];
            $password = empty($_POST['password']) ? $author->password : password_hash($_POST['password'],
                PASSWORD_DEFAULT);

            $avatar = $author->avatar;
            if ($_FILES['avatar']['tmp_name'] != '') {
                $image = ImageManagerStatic::make($_FILES['avatar']['tmp_name']);
                $image->resize(200, null, function ($constraint) {
                    $constraint->aspectRatio();
                });
                $avatar = 'images/'.uniqid().'.jpg';
                $image->save($avatar, 80);
            }

            $author->update(compact('email', 'password', 'avatar'));
            $_SESSION['connected_author'] = serialize($author);
            header('Location: index.php?action=edit&resource=profile');
            exit;
        } else {
            $_SESSION['old'] = $_POST;
            header('Location: index.php?action=edit&resource=profile#general-error');
            exit;
        }
    }
}