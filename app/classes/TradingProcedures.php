<?php

namespace classes;

use DateTime;
use Doctrine\ORM\EntityManager;
use DOMNode;
use entities\Bidding;
use entities\Lots;
use Symfony\Component\DomCrawler\Crawler;
use Doctrine\ORM\NoResultException;


class TradingProcedures
{
    public array $trades = [];
    public $trade_number;
    public $lot_number;
    const TRADES_DATA_TBODY_CSS_SELECTOR = <<<EOD
#art-main
> div.art-sheet > div
> div.art-content-layout > div
> div.art-layout-cell.art-content
> div > div > table > tbody
EOD;

    public function parse_trades(string $html): void {
        $crawler = new Crawler($html);
        $tbody = $crawler->filter(self::TRADES_DATA_TBODY_CSS_SELECTOR);

        /* @var $trade_tr_element DOMNode */
        foreach ($tbody->children() as $trade_tr_element) {
            if ($trade_tr_element->firstChild->textContent == "Не найдено торгов по заданным условиям") {
                break;
            }

            $trade = new Trade($trade_tr_element);
            $trade->parse_tr();
            $trade->parse_additional_loaded_info();
            $this->trades[] = $trade;
        }
    }

    public function save()
    {
        /* @var $model Bidding */
        /* @var $entityManager EntityManager */
        /* @var $trade Trade */

        require __DIR__ . "/../bootstrap.php";

        foreach ($this->trades as $trade) {
            $qb = $entityManager->createQueryBuilder();

            $qb->select('b')
                ->from('entities\Bidding', 'b')
                ->where('b.trade_number = :tradeNumber')
                ->setParameter('tradeNumber', $trade->bidding_code);

            try {
                $model = $qb->getQuery()->getSingleResult();
                $model->getLots()->forAll(function ($key, $entity) use ($entityManager) {
                    $entityManager->remove($entity);
                    return true;
                });
            } catch (NoResultException $e) {
                $model = new Bidding();
            }


            $model->setTradeNumber($trade->bidding_code)
                ->setUrl($trade->url)
                ->setBenefitInformation(implode(PHP_EOL, array_column($trade->subjects_of_bidding, "name")))
                ->setContactPersonEmail($trade->email)
                ->setContactPersonPhoneNumber($trade->phone)
                ->setDebtorInn($trade->INN)
                ->setBankruptcyCaseNumber($trade->bankruptcy_number)
                ->setDateOfBidding(DateTime::createFromFormat('d.m.Y H:i', $trade->start_of_acceptance));

            foreach ($trade->subjects_of_bidding as $item) {
                $lot_model = new Lots();
                $lot_model->setLotNumber($item["lot_number"])
                    ->setName($item["name"])
                    ->setStartingPrice($item["starting_price"])
                    ->setBidding($model);
                $model->addLot($lot_model);
                $entityManager->persist($lot_model);
            }

            $entityManager->persist($model);
        }

        $entityManager->flush();
    }
}