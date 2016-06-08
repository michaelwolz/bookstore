<?php

namespace maw\controller;

use maw\core\DataProcessor;

class ProductHolder_Controller extends Controller
{
    public function forTemplate($skeleton) {
        return str_replace("{{Books}}", ProductHolder_Controller::productList(), $skeleton);
    }

    public static function productList()
    {
        $tpl = file_get_contents('theme/templates/objects/productListItem.maw');
        $tplTags = [
            '{{ID}}',
            '{{COVER}}',
            '{{TITLE}}',
            '{{SUMMARY}}',
            '{{AUTHOR}}',
            '{{CATEGORY}}',
            '{{PRICE}}',
            '{{MAXAMOUNT}}',
            '{{LINK}}'
        ];

        if (!$tpl) return null;

        $products = DataProcessor::getJSONAsArray(PRODUCT_FILE);
        $html = '';

        foreach ($products as $product) {
            $replaceTags = [
                $product->id ,
                BASEURL . $product->cover,
                $product->title,
                $product->summary,
                $product->author,
                $product->category,
                number_format($product->price, 2, ",", "."),
                $product->amount,
                'book/item/' . $product->id
            ];

            $html .= str_replace($tplTags, $replaceTags, $tpl);
        }
        return $html;
    }
}