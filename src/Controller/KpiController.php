<?php
namespace App\Controller;

use App\Model\ArtistManager;
use App\Model\CommandManager;
use App\Model\UserManager;
use App\Model\CategoryManager;
use App\Model\WishlistManager;
use App\Model\ProductManager;
use App\Service\FilterService;
use App\Model\InvPdtManager;

class KpiController extends AbstractController
{
    public function index()
    {
        if (isset($_SESSION['admin']) && !empty($_SESSION['admin'])) {
            $filterService = new FilterService();

            $commandManager = new commandManager();
            $commands = $commandManager->selectAll();

            // KPI : numbers of commands
            $date1 = null;
            $date2 = null;
            $nbCommands = 0;
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                if (isset($_POST['date1']) && !empty($_POST['date1'])
                && isset($_POST['date2']) && !empty($_POST['date2'])) {
                    $commands = $filterService->getCommandsByDate($_POST, $commands);
                    $nbCommands = count($commands);
                    $date1=$_POST['date1'];
                    $date2=$_POST['date2'];
                }
            } else {
                $nbCommands = count($commands);
            }
            //KPI : average cart
            $totalCommands = 0;
            foreach ($commands as $command) {
                $totalCommands += $command['total'];
            }
            if ($nbCommands > 0) {
                $avgCart = ceil($totalCommands / $nbCommands);
            } else {
                $avgCart = 0;
            }
            
            // KPI : total artist
            $artistManager = new artistManager();
            $artists = $artistManager->selectAll();

            $nbArtists = count($artists);

            // KPI : total users
            $userManager = new userManager();
            $users = $userManager->selectAll();

            $nbUsers = count($users);

            // KPI : total categories
            $categoryManager = new categoryManager();
            $categories = $categoryManager->selectAll();

            $nbCategories = count($categories);
            // Product most liked by user
            $wishlistManager = new WishlistManager();
            $productsLiked = $wishlistManager->selectAll();

            $wishlistProducts = [];
            foreach ($productsLiked as $productLiked) {
                array_push($wishlistProducts, $productLiked['product_id']);
            }
 
            $likes= array_count_values($wishlistProducts);
            $mostLikedProductId = array_keys($likes, max($likes));
            $mostLikedProductId = $mostLikedProductId[0];

            $productManager = new ProductManager();
            $mostLikedProduct = $productManager->selectOneById($mostLikedProductId);
        
            //KPI : best-seller
            $invPdtManager = new InvPdtManager();
            $bestSellers = $invPdtManager->bestSeller();

            $productManager = new ProductManager();
            $products = $productManager->selectAll();
            $nbProducts = count($products);
        
            return $this->twig->render('Kpi/index.html.twig', [
                'nbCommands' => $nbCommands,
                'avgCart' => $avgCart,
                'nbArtists' => $nbArtists,
                'nbUsers' => $nbUsers,
                'nbCategories' => $nbCategories,
                'date1' => $date1,
                'date2' => $date2,
                'bestSellers' => $bestSellers,
                'mostLikedProduct' => $mostLikedProduct,
                'nbProducts' => $nbProducts
            ]);
        } else {
            header('Location:/');
        }
    }

    public function pieChart()
    {
        $productManager = new ProductManager();
        $products = $productManager->selectAll();
        $categoryManager = new CategoryManager();
    
        $productsCategoryId = [];
        foreach ($products as $product) {
            array_push($productsCategoryId, $product['category_id']);
        }
    
        $nbByCategory= array_count_values($productsCategoryId);
            
        $nbProducts = count($products);
            
        $avgCategory = [];
        foreach ($nbByCategory as $key => $value) {
            $value = ceil(($value / $nbProducts) * 100);
            $category = $categoryManager->selectOneById($key);
            $categoryName = $category['name'];
            $avgCategory[$categoryName]=$value;
        }

        return json_encode($avgCategory);
    }
}
