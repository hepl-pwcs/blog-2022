<?php

namespace Blog\Request\Validators;

use Blog\Models\Author;
use Intervention\Image\ImageManagerStatic as Image;

trait UpdateProfileRequest
{
    public function has_validation_errors(): bool
    {
        unset($_SESSION['errors'], $_SESSION['old']);
        if (!isset($_POST['email']) ||
            !isset($_POST['old_password']) ||
            !isset($_POST['password']) ||
            !isset($_POST['repeat_password']) ||
            !isset($_FILES['avatar'])
        ) {
            $_SESSION['errors']['general'] = 'Merci d’utiliser le formulaire sans le modifier';

            return true;
        }

        if (!empty($_POST['email'])) {
            if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
                $_SESSION['errors']['email'] = 'Cette adresse email n’est pas valide';
            }
        }

        if (!empty($_POST['password'])) {
            if (empty($_POST['old_password'])) {
                $_SESSION['errors']['old_password'] = 'Vous devez entrer l’ancien mot de passe pour pouvoir le changer';
            } else {
                $author = unserialize($_SESSION['connected_author']);
                if (!password_verify($_POST['old_password'], $author->password)) {
                    $_SESSION['errors']['old_password'] = 'L’ancien mot de passe fourni ne correspond pas à celui que vous aviez entré';
                }
            }
            if (empty($_POST['repeat_password'])) {
                $_SESSION['errors']['repeat_password'] = 'Vous devez répéter le nouveau mot de passe pour pouvoir changer l’actuel';
            }
            if ($_POST['password'] !== $_POST['repeat_password']) {
                $_SESSION['errors']['repeat_password'] = 'Vous n’avez pas correctement répété le nouveau mot de passe';
            }
            if (!preg_match('~[0-9]+~', $_POST['password'])) {
                $_SESSION['errors']['password'] = 'Le mot de passe doit contenir un chiffre';
            }
            if (!preg_match('~[A-Z]+~', $_POST['password'])) {
                $_SESSION['errors']['password'] = 'Le mot de passe doit contenir une lettre capitale';
            }
            if (mb_strlen($_POST['password']) > 64 || mb_strlen($_POST['password']) < 8) {
                $_SESSION['errors']['password'] = 'Le mot de passe n’a pas la bonne taille';
            }
        }
        $valid_types = [
            'image/jpg',
            'image/jpeg',
            'image/png',
        ];
        if ($_FILES['avatar']['error'] !== 4) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            if (!in_array(finfo_file($finfo, $_FILES['avatar']['tmp_name']), $valid_types)) {
                $_SESSION['errors']['avatar'] = 'Le type du fichier ne semble pas être celui d’une image';
            } else {
                $image = Image::make($_FILES['avatar']['tmp_name']);
                if ($image->getWidth() < 200 ||
                    $image->getWidth() > 2000 ||
                    $image->getHeight() < 200 ||
                    $image->getHeight() > 2000) {
                    $_SESSION['errors']['avatar'] = 'Les dimensions de l’image ne sont pas comprises entre 200 et 2000 pixels';
                }
            }
            finfo_close($finfo);

        }


        return isset($_SESSION['errors']) && count($_SESSION['errors']);
    }
}