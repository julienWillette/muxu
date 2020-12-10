<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 11/10/17
 * Time: 16:07
 * PHP version 7
 */

namespace App\Controller;

use App\Model\FaqManager;

/**
 * Class FaqController
 *
 */
class FaqController extends AbstractController
{


    /**
     * Display faq listing
     *
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function index()
    {
        if (isset($_SESSION['admin']) && !empty($_SESSION['admin'])) {
            $faqManager = new FaqManager();
            $faq = $faqManager->selectAll();

            return $this->twig->render('Faq/index.html.twig', ['faq' => $faq]);
        } else {
            header('Location:/');
        }
    }


    /**
     * Display faq informations specified by $id
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
            $faqManager = new FaqManager();
            $faq = $faqManager->selectOneById($id);

            return $this->twig->render('Faq/show.html.twig', ['faq' => $faq]);
        } else {
            header('Location:/');
        }
    }


    /**
     * Display faq edition page specified by $id
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
            $faqManager = new FaqManager();
            $faq = $faqManager->selectOneById($id);

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $faq['question'] = $_POST['question'];
                $faq['answer'] = $_POST['answer'];
                $faqManager->update($faq);
                header('Location:/faq/show/' . $id);
            }

            return $this->twig->render('Faq/edit.html.twig', ['faq' => $faq]);
        } else {
            header('Location:/');
        }
    }


    /**
     * Display faq creation page
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
                $faqManager = new FaqManager();
                $faq = [
                    'question' => $_POST['question'],
                    'answer' => $_POST['answer'],
                ];
                $id = $faqManager->insert($faq);
                header('Location:/faq/show/' . $id);
            }

            return $this->twig->render('Faq/add.html.twig');
        } else {
            header('Location:/');
        }
    }


    /**
     * Handle faq deletion
     *
     * @param int $id
     */
    public function delete(int $id)
    {
        if (isset($_SESSION['admin']) && !empty($_SESSION['admin'])) {
            $faqManager = new FaqManager();
            $faqManager->delete($id);
            header('Location:/Faq/index');
        } else {
            header('Location:/');
        }
    }
}
