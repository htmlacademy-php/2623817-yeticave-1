<?php require_once('./dataFormatting.php');?>

<section class="lot-item container">
    <h2><?= htmlspecialchars($item['lot_name'])?></h2>
    <div class="lot-item__content">
        <div class="lot-item__left">
            <div class="lot-item__image">
                <img src="<?= $item['lot_image_path']?>" width="730" height="548" alt="Сноуборд">
            </div>
            <p class="lot-item__category">Категория: <span><?= $item['category'] ?? 'Прочее'?></span></p>
            <p class="lot-item__description"><?= htmlspecialchars($item['lot_description'])?></p>
        </div>
        <div class="lot-item__right">
            <div class="lot-item__state">
                <?php $expireTime = get_expire_time(($item['lot_expiration_date']??''));?>
                <div class="lot-item__timer timer <?php if($expireTime['hours'] < 1){ echo "timer--finishing";}?>">
                    <?= sprintf("%02d:%02d",$expireTime['hours'],$expireTime['minutes']);?>
                </div>
                <div class="lot-item__cost-state">
                    <div class="lot-item__rate">
                        <span class="lot-item__amount">Текущая цена</span>
                        <span class="lot-item__cost"><?=htmlspecialchars(get_value_in_money_type($item['price'])) ?></span>
                    </div>
                    <div class="lot-item__min-cost">
                        Мин. ставка <span><?=htmlspecialchars(get_value_in_money_type($item['lot_start_price'])) ?></span>
                    </div>
                </div>
                
            </div>
            
        </div>
    </div>
</section>