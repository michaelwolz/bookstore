<?php

namespace maw\controller;

use maw\core\DataProcessor;

class Product_Controller extends Controller
{
    private $productID;

    public function forTemplate($skeleton)
    {
        return str_replace("{{Bookdetails}}", self::productDetails($this->productID), $skeleton);
    }

    public function item($id = null)
    {
        $this->productID = $id;
        $this->render();
    }

    public static function productDetails($id)
    {
        $tpl = file_get_contents('theme/templates/objects/product.maw');
        $tplTags = [
            '{{ID}}',
            '{{COVER}}',
            '{{TITLE}}',
            '{{SUMMARY}}',
            '{{DESCRIPTION}}',
            '{{AUTHOR}}',
            '{{CATEGORY}}',
            '{{PRICE}}',
            '{{MAXAMOUNT}}'
        ];

        if (!$tpl) return null;

        $product = self::getProduct($id);

        if (!$product)
            return self::NotFound();

        $replaceTags = [
            $product->id,
            BASEURL . $product->cover,
            $product->title,
            $product->summary,
            $product->description,
            $product->author,
            $product->category,
            number_format($product->price, 2, ",", "."),
            $product->amount
        ];

        $html = str_replace($tplTags, $replaceTags, $tpl);
        return $html;
    }

    public static function NotFound()
    {
        return "<h1>Product not found</h1>";
    }

    public static function getProduct($id)
    {
        $products = DataProcessor::getJSONAsArray(PRODUCT_FILE);
        $product = null;
        $found = false;
        foreach ($products as $product)
            if ($product->id == $id) {
                $found = true;
                break;
            }
        return $found ? $product : null;
    }
}