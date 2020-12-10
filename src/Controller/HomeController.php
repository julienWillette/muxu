<?php
/**
 * Created by PhpStorm.
 * User: aurelwcs
 * Date: 08/04/19
 * Time: 18:40
 */

namespace App\Controller;

use App\Model\ProductManager;
use App\Model\HeaderManager;
use App\Service\CartService;
use App\Model\FaqManager;
use App\Model\WishlistManager;
use App\Service\LoginService;
use App\Service\LikedService;
use App\Model\UserManager;

class HomeController extends AbstractController
{
    public function products()
    {
        $cartService = new CartService();
        $likedService = new LikedService();

        $productManager = new ProductManager();
        $products = $productManager->selectAll();

        if (!empty($_SESSION['flash_message_product'])) {
            unset($_SESSION['flash_message_product']);
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!empty($_POST['add_product'])) {
                $product = $_POST['add_product'];
                $cartService->add($product);
            }
            if (!empty($_POST['search'])) {
                $term = $_POST['search'];
                $products = $productManager->searchFull($term);
            }
            if (isset($_POST['category_id']) && !empty($_POST['category_id'])) {
                $categoryId = $_POST['category_id'];
                $products = $productManager->searchByCategory($categoryId);
            }
            if (isset($_POST['dislike']) && !empty($_POST['dislike'])) {
                $likedService->dislike($_POST['dislike']);
                header('Location:/home/products');
            }
            if (isset($_POST['like']) && !empty($_POST['like'])) {
                $likedService->like($_POST['like']);
                header('Location:/home/products');
            }
        }
        $result = [];
        $wishlist = null;
        $wishlistManager = new WishlistManager();
        if (isset($_SESSION['id']) && !empty($_SESSION['id'])) {
            $wishlist = $wishlistManager->getWishlistByUser($_SESSION['id']);
        }

        if (!isset($_SESSION['user']) && empty($_SESSION['user'])) {
            foreach ($products as $product) {
                $result[] = $product;
            }
        }

        if ($wishlist) {
            foreach ($products as $product) {
                foreach ($wishlist as $wish) {
                    if ($wish['product_id'] === $product['id']) {
                        $product['is_liked'] = 'true';
                    }
                }
                $result[] = $product;
            }
        } else {
            $result = $products;
        }

        return $this->twig->render('Home/products.html.twig', [
            'products' => $result,
            'wishlist' => $wishlist
        ]);
    }

    public function index()
    {
        $headerManager = new HeaderManager();
        $headers = $headerManager->selectAll();
        
        return $this->twig->render('Home/index.html.twig', ['headers' => $headers]);
    }

    /**
     * Display product informations specified by $id
     *
     * @param int $id
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function show(int $id)
    {
        $cartService = new CartService();
        $likedService = new LikedService();
        $productManager = new ProductManager();
        $product = $productManager->selectOneById($id);
        $images = $productManager->selectAllImgByProduct($id);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!empty($_POST['add_product'])) {
                $product = $_POST['add_product'];
                $cartService->add($product);
            }
            if (isset($_POST['dislike']) && !empty($_POST['dislike'])) {
                $likedService->dislike($_POST['dislike']);
                header('Location:/home/show/' . $id);
            }
            if (isset($_POST['like']) && !empty($_POST['like'])) {
                $likedService->like($_POST['like']);
                header('Location:/home/show/' . $id);
            }
        }

        if (!isset($product['id']) || ($product['is_activated'] === '0')) {
            return $this->twig->render('Error/error_404.html.twig');
        }
        $result = [];
        $wishlist = null;
        $wishlistManager = new WishlistManager();
        if (isset($_SESSION['id']) && !empty($_SESSION['id'])) {
            $wishlist = $wishlistManager->getWishlistByUser($_SESSION['id']);
        }
        if (!isset($_SESSION['user']) && empty($_SESSION['user'])) {
                $result[] = $product;
        }
        if ($wishlist) {
            foreach ($wishlist as $wish) {
                if ($wish['product_id'] === $product['id']) {
                    $product['is_liked'] = 'true';
                }
            }
            $result[] = $product;
        } else {
            $result = $product;
        }

        return $this->twig->render('Home/show.html.twig', [
            'product' => $product,
            'images' => $images,
            'wishlist' => $wishlist
            ]);
    }

    public function cart()
    {
        // login if the customer is not identify on the cart
        $loginService = new LoginService();
        $loginCart = $loginService ->login();
        $errorLogin= null;
        // end of login

        $cartService = new CartService();
        $errorForm = null;
        $productsDetails = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['delete_id'])) {
                $product = $_POST['delete_id'];
                $cartService->delete($product);
            }
            if (!empty($_POST['add_product'])) {
                $product = $_POST['add_product'];
                $cartService->add($product);
            }
            if (isset($_POST['payment'])) {
                $cartService->payment($_POST);
            }
        }
        if (!isset($_SESSION['user']) && empty($_SESSION['user'])) {
            $user = null;
        }
        if (isset($_SESSION['user']) && !empty($_SESSION['user'])) {
            $wishlistManager = new WishlistManager();
            $wishlist = $wishlistManager->getWishlistByUser($_SESSION['id']);
            $likedService = new LikedService();
            $productManager = new ProductManager();
            $userManager = new UserManager();
            $user = $userManager->selectOneById($_SESSION['id']);
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
                header('Location:/home/cart');
            }
            if (isset($_POST['like']) && !empty($_POST['like'])) {
                $likedService->like($_POST['like']);
                header('Location:/home/cart');
            }
        }

        return $this->twig->render('Home/cart.html.twig', [
            'cartInfos' => $cartService->cartInfos() ? $cartService->cartInfos() : null,
            'total' => $cartService->cartInfos() ? $cartService->totalCart() : null,
            'errorForm' => $errorForm,
            'errorLogin' => $errorLogin,
            'user' => $user,
            'wishlist' => $productsDetails
        ]);
    }

    public function faq()
    {
        $faqManager = new FaqManager();
        $faqs = $faqManager->selectAll();

        return $this->twig->render('Home/faq.html.twig', ['faqs' => $faqs]);
    }

    public function success()
    {
        return $this->twig->render('Home/success.html.twig');
    }

    public function contact()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ((isset($_POST['submit']) &&
            !empty($_POST['firstname']) &&
            !empty($_POST['lastname']))) {
                $userContact = [
                    'firstname' => $_POST['firstname'],
                    'lastname' => $_POST['lastname']
                ];
                return $this->twig->render('Home/confirmation.html.twig', ['userContact' => $userContact]);
            }
        }

        return $this->twig->render('Home/contact.html.twig');
    }
    
    public function suggestion()
    {
        $productManager = new ProductManager();
        $products = $productManager->selectAll();

        
        $productsActivated = [];
        foreach ($products as $product) {
            if ($product['is_activated'] == 1) {
                array_push($productsActivated, $product);
            }
        }
    
        $product = [];
        array_push($product, $productsActivated[array_rand($productsActivated, 1)]);

        return $this->twig->render('Home/suggestion.html.twig', [
            'product' => $product
            ]);
    }

    public function newsletter()
    {
        return $this->twig->render('Home/newsletter.html.twig');
    }
}
