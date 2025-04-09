<?php require_once('./dataFormatting.php'); ?>

<section class="lot-item container">
    <h2><?= htmlspecialchars($item['lot_name']) ?></h2>
    <div class="lot-item__content">
        <div class="lot-item__left">
            <div class="lot-item__image">
                <img src="<?= $item['lot_image_path'] ?>" width="730" height="548" alt="Сноуборд">
            </div>
            <p class="lot-item__category">Категория: <span><?= $item['category'] ?? 'Прочее' ?></span></p>
            <p class="lot-item__description"><?= htmlspecialchars($item['lot_description']) ?></p>
        </div>
        <div class="lot-item__right">
            <?php 
            $lastBetUserId = count($betsArray) > 0 ? $betsArray[0]['user_id'] : '' ;
            
            if (
                $isAuth && // пользователь авторизован
                $_SESSION['id'] <> $item['lot_author_id'] && // Это лот другого пользователя
                strtotime($item['lot_expiration_date']) > strtotime('now') &&// срок лота не вышел
                $lastBetUserId <> $_SESSION['id'] // пользователь отличается от последнего сделавшего ставку
            ) { ?>
                <div class="lot-item__state">
                    <?php $expireTime = get_expire_time(($item['lot_expiration_date'] ?? '')); ?>
                    <div class="lot-item__timer timer <?php if ($expireTime['hours'] < 1) {
                        echo "timer--finishing";
                    } ?>">
                        <?= sprintf("%02d:%02d", $expireTime['hours'], $expireTime['minutes']); ?>
                    </div>
                    <div class="lot-item__cost-state">
                        <div class="lot-item__rate">
                            <span class="lot-item__amount">Текущая цена</span>
                            <span
                                class="lot-item__cost"><?= htmlspecialchars(get_value_in_money_type($item['price'])) ?></span>
                        </div>
                        <div class="lot-item__min-cost">
                            Мин. ставка
                            <span><?= htmlspecialchars(get_value_in_money_type($item['min_bet'])) ?></span>
                        </div>
                    </div>
                        <form class="lot-item__form" action="<?= $requestUri ?>" method="post" autocomplete="off">
                            <p
                                class="lot-item__form-item form__item <?= $errors['cost']['IsError'] ?? false ? 'form__item--invalid' : '' ?>">
                                <label for="cost">Ваша ставка</label>
                                <input id="cost" type="text" name="cost" placeholder="<?= $item['min_bet'] ?>"
                                    value="<?= $formData['cost'] ?? ""; ?>">
                                <span
                                    class="form__error"><?= $errors['cost']['IsError'] ?? false ? $errors['cost']['errorDescription'] : '' ?></span>
                            </p>
                            <button type="submit" class="button">Сделать ставку</button>
                        </form>
                </div>
            <?php } ?>
            <div class="history">
            <h3>История ставок (<span><?= count($betsArray)?></span>)</h3>
            <table class="history__list">
                <?php foreach($betsArray as $betinfo){?>
              <tr class="history__item">
                <td class="history__name"><?= $betinfo['user_name']?></td>
                <td class="history__price"><?= get_value_in_money_type($betinfo['price']) ?></td>
                <td class="history__time"><?= get_date_diff_string($betinfo['date'], 'now') ?></td>
              </tr>
              <?php }; ?>
            </table>
        </div>
    </div>
</section>