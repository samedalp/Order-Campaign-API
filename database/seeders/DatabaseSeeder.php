<?php

namespace Database\Seeders;

use App\Models\Author;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $authors = $this->parseJson('seeders/JsonDatas/authors.json');
        $categories = $this->parseJson('seeders/JsonDatas/categories.json');
        $products = $this->parseJson('seeders/JsonDatas/products.json');

        foreach ($authors as $author) {
            Author::create([
                'id' => $author['author_id'],
                'name' => $author['author_name'],
            ]);
        }

        foreach ($categories as $category) {
            Category::create([
                'id' => $category['category_id'],
                'name' => $category['category_title'],
            ]);
        }

        foreach ($products as $product) {
            Product::create([
                'id' => $product['product_id'],
                'name' => $product['title'],
                'price' => $product['list_price'],
                'stock' => $product['stock_quantity'],
                'category_id' => $product['category_id'],
                'author_id' => $product['author_id'],
                'is_active' => true,
            ]);
        }
    }


    private function parseJson(string $path): array
    {
        try {
            $result = json_decode(
                File::get(database_path($path)),
                true
            );

        } catch (\Throwable) {
            Log::error('error while parsing JSON file ' . $path);
            $result = [];
        }

        return $result;
    }
}
