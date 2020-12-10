<?php

namespace App\Controller;

use App\Model\HeaderManager;
use App\Model\ImageManager;

/**
 * Class HeaderController
 *
 */
class HeaderController extends AbstractController
{


    /**
     * Display Header listing
     *
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function index()
    {
        if (isset($_SESSION['admin']) && !empty($_SESSION['admin'])) {
            $headerManager = new HeaderManager();
            $headers = $headerManager->selectAll();

            return $this->twig->render('Header/index.html.twig', ['headers' => $headers]);
        } else {
            header('Location:/');
        }
    }


    /**
     * Display Header informations specified by $id
     *
     * @param int $id
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function show(int $id)
    {
        if (isset($_SESSION['admin']) && !empty($_SESSION['admin'])) {
            $headerManager = new HeaderManager();
            $header = $headerManager->selectOneById($id);

            return $this->twig->render('Header/show.html.twig', ['header' => $header]);
        } else {
            header('Location:/');
        }
    }


    /**
     * Display Header edition page specified by $id
     *
     * @param int $id
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */

     
    public function edit(int $id)
    {
        if (isset($_SESSION['admin']) && !empty($_SESSION['admin'])) {
            $headerManager = new HeaderManager();
            $header = $headerManager->selectOneById($id);

            $imageManager = new ImageManager();
            $images = $imageManager->selectAll();

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                if (empty($_POST['img_id'])) {
                    $_POST['img_id'] = null;
                }
                $header = [
                    'id' => $_POST['id'],
                    'title' => $_POST['title'],
                    'description' => $_POST['description'],
                    'img_id' => $_POST['img_id']
                ];
                $headerManager->update($header);
                header('Location:/header/show/' . $id);
            }

            return $this->twig->render('Header/edit.html.twig', [
                'images' => $images,
                'header' => $header]);
        } else {
            header('Location:/');
        }
    }
    
    /**
     * Display Header creation page
     *
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function add()
    {
        if (isset($_SESSION['admin']) && !empty($_SESSION['admin'])) {
            $imageManager = new ImageManager();
            $images = $imageManager->selectAll();

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                if (empty($_POST['img_id'])) {
                    $_POST['img_id'] = null;
                }
                $headerManager = new HeaderManager();
                $header = [
                    'title' => $_POST['title'],
                    'description' => $_POST['description'],
                    'img_id' => $_POST['img_id']
                ];
                $id = $headerManager->insert($header);
                header('Location:/header/show/' . $id);
            }

            return $this->twig->render('Header/add.html.twig', ['images' => $images]);
        } else {
            header('Location:/');
        }
    }
}
