<?php

namespace App\Controller;

use App\Model\CommandManager;

/**
 * Class CommandController
 *
 */
class CommandController extends AbstractController
{


    /**
     * Display command listing
     *
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function index()
    {
        if (isset($_SESSION['admin']) && !empty($_SESSION['admin'])) {
            $commandManager = new CommandManager();
            $commands = $commandManager->selectAll();
            
            return $this->twig->render('Command/index.html.twig', ['commands' => $commands]);
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
            $commandManager = new CommandManager();
            $command = $commandManager->selectOneById($id);
            $invProducts = $commandManager->selectAllProductsByCommand();
            
            $detailsCommand = [];
            foreach ($invProducts as $invProduct) {
                if ($invProduct['invoice_id'] == $command['invoice_id']) {
                    array_push($detailsCommand, $invProduct);
                }
            }

            return $this->twig->render('Command/show.html.twig', ['command' => $command,
            'detailsCommand' => $detailsCommand]);
        } else {
            header('Location:/');
        }
    }
}
