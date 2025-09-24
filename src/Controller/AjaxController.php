<?php
declare(strict_types=1);

namespace App\Controller;

use App\Controller\AppController;
use Cake\View\JsonView;

class AjaxController extends AppController
{
    public function initialize(): void
    {
        parent::initialize();

        $this->Authentication->allowUnauthenticated(['addToCart','getCart','removeFromCart']);
    }


    public function viewClasses(): array
    {
        return [JsonView::class];
    }


    /*
     * removeFromCart
     * Paramètre : product id to remove from the cart
    */
    public function removeFromCart($idProduct)
    {
        try {
            // verify if the parameter provided is correct
            if (is_numeric($idProduct)) {
                $currentQuantity = $this->request->getSession()->read("Cart." . (string)$idProduct);
    
                if ($currentQuantity !== null) {
                    // Remove from session
                    $this->request->getSession()->delete("Cart." . (string)$idProduct);
                    $response = ["success" => true, 'cart' => $this->getCart()];
                } else {
                    // product not found
                    $response = ["success" => false, "message" => "Product not found"];
                }
            } else {
                $response = ["success" => false, "message" => "Data not valid"];
            }
        } catch (\Exception $ex) {
            $response = ["success" => false, "message" => "Error occured"];
        }
    
        return $this->response->withType("application/json")
            ->withStringBody(json_encode(["response" => $response]));
    }
    
    
    
    
    
    /*
     * addToCart
     * Paramètre : 
     *  idProduct   => id of the product to add
     *  quantity    =>  quantity of the product the user want
    */
    public function addToCart($idProduct, $quantity)
    { 
        try {
            // test parameter
            if (is_numeric($idProduct) && is_numeric($quantity) && $quantity > 0) {
                $currentquantity = $this->request->getSession()->read("Cart." . (string)$idProduct);
                
                if ($currentquantity !== null) {
                    // verify if not exist in the cart so it accumulate the quantity
                    $newquantity = (int)$currentquantity + (int)$quantity;
                    $this->request->getSession()->write("Cart." . (string)$idProduct, (string)$newquantity);
                } else {
                    $this->request->getSession()->write("Cart." . (string)$idProduct, (string)$quantity);
                }
                $response = ["success" => true , "cart" => $this->getCart()];
            } else {
                $response = ["success" => false , "Message" => "Invalid parameters"];
            }
        } catch (\Exception $e) {
            $response = ["success" => false,"Error occured"];
        }
    
        return $this->response->withType("application/json")
            ->withStringBody(json_encode(["response" => $response]));
    }




    
    /*
     * getCart
     * Return the content of the cart in the session (product && quantity)
     */
    public function getCart()
    {
        $sessionData = $this->request->getSession()->read();
        $response = [];
        
        if (isset($sessionData["Cart"]))
        {
            foreach($sessionData["Cart"] as $key => $quantity)
            {
                // get && format object
                $product = $this->fetchTable('Products')->get($key, [
                    'contain' => ['Categories']
                ]);
                $item = [
                    "idProduct" => $product->id,
                    "name" => $product->name,
                    "category" => $product->category,
                    "price" => $product->price,
                    "created_at" => $product->created_at,
                    "quantity" => $quantity
                ];
                $response[] = $item;
            }
    
            return $this->response->withType("application/json")
                ->withStringBody(json_encode(["response" => $response]));
        }
        else
        {
            return $this->response->withType("application/json")
                ->withStringBody(json_encode(["response" => "Panier vide"]));
        }
    }       
}