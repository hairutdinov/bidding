<form method="POST" action="index.php" class="mb-4">
    <div class="form-group">
        <label for="trade_number">Номер торгов</label>
        <input type="text" class="form-control" id="trade_number" name="trade_number"
               placeholder="Введите номер торгов (например, 31710-ОТПП)"
               value="<?= $_POST["trade_number"] ?? '' ?>"
        >
    </div>
    <div class="form-group">
        <label for="lot_number">Номер лота</label>
        <input type="number" class="form-control" id="lot_number" name="lot_number"
               placeholder="Введите номер лота (1-9999)"
               value="<?= $_POST["lot_number"] ?? '' ?>"
        >
    </div>
    <button type="submit" name="submit_btn" class="btn btn-primary">Найти</button>
</form>

<?php
require 'vendor/autoload.php';
require_once "bootstrap.php";

use classes\TradingProcedures;
use Doctrine\ORM\EntityManager;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

/* @var $entityManager EntityManager */

if (isset($_POST["trade_number"]) or isset($_POST["lot_number"])) {
    $client = new Client([
        'curl' => [
            CURLOPT_SSL_VERIFYPEER => false
        ]
    ]);

    try {
        $response = $client->request('GET', 'https://nistp.ru', [
            'query' => [
                "trade_number" => $_POST["trade_number"] ?? '',
                "lot_number" => $_POST["lot_number"] ?? '',
            ]
        ]);

        $html = $response->getBody()->getContents();

        $trading = new TradingProcedures();
        $trading->trade_number = $_POST["trade_number"] ?? '';
        $trading->lot_number = $_POST["lot_number"] ?? '';
        $trading->parse_trades($html);

        if (count($trading->trades) == 0) {
            echo "<div class='alert alert-danger'>Лот не найден</div>";
        } else {
            $trading->save();
        }
    } catch (RequestException $e) {
        echo 'Ошибка при выполнении запроса: ' . $e->getMessage();
    }
}
?>