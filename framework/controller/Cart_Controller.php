<?php

namespace maw\controller;

use \maw\core\SessionHandler;

class Cart_Controller extends Controller
{
    private $status = null;

    public function forTemplate($skeleton)
    {
        $tplTags = [
            '{{STATUS}}',
            '{{CART}}',
            '{{SHOWPAYMENT}}',
            '{{TOTAL}}',
            '{{SHIPPING}}'
        ];
        $replaceTags = [
            $this->getStatusHTML(),
            $this->renderCart(),
            self::countCurrentObjects() > 0 ? 'show' : 'hide',
            number_format(self::getTotal(), 2, ",", "."),
            number_format(self::getShipping(), 2, ",", "."),
        ];
        return str_replace($tplTags, $replaceTags, $skeleton);
    }

    public function add($productID)
    {
        if (isset($_POST['amount'])) {
            $product = Product_Controller::getProduct($productID);
            if (!$product)
                $this->status = 1;

            $cart = self::getCurrentCart();

            if (isset($cart[$productID]) && $product->amount < ($cart[$productID] + $_POST['amount']) || $product->amount < $_POST['amount'])
                $this->status = 2;
            else {
                $cart[$productID] = isset($cart[$productID]) ? $cart[$productID] + $_POST['amount'] : $_POST['amount'];
                SessionHandler::setMAWSession('cart', $cart);
                $this->status = 3;
            }
        }
        $this->render();
    }

    public function update($productID)
    {
        $product = Product_Controller::getProduct($productID);
        if (!$product)
            $this->status = 1;
        elseif ($product->amount < $_POST['amount'])
            $this->status = 2;
        else {
            $cart = self::getCurrentCart();
            $cart[$productID] = $_POST['amount'];
            SessionHandler::setMAWSession('cart', $cart);
            $this->status = 4;
        }
        $this->render();
    }

    public function delete($productID)
    {
        $cart = self::getCurrentCart();
        unset($cart[$productID]);
        SessionHandler::setMAWSession('cart', $cart);
        $this->status = 5;
        $this->render();
    }

    public function buy() {
        $to = $_POST['email'];
        $subject = "Bestellbestätigung";
        $headers = "From: WW-Shop <bestellbestaetigung@ww-shop.de>" . "\r\n";
        $headers .= "X-Mailer: PHP/".phpversion();

        $messageCustomer = "Vielen Dank für Ihre Bestellung bei WW-Shop.\n\n";
        $messageCustomer .= "Zusammenfassung Ihrer Bestellung:\n";

        $messageAdmin = "Neue Bestellung im Onlineshop\n\n";
        $messageAdmin .= "Zusammenfassung der Bestellung\n";

        $message = "";
        $cart = self::getCurrentCart();
        foreach ($cart as $key => $value) {
            $product = Product_Controller::getProduct($key);
            $message .= $value . "x $product->title " .  $product->price . "€\n";
        }
        $message .= "\nVersandkosten: " . self::getShipping() . "€\n";
        $message .= "Gesamtkosten (inkl. 19% MwSt.): " . self::getTotal() . "€\n";

        mail($to, $subject, $messageCustomer . $message, $headers);

        $to = "s4miwolz@uni-trier.de,s4aawinz@uni-trier.de";
        $subject = "Neue Bestellung";
        $headers = "From: WW-Shop <bestellung@ww-shop.de>" . "\r\n";
        $headers .= "X-Mailer: PHP/".phpversion();

        $messageAdmin2 = "\n\nDaten:\n";
        $messageAdmin2 .= "Name: " . $_POST['name'] . " " . $_POST['surname'] . "\n";
        $messageAdmin2 .= "E-Mail: " . $_POST['email'] . "\n";
        $messageAdmin2 .= "Straße: " . $_POST['street'] . "\n";
        $messageAdmin2 .= "PLZ: " . $_POST['plz'] . "\n";
        $messageAdmin2 .= "Stadt: " . $_POST['city'] . "\n";
        $messageAdmin2 .= "Anmerkungen: " . $_POST['messageAdmin2'] . "\n";
        $messageAdmin2 .= "Kontoinhaber: " . $_POST['owner'] . "\n";
        $messageAdmin2 .= "Bank: " . $_POST['bank'] . "\n";
        $messageAdmin2 .= "IBAN: " . $_POST['iban'] . "\n";
        $messageAdmin2 .= "BIC: " . $_POST['bic'] . "\n";

        mail($to, $subject, $messageAdmin . $message . $messageAdmin2, $headers);
        SessionHandler::destroyMAWSession();
        $this->status = 6;
        $this->render();
    }

