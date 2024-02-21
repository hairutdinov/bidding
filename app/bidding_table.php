<?php
require_once "bootstrap.php";
require 'vendor/autoload.php';

use Doctrine\ORM\EntityManager;
use entities\Bidding;


/* @var $entityManager EntityManager */

$bidding_repository = $entityManager->getRepository(Bidding::class);
$biddings = $bidding_repository->findBy([], ['id' => 'DESC']);
?>


<table class="table table-bordered table-responsive">
    <thead>
    <tr>
        <th>Код торгов</th>
        <th>Предмет торгов</th>
        <th>email контактного лица</th>
        <th>Телефон контактного лица</th>
        <th>ИНН должника</th>
        <th>Номер дела о банкротстве</th>
        <th>Дата торгов</th>
        <th>Дата создания</th>
        <th>Дата обновления</th>
    </tr>
    </thead>
    <tbody>
    <?php
    /* @var $bidding Bidding */
    foreach ($biddings as $bidding):
        ?>
        <tr>
            <td><a href="<?= $bidding->getUrl() ?>" target="_blank"><?= $bidding->getTradeNumber() ?></a></td>
            <td><?= $bidding->getBenefitInformation() ?></td>
            <td><?= $bidding->getContactPersonEmail() ?></td>
            <td><?= $bidding->getContactPersonPhoneNumber() ?></td>
            <td><?= $bidding->getDebtorInn() ?></td>
            <td><?= $bidding->getBankruptcyCaseNumber() ?></td>
            <td><?= $bidding->getDateOfBidding() ?></td>
            <td><?= $bidding->getCreatedAt()->format("m.d.Y H:i:s") ?></td>
            <td><?= $bidding->getUpdatedAt()->format("m.d.Y H:i:s") ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>