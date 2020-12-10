<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 11/10/17
 * Time: 16:07
 * PHP version 7
 */
namespace App\Controller;

use App\Model\ImageManager;
use App\Model\HeaderManager;
use App\Model\ProductManager;
use App\Model\ArtistManager;

/**
 * Class ImageController
 *
 */
class ImageController extends AbstractController
{
    /**
     * Display image listing
     *
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function index()
    {
        if (isset($_SESSION['admin']) && !empty($_SESSION['admin'])) {
            $imageManager = new ImageManager();
            $images = $imageManager->selectAll();

            return $this->twig->render('Image/index.html.twig', ['images' => $images]);
        } else {
            header('Location:/');
        }
    }
    /**
     * Display image informations specified by $id
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
            $imageManager = new ImageManager();
            $image = $imageManager->selectOneById($id);

            return $this->twig->render('Image/show.html.twig', ['image' => $image]);
        } else {
            header('Location:/');
        }
    }
    /**
     * Display image edition page specified by $id
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
            $imageManager = new ImageManager();
            $image = $imageManager->selectOneById($id);

            $productManager = new ProductManager();
            $products = $productManager->selectAll();

            $artistManager = new ArtistManager();
            $artists = $artistManager->selectAll();

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                if (empty($_POST['artist_id'])) {
                    $_POST['artist_id'] = null;
                }
                if (empty($_POST['product_id'])) {
                    $_POST['product_id'] = null;
                }
                $image = [
                    'id' => $_POST['id'],
                    'url' => $_POST['url'],
                    'artist_id' => $_POST['artist_id'],
                    'product_id' => $_POST['product_id'],
                ];

                $imageManager->update($image);
                header('Location:/image/show/' . $id);
            }

            return $this->twig->render('Image/edit.html.twig', [
                'image' => $image,
                'artists' => $artists,
                'products' => $products
                ]);
        } else {
            header('Location:/');
        }
    }
    /**
     * Display image creation page
     *
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function add()
    {
        if (isset($_SESSION['admin']) && !empty($_SESSION['admin'])) {
            $productManager = new ProductManager();
            $products = $productManager->selectAll();

            $artistManager = new ArtistManager();
            $artists = $artistManager->selectAll();

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $imageManager = new ImageManager();
                if (empty($_POST['artist_id'])) {
                    $_POST['artist_id'] = null;
                }
                if (empty($_POST['product_id'])) {
                    $_POST['product_id'] = null;
                }
                $image = [
                    'url' => $_POST['url'],
                    'artist_id' => $_POST['artist_id'],
                    'product_id' => $_POST['product_id']
                ];
                $id = $imageManager->insert($image);
                header('Location:/image/show/' . $id);
            }

            return $this->twig->render('Image/add.html.twig', [
                'artists' => $artists,
                'products' => $products
            ]);
        } else {
            header('Location:/');
        }
    }
    /**
     * Handle image deletion
     *
     * @param int $id
     */
    public function delete(int $id)
    {
        if (isset($_SESSION['admin']) && !empty($_SESSION['admin'])) {
            $imageManager = new ImageManager();
            $headerManager = new HeaderManager();
            $header = $headerManager->selectOneByIdImage($id);
            if (!$header) {
                $imageManager->delete($id);
                header('Location:/image/index');
            } else {
                return $this->twig->render('Error/error_admin.html.twig', [
                    'error' => "Plop error too bad"
                ]);
            }
        } else {
            header('Location:/');
        }
    }

    public function url(int $id)
    {
        if (isset($_SESSION['admin']) && !empty($_SESSION['admin'])) {
            $imageManager = new ImageManager();
            $image = $imageManager->selectOneById($id);
            
            return json_encode($image);
        } else {
            header('Location:/');
        }
    }
}