    private function getStatusHTML()
    {
        if ($this->status) {
            $html = '<div id="status" class="';
            switch ($this->status) {
                case 1: {
                    $html .= 'error"> ERROR: Product not found!';
                    break;
                }
                case 2: {
                    $html .= 'warning"> Derzeit ist das Produkt leider nicht in der gewünschten Menge verfügbar!';
                    break;
                }
                case 3: {
                    $html .= 'success"> Das Produkt wurde erfolgreich Ihrem Warenkorb hinzugefügt!';
                    break;
                }
                case 4: {
                    $html .= 'success"> Die Menge wurde erfolgreich aktualisiert!';
                    break;
                }
                case 5: {
                    $html .= 'warning"> Das Produkt wurde erfolgreich aus Ihrem Warenkorb entfernt!';
                    break;
                }
                case 6: {
                    $html .= 'success"> Vielen Dank für Ihre Bestellung bei WW-Shop. Sie erhalten in Kürze eine Bestellbestätigung per Mail.';
                    break;
                }
            }
            $html .= '</div>';
            return $html;
        } else
            return "";
    }

    private function renderCart()
    {
        $tpl = file_get_contents('theme/templates/objects/cartitem.maw');
        $tplTags = [
            '{{ID}}',
            '{{COVER}}',
            '{{TITLE}}',
            '{{PRICE}}',
            '{{AMOUNT}}',
            '{{MAXAMOUNT}}'
        ];

        if (!$tpl) return null;

        if (Cart_Controller::countCurrentObjects() > 0) {
            $cart = Cart_Controller::getCurrentCart();

            $html = '<table id="carttable"><tr><th class="title" colspan="2">Titel</th><th class="price">Preis</th><th class="amount">Menge</th></tr>';
            foreach ($cart as $key => $value) {
                $product = Product_Controller::getProduct($key);
                $replaceTags = [
                    $product->id,
                    BASEURL . $product->cover,
                    $product->title,
                    number_format($product->price, 2, ",", "."),
                    $value,
                    $product->amount
                ];

                $html .= str_replace($tplTags, $replaceTags, $tpl);
            }
            $html .= '</table>';
        } elseif ($this->status == 6)
            $html = '';
        else
            $html = '<strong>Derzeit befinden sich keine Produkte in Ihrem Warenkorb.</strong>';
        return $html;
    }

    private static function getCurrentCart()
    {
        return SessionHandler::getMAWSession('cart');
    }

    public static function getTotalProducts()
    {
        $cart = self::getCurrentCart();
        $total = 0;
        if ($cart)
            foreach ($cart as $key => $value) {
                $product = Product_Controller::getProduct($key);
                $total += $product->price * $value;
            }
        return $total;
    }

    public static function getTotal()
    {
        return self::getShipping() + self::getTotalProducts();
    }

    public static function getShipping()
    {
        if (self::getTotalProducts() > 20)
            $shipping = 0;
        else
            $shipping = 2 + 0.5 * (self::countCurrentObjects() - 1);
        return $shipping;
    }

    public static function countCurrentObjects()
    {
        $cart = self::getCurrentCart();
        $count = 0;
        if ($cart)
            foreach ($cart as $el)
                $count += $el;
        return $count;
    }
}