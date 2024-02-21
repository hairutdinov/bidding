<?php

namespace classes;

use DOMNode;
use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;

class Trade
{
    private $trade_tr_node;
    public string $bidding_code; // Код торгов
    public string $organizer; // Организатор
    public string $debtor; // Должник
    public array $subjects_of_bidding; // предметы торгов
    public string $status; // Состояние
    public string $start_of_acceptance; // Начало приема заявок
    public string $end_of_acceptance; // Конец приема заявок

    public $url;

    public $email;
    public $phone;
    public $INN;
    public $bankruptcy_number;

    public function __construct(DOMNode $trade_tr_node)
    {
        $this->trade_tr_node= $trade_tr_node;
    }

    public function parse_tr() {
        $this->bidding_code = $this->trade_tr_node->firstChild->textContent;
        $this->organizer = $this->trade_tr_node->childNodes[2]->textContent;
        $this->parse_debtor_and_subjects($this->trade_tr_node->childNodes[3]);
        $this->status = $this->trade_tr_node->childNodes[5]->textContent;
        $this->start_of_acceptance = $this->trade_tr_node->childNodes[7]->textContent;
        $this->end_of_acceptance = $this->trade_tr_node->childNodes[9]->textContent;
    }

    public function parse_additional_loaded_info()
    {
        $client = new Client([
            'curl' => [
                CURLOPT_SSL_VERIFYPEER => false
            ]
        ]);

        $response = $client->get($this->url);
        $content = $response->getBody()->getContents();
        $crawler = new Crawler($content);

        $organizer_info = $crawler->filter('#art-main > div.art-sheet > div > div.art-content-layout > div > div.art-layout-cell.art-content > div > div > table:nth-child(3)');

//        $this->email = $organizer_info->filter('tbody > tr:nth-child(2) > td:nth-child(2)')->html();
        if (preg_match('/E-mail\s(\S+@\S+\.\S+)/', $organizer_info->text(), $email_match)) {
            $this->email = $email_match[1];
        }

//        $this->phone = $organizer_info->filter('tbody > tr:nth-child(3) > td:nth-child(2)')->html();
        if (preg_match('/Телефон\s(((8|\+7)[\- ]?)?(\(?\d{3}\)?[\- ]?)?[\d\- ]{7,10})/', $organizer_info->text(), $phone_match)) {
            $this->phone = $phone_match[1];
        }

        $debtor_information = $crawler->filter('#art-main > div.art-sheet > div > div.art-content-layout > div > div.art-layout-cell.art-content > div > div > table:nth-child(5)');

//        $this->INN = $debtor_information->filter('tr:nth-child(6) > td:nth-child(2)')->html();
        if (preg_match('/ИНН\s?(\d{10,12})/', $debtor_information->text(), $inn_match)) {
            $this->INN = $inn_match[1];
        }

//        $this->bankruptcy_number = $debtor_information->filter('tr:nth-child(10) > td:nth-child(2)')->html();
        if (preg_match('/Номер дела о банкротстве\s*(?:№)?\s*([А-Яа-я\w]+\d+\-\d+(?:\/\d+)*)/', $debtor_information->text(), $bankruptcy_match)) {
            $this->bankruptcy_number = $bankruptcy_match[1];
        }

        foreach ($this->subjects_of_bidding as $index => $item) {
            $table_lot_num = $index + 1;
            $lot_description_table = $crawler->filter('#table_lot_' . $table_lot_num);
            if (preg_match('/Начальная цена(?::)?(\d+(?:\.\d*))/', $lot_description_table->text(), $price_match)) {
                $this->subjects_of_bidding[$index]["starting_price"] = (float)$price_match[1];
            }
        }
    }

    public function parse_debtor_and_subjects(DOMNode $node){
        $this->debtor = $node->firstChild->textContent;
        $this->url = $node->childNodes[2]->attributes['href']->value;
        $trades_count_pattern = '/Всего лотов: (\d+)/';
        if (preg_match($trades_count_pattern, $node->childNodes[2]->textContent, $match) === false) {
            return;
        }

        $trades_count = (int) $match[1];
        for ($n = 1; $n <= $trades_count; ++$n) {
            $offset = ($n - 1) * 3;
            $trade_subject_text_node = $node->childNodes[$offset + 5];
            $this->subjects_of_bidding[] = [
                "lot_number" => $n,
                "name" => $trade_subject_text_node->textContent,
            ];
        }
    }
}