<?php


namespace App\Controller;

use App\Model\CategoryManager;

/**
 * Class CategoryController
 *
 */
class CategoryController extends AbstractController
{


    /**
     * Display category listing
     *
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function index()
    {
        if (isset($_SESSION['admin']) && !empty($_SESSION['admin'])) {
            $categoryManager = new CategoryManager();
            $categories = $categoryManager->selectAll();

            return $this->twig->render('Category/index.html.twig', ['categories' => $categories]);
        } else {
            header('Location:/');
        }
    }


    /**
     * Display category informations specified by $id
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
            $categoryManager = new CategoryManager();
            $category = $categoryManager->selectOneById($id);

            return $this->twig->render('Category/show.html.twig', ['category' => $category]);
        } else {
            header('Location:/');
        }
    }


    /**
     * Display category edition page specified by $id
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
            $categoryManager = new CategoryManager();
            $category = $categoryManager->selectOneById($id);

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $category['name'] = $_POST['name'];
                $categoryManager->update($category);
                header('Location:/category/show/' . $id);
            }

            return $this->twig->render('Category/edit.html.twig', ['category' => $category]);
        } else {
            header('Location:/');
        }
    }


    /**
     * Display category creation page
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
                $categoryManager = new CategoryManager();
                $category = [
                    'name' => $_POST['name'],
                ];
                $id = $categoryManager->insert($category);
                header('Location:/category/show/' . $id);
            }

            return $this->twig->render('Category/add.html.twig');
        } else {
            header('Location:/');
        }
    }
}
