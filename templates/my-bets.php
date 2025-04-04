<?php require_once('./dataFormatting.php'); ?>
<?php require_once('./helpers.php'); ?>
<nav class="nav">
    <ul class="nav__list container">
        <?php foreach ($categoryList as $categoryId => $categoryName) { ?>
            <li class="nav__item">
                <a href="<?= "my-bets.php?category=" . $categoryId ?>"><?= htmlspecialchars($categoryName) ?></a>
            </li>
        <?php }
        ; ?>
    </ul>
</nav>
<section class="rates container">
    <h2>Мои ставки</h2>
    <table class="rates__list">
        <?php foreach ($myBetsList as $myBet) { ?>
            <tr class="rates__item 
            <?php
            $lot_expiration_date = $myBet['lot_expiration_date'] ?? '';
            $expireTime = get_expire_time(($myBet['lot_expiration_date'] ?? ''));
            if (new DateTime($lot_expiration_date) <= new DateTime()) {
                if ($myBet['winner_id'] === $_SESSION['id']) {
                    echo 'rates__item--win';
                } else {
                    echo 'rates__item--end';
                }
            } ?>">
                <td class="rates__info">
                    <div class="rates__img">
                        <img src="<?= $myBet['lot_image_path'] ?>" width="54" height="40">
                    </div>
                    <h3 class="rates__title"><a href="lot.php?id=<?= $myBet['lot_id'] ?>"><?= $myBet['lot_name'] ?></a></h3>
                    <?php if ($myBet['winner_id'] === $_SESSION['id']) { ?>
                        <p><?= $myBet['author_contact_info']; ?></p>
                    <?php }
                    ; ?>
                </td>
                <td class="rates__category">
                    <?= $myBet['lot_category_name'] ?>
                </td>
                <td class="rates__timer">
                    <?php
                    if (new DateTime($lot_expiration_date) > new DateTime()) { ?>
                        <div class="timer <?php if ($expireTime['hours'] < 1) {
                            echo "timer--finishing";
                        } ?>">
                            <?= sprintf("%02d:%02d", $expireTime['hours'], $expireTime['minutes']); ?>
                        </div>
                    <?php } elseif ($myBet['winner_id'] === $_SESSION['id']) { ?>
                        <div class="timer timer--win">Ставка выиграла</div>
                    <?php } else { ?>
                        <div class="timer timer--end">Торги окончены</div>
                    <?php }
                    ; ?>
                </td>
                <td class="rates__price">
                    <?= htmlspecialchars(get_value_in_money_type($myBet['price'])) ?>
                </td>
                <td class="rates__time">
                    <?= $myBet['date'] ?>
                    <?php
                    $date = new DateTime($myBet['date']);
                    $dateDiffString = get_date_diff_string(new DateTime($myBet['date']), new DateTime());
                    echo $dateDiffString;
                    ?>
                </td>
            </tr>
        <?php }
        ; ?>
    </table>
</section>