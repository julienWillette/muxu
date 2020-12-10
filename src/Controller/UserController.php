<?php

namespace App\Controller;

use App\Model\UserManager;
use App\Model\CommandManager;
use App\Model\ProductManager;
use App\Model\WishlistManager;
use App\Service\LikedService;
use App\Service\CartService;

class UserController extends AbstractController
{
    
    /**
     * Display account user page
     *
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function account()
    {
        if (!isset($_SESSION['user'])) {
            header('Location:/security/login');
        } else {
            $commandManager = new CommandManager();
            $commands = $commandManager->selectAll();

            $userCommands = [];
            foreach ($commands as $command) {
                if (($_SESSION['id']) == $command['user_id']) {
                    array_push($userCommands, $command);
                }
            }

            if (isset($_SESSION['user']) && !empty($_SESSION['user'])) {
                $userManager = new UserManager();
                $user = $userManager->selectOneById($_SESSION['id']);

                $wishlistManager = new WishlistManager();
                $wishlist = $wishlistManager->getWishlistByUser($user['id']);
                $likedService = new LikedService();
                $productManager = new ProductManager();
                $productsDetails = [];

                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    if ((!empty($_POST['firstname']) &&
                    !empty($_POST['lastname']) &&
                    !empty($_POST['address']) &&
                    !empty($_POST['email']))) {
                        $newsletter = isset($_POST['newsletter']) ? true : false;
                        $user = [
                            'id' => $_POST['id'],
                            'firstname' => $_POST['firstname'],
                            'lastname' => $_POST['lastname'],
                            'email' => $_POST['email'],
                            'address' => $_POST['address'],
                            'newsletter' => $newsletter,
                        ];
                        $userManager->update($user);
                        header('Location:/user/account');
                    }
                }

                foreach ($wishlist as $wish) {
                    $product = $productManager->selectOneById($wish['product_id']);
                    $product['wishlist_id'] = $wish['id'];
                    $product['is_liked'] = 'true';
                    $productsDetails[] = $product;
                }
                if (isset($_POST['dislike']) && !empty($_POST['dislike'])) {
                    $likedService->dislike($_POST['dislike']);
                    header('Location:/user/account');
                }
                if (isset($_POST['like']) && !empty($_POST['like'])) {
                    $likedService->like($_POST['like']);
                    header('Location:/user/account');
                }

                $cartService = new CartService();

                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    if (!empty($_POST['add_product'])) {
                        $product = $_POST['add_product'];
                        $cartService->add($product);
                    }
                }

                return $this->twig->render('User/account.html.twig', [
                    'commands' => $commands,
                    'userCommands' => $userCommands,
                    'user' => $user,
                    'wishlist' => $productsDetails
                ]);
            }
        }
    }

    public function command(int $id)
    {
        $commandManager = new CommandManager();
        $command = $commandManager->selectOneById($id);
        $invProducts = $commandManager->selectAllProductsByCommand();
        
        if (isset($_SESSION['user'])) {
            if (isset($command['user_id'])) {
                if (($_SESSION['id']) == $command['user_id']) {
                    $detailsCommand = [];
                    foreach ($invProducts as $invProduct) {
                        if ($invProduct['invoice_id'] == $command['invoice_id']) {
                            array_push($detailsCommand, $invProduct);
                        }
                    }
                } else {
                    return $this->twig->render('Error/error_404.html.twig');
                }
            } else {
                return $this->twig->render('Error/error_404.html.twig');
            }
        } else {
            return $this->twig->render('Error/error_404.html.twig');
        }

        return $this->twig->render('User/command.html.twig', ['command' => $command,
        'detailsCommand' => $detailsCommand]);
    }
}
