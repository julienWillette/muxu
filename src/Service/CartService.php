<?php

namespace App\Service;

use App\Model\ProductManager;
use App\Model\CommandManager;
use App\Model\InvoiceManager;
use App\Model\InvPdtManager;
use Stripe\Stripe;

class CartService
{
    public function add($product)
    {
        $productManager = new ProductManager();
        $productAdded = $productManager->selectOneById($product);
        
        if (!empty($_SESSION['cart'][$product])) {
            if ($_SESSION['cart'][$product] < $productAdded['quantity']) {
                $_SESSION['cart'][$product]++;
            } else {
                $_SESSION['flash_message_product'] = [
                    "Sorry, the product " . $productAdded['name'] ." is just soldout"];
            }
        } else {
            $_SESSION['cart'][$product] = 1;
        }
        $_SESSION['count'] = $this->countProduct();
        header("Location: http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
    }

    public function delete($product)
    {
        $cart = $_SESSION['cart'];
        if (!empty($cart[$product])) {
            unset($cart[$product]);
        }
        $_SESSION['cart'] = $cart;
        $_SESSION['count'] = $this->countProduct();
        header('Location:/home/cart');
    }

    public function cartInfos()
    {
        if (isset($_SESSION['cart'])) {
            $cart = $_SESSION['cart'];
            $cartInfos = [];
            $productManager = new ProductManager();
            foreach ($cart as $id => $quantity) {
                $infosProduct = $productManager->selectOneById($id);
                $infosProduct['quantity'] = $quantity;
                $cartInfos[] = $infosProduct;
            }
            return $cartInfos;
        }
        return false;
    }

    public function totalCart()
    {
        $total = 0;
        if ($this->cartInfos() != false) {
            foreach ($this->cartInfos() as $item) {
                $total += $item['price'] * $item['quantity'];
            }
            return $total;
        }
        return $total;
    }

    public function countProduct()
    {
        $total = 0;
        if ($this->cartInfos() != false) {
            foreach ($this->cartInfos() as $item) {
                $total += $item['quantity'];
            }
            return $total;
        }
        return $total;
    }

    public function payment($infos)
    {
        $stripe = \Stripe\Stripe::setApiKey(API_KEY);
        $commandManager = new CommandManager();
        $invoiceManager = new InvoiceManager();
        $invPdtManager = new InvPdtManager();
        $productManager = new ProductManager();
        

        $invoice = [
            'user_id' => $_SESSION['id'],
            'created_at' => date("Y-m-d")
        ];

        $invoiceId = $invoiceManager->insert($invoice);

        $command = [
            'invoice_id' => $invoiceId,
            'user_id' => $_SESSION['id'],
            'total' => $this->totalCart(),
            'shipping_id' => 1,
            'created_at' => date("Y-m-d")
        ];
        
        $commandManager->insert($command);

        foreach ($this->cartInfos() as $item) {
            $productId = $item['id'];
            $productQty = $item['quantity'];
            $productPrice = $item['price'];
            $invPdt = [
                'quantity' => $productQty,
                'price' => $productPrice,
                'product_id' => $productId,
                'invoice_id' => $invoiceId,
            ];
            $invPdtManager->insert($invPdt);
        }

        foreach ($this->cartInfos() as $item) {
            $productBought = [
                'id' => $item['id'],
                'quantity' => $item['quantity']
            ];
            $product = $productManager->selectOneById($productBought['id']);
            $updatedProduct = [
                'id' => $productBought['id'],
                'quantity' => $product['quantity'] - $productBought['quantity']
            ];
            $productManager->updateQty($updatedProduct);
        }
        try {
            //CUSTOMER
            $data = [
                'source' => $_POST['stripeToken'],
                'description' => $_POST['name'],
                'email' => $_POST['email']
            ];
            $customer = \Stripe\Customer::create($data);
    
            // CHARGE
            $charge = \Stripe\Charge::create([
                'amount' => $this->totalCart()*100,
                'currency' => 'eur',
                'description' => 'Example charge',
                'customer' => $customer->id,
                'statement_descriptor' => 'Custom descriptor',
            ]);
            $transacUrl = $charge->receipt_url;
            unset($_SESSION['cart']);
            unset($_SESSION['count']);
            $_SESSION['transaction'] = [
                'stripe' => $transacUrl
            ];
        } catch (\Stripe\Exception\ApiErrorException $e) {
            $e->getError();
        }
        unset($_SESSION['cart']);
        unset($_SESSION['count']);
        header('Location:/home/success');
    }
}
