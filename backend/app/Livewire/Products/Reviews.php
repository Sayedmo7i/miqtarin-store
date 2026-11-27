<?php

namespace App\Livewire\Products;

use App\Models\Product;
use App\Models\ProductRating;
use App\Models\User;
use Livewire\Component;

class Reviews extends Component
{
    public $product_id = "";
    public function mount($slug)
    {
        $product_id = fetchDetails(Product::class, ['slug' => $slug], 'id');
        $this->product_id = $product_id[0]->id;
    }

    public function render()
    {
        $reviews = fetchDetails(ProductRating::class, ['product_id' => $this->product_id]);
        foreach ($reviews as $key => $ratings) {
            $user_profile = fetchDetails(User::class, ['id' => $ratings->user_id], ['image', 'username']);
            $userRow = $user_profile[0] ?? null;
            $reviews[$key]->user_profile = !empty($userRow?->image) ? $userRow->image : 'demo-avatar.jpg';
            $reviews[$key]->user_name = !empty($userRow?->username) ? $userRow->username : 'Verified Customer';
        }
        return view('livewire.' . config('constants.theme') . '.products.reviews', [
            'customer_reviews' => $reviews
        ]);
    }


}
