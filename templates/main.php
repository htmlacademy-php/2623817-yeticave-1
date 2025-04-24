
<?php require_once('./dataFormatting.php');?>

<section class="promo">
        <h2 class="promo__title">Нужен стафф для катки?</h2>
        <p class="promo__text">На нашем интернет-аукционе ты найдёшь самое эксклюзивное сноубордическое и горнолыжное снаряжение.</p>
        <ul class="promo__list">
            <!--заполните этот список из массива категорий-->
            <?php foreach ($categoryList as $categoryId => $categoryName) {?>
            <li class="promo__item promo__item--<?=$categoryId //Считаем, что ИД пользователь не вводит?>">
                <a class="promo__link" href="<?="index.php?category=" . $categoryId?>">
                    <?= htmlspecialchars($categoryName)?>
                </a>
            </li>
            <?php }?>
        </ul>
    </section>
    <section class="lots">
        <div class="lots__header">
            <h2>Открытые лоты</h2>
        </div>
        <ul class="lots__list">
            <!--заполните этот список из массива с товарами-->
            <?php foreach ($itemList as $item) { ?>
                <li class="lots__item lot">
                    <div class="lot__image">
                        <img src="<?=$item['image_path']//считаем, что названия картинок генерируются системой ?>" width="350" height="260" alt="">
                    </div>
                    <div class="lot__info">
                        <span class="lot__category">
                            <?= (htmlspecialchars($categoryList[$item['category_label']] ?? 'Прочее')) ?>
                        </span>
                        <h3 class="lot__title">
                            <a class="text-link" href=
                                <?="lot.php?id={$item['lot_id']}"?>><?=htmlspecialchars($item['lot_name']) ?>
                            </a>
                        </h3>
                        <div class="lot__state">
                            <div class="lot__rate">
                                <span class="lot__amount"><?= htmlspecialchars($item['start_price']) ?></span>
                                <span class="lot__cost">
                                    <?= htmlspecialchars(get_value_in_money_type($item['price'])) ?>
                                </span>
                            </div>
                            <?php $expireTime = get_expire_time(($item['expiration_date'] ?? ''));?>
                            <div class="lot__timer timer
                                <?php if ($expireTime['hours'] < 1) {
                                    echo "timer--finishing";
                                }?>">
                                <?= sprintf("%02d:%02d", $expireTime['hours'], $expireTime['minutes'])?>
                            </div>
                        </div>
                    </div>
                </li>
            <?php } ?>
        </ul>
    </section>
    <?php if ($numberOfPages > 1) { ?>
        <ul class="pagination-list">
            <?php if ($currentPage <> 1) { ?>
                <li class="pagination-item pagination-item-prev"><a
                        href="<?= get_new_url(['page' => $currentPage - 1]) ?>">Назад</a></li>
            <?php } ?>
            <?php for ($pageIndex = 1; $pageIndex <= $numberOfPages; $pageIndex++) { ?>
                <li class="pagination-item <?= $pageIndex === $currentPage ? 'pagination-item-active' : '' ?>"><a
                        href="<?= get_new_url(['page' => $pageIndex]) ?>"><?= $pageIndex ?></a></li>
            <?php } ?>
            <?php if ($currentPage <> $numberOfPages) { ?>
                <li class="pagination-item pagination-item-next"><a
                        href="<?= get_new_url(['page' => $currentPage + 1]) ?>">Вперед</a></li>
            <?php } ?>
        </ul>
    <?php } ?>
