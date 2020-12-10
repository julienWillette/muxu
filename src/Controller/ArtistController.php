<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 11/10/17
 * Time: 16:07
 * PHP version 7
 */

namespace App\Controller;

use App\Model\ArtistManager;
use App\Model\ImageManager;

/**
 * Class ArtistController
 *
 */
class ArtistController extends AbstractController
{


    /**
     * Display artiste listing
     *
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function index()
    {
        if (isset($_SESSION['admin']) && !empty($_SESSION['admin'])) {
            $artistManager = new ArtistManager();
            $artists = $artistManager->selectAll();

            return $this->twig->render('Artist/index.html.twig', ['artists' => $artists]);
        } else {
            header('Location:/');
        }
    }


    /**
     * Display artiste informations specified by $id
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
            $artistManager = new ArtistManager();
            $artist = $artistManager->selectOneById($id);

            $images = $artistManager->selectAllImgByArtist($id);
        
            return $this->twig->render('Artist/show.html.twig', ['artist' => $artist,
            'images' => $images]);
        } else {
            header('Location:/');
        }
    }


    /**
     * Display artiste edition page specified by $id
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
            $artistManager = new ArtistManager();
            $artist = $artistManager->selectOneById($id);

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $artist['firstname'] = $_POST['firstname'];
                $artist['lastname'] = $_POST['lastname'];
                $artist['description'] = $_POST['description'];
                $artistManager->update($artist);
                header('Location:/artist/show/' . $id);
            }

            return $this->twig->render('Artist/edit.html.twig', ['artist' => $artist]);
        } else {
            header('Location:/');
        }
    }


    /**
     * Display artiste creation page
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
                $artistManager = new ArtistManager();
                $artist = [
                    'firstname' => $_POST['firstname'],
                    'lastname' => $_POST['lastname'],
                    'description' => $_POST['description']
                
                ];
                $id = $artistManager->insert($artist);
                header('Location:/artist/show/' . $id);
            }

            return $this->twig->render('Artist/add.html.twig');
        } else {
            header('Location:/');
        }
    }
}
