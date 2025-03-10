<form class="form form--add-lot container <?= ($formError)?'form--invalid':''?>" action="/add.php" method="post"> <!-- form--invalid -->
      <h2>Добавление лота</h2>
      <div class="form__container-two">
        <div class="form__item <?= $errors['lot-name']['IsError'] ?? false ?'form__item--invalid':''?>"> <!-- form__item--invalid -->
          <label for="lot-name">Наименование <sup>*</sup></label>
          <input id="lot-name" type="text" name="lot-name" placeholder="Введите наименование лота" value = "<?= $formData['lot-name'] ?? "";?>">
          <span class="form__error"><?= $errors['lot-name']['IsError'] ?? false ?$errors['lot-name']['errorDescription']:''?></span>
        </div>
        <div class="form__item <?= $errors['category']['IsError'] ?? false ?'form__item--invalid':''?>">
          <label for="category">Категория <sup>*</sup></label>
          <select id="category" name="category">
            <option value>Выберите категорию</option>
            <?php foreach($categoryList as $categoryId => $categoryName){?>
            <option <?= ($formData['category'] ?? "") === $categoryId ? 'selected':''?> value="<?=$categoryId?>"><?=$categoryName?></option>
            <?php };?>
          </select>
          <span class="form__error"><?= $errors['category']['IsError'] ?? false ?$errors['category']['errorDescription']:''?></span>
        </div>
      </div>
      <div class="form__item form__item--wide <?= $errors['message']['IsError'] ?? false ?'form__item--invalid':''?>">
        <label for="message">Описание <sup>*</sup></label>
        <textarea id="message" name="message" placeholder="Напишите описание лота"><?= $formData['message'] ?? "";?></textarea>
        <span class="form__error"><?= $errors['message']['IsError'] ?? false ?$errors['message']['errorDescription']:''?></span>
      </div>
      <div class="form__item form__item--file">
        <label>Изображение <sup>*</sup></label>
        <div class="form__input-file">
          <input class="visually-hidden" type="file" id="lot-img" value="">
          <label for="lot-img">
            Добавить
          </label>
        </div>
      </div>
      <div class="form__container-three">
        <div class="form__item form__item--small <?= $errors['lot-rate']['IsError'] ?? false ?'form__item--invalid':''?>">
          <label for="lot-rate">Начальная цена <sup>*</sup></label>
          <input id="lot-rate" type="text" name="lot-rate" placeholder="0" value = "<?= $formData['lot-rate'] ?? "";?>">
          <span class="form__error"><?= $errors['lot-rate']['IsError'] ?? false ?$errors['lot-rate']['errorDescription']:''?></span>
        </div>
        <div class="form__item form__item--small <?= $errors['lot-step']['IsError'] ?? false ?'form__item--invalid':''?>">
          <label for="lot-step">Шаг ставки <sup>*</sup></label>
          <input id="lot-step" type="text" name="lot-step" placeholder="0" value = "<?= $formData['lot-step'] ?? "";?>">
          <span class="form__error"><?= $errors['lot-step']['IsError'] ?? false ?$errors['lot-step']['errorDescription']:''?></span>
        </div>
        <div class="form__item <?= $errors['lot-date']['IsError'] ?? false ?'form__item--invalid':''?>">
          <label for="lot-date">Дата окончания торгов <sup>*</sup></label>
          <input class="form__input-date" id="lot-date" type="text" name="lot-date" placeholder="Введите дату в формате ГГГГ-ММ-ДД" value = "<?= $formData['lot-date'] ?? "";?>">
          <span class="form__error"><?= $errors['lot-date']['IsError'] ?? false ?$errors['lot-date']['errorDescription']:''?></span>
        </div>
      </div>
      <span class="form__error form__error--bottom">Пожалуйста, исправьте ошибки в форме.</span>
      <button type="submit" class="button">Добавить лот</button>
    </form>