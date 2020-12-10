<?php

namespace App\Service;

use App\Model\WishlistManager;

class LikedService
{
    public function like(int $id)
    {
        $wishlistManager = new WishlistManager();
        $isLiked = $wishlistManager->isLikedByUser($id, $_SESSION['id']);
        if (!$isLiked) {
            $wish = [
                'user_id' => $_SESSION['id'],
                'product_id' => $id
            ];
            $wishlistManager->insert($wish);
        }
    }
    
    public function dislike(int $id)
    {
        $wishlistManager = new WishlistManager();
        $wishlistManager->delete($id, $_SESSION['id']);
    }
}
