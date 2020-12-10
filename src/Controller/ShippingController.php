<?php


namespace App\Controller;

use App\Model\ShippingManager;

/**
 * Class ShippingController
 *
 */
class ShippingController extends AbstractController
{


    /**
     * Display shipping listing
     *
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function index()
    {
        if (isset($_SESSION['admin']) && !empty($_SESSION['admin'])) {
            $shippingManager = new ShippingManager();
            $shipping = $shippingManager->selectAll();

            return $this->twig->render('Shipping/index.html.twig', ['shipping' => $shipping]);
        } else {
            header('Location:/');
        }
    }


    /**
     * Display shipping informations specified by $id
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
            $shippingManager = new ShippingManager();
            $shipping = $shippingManager->selectOneById($id);

            return $this->twig->render('Shipping/show.html.twig', ['shipping' => $shipping]);
        } else {
            header('Location:/');
        }
    }
}
