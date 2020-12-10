<?php

namespace App\Controller;

use App\Model\SizeManager;

/**
 * Class SizeController
 *
 */
class SizeController extends AbstractController
{


    /**
     * Display size listing
     *
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function index()
    {
        if (isset($_SESSION['admin']) && !empty($_SESSION['admin'])) {
            $sizeManager = new SizeManager();
            $sizes = $sizeManager->selectAll();

            return $this->twig->render('Size/index.html.twig', ['sizes' => $sizes]);
        } else {
            header('Location:/');
        }
    }


    /**
     * Display size informations specified by $id
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
            $sizeManager = new SizeManager();
            $size = $sizeManager->selectOneById($id);

            return $this->twig->render('Size/show.html.twig', ['size' => $size]);
        } else {
            header('Location:/');
        }
    }


    /**
     * Display size edition page specified by $id
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
            $sizeManager = new SizeManager();
            $size = $sizeManager->selectOneById($id);

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $size['format'] = $_POST['format'];
                $sizeManager->update($size);
                header('Location:/size/show/' . $id);
            }

            return $this->twig->render('Size/edit.html.twig', ['size' => $size]);
        } else {
            header('Location:/');
        }
    }

    
    /**
     * Display size creation page
     *
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function add()
    {
        if (isset($_SESSION['admin']) && !empty($_SESSION['admin'])) {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $sizeManager = new SizeManager();
                $size = [
                    'format' => $_POST['format'],
                ];
                $id = $sizeManager->insert($size);
                header('Location:/size/show/' . $id);
            }
            return $this->twig->render('Size/add.html.twig');
        } else {
            header('Location:/');
        }
    }
}
