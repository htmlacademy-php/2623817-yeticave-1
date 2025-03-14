<form class="form container <?= ($formError)?'form--invalid':''?>" action="sign-up.php" method="post" autocomplete="off"> <!-- form
    --invalid -->
      <h2>Регистрация нового аккаунта</h2>
      <div class="form__item <?= $errors['email']['IsError'] ?? false ?'form__item--invalid':''?>"> <!-- form__item--invalid -->
        <label for="email">E-mail <sup>*</sup></label>
        <input id="email" type="text" name="email" placeholder="Введите e-mail" value = "<?= $formData['email'] ?? "";?>">
        <span class="form__error"><?= $errors['email']['IsError'] ?? false ?$errors['email']['errorDescription']:'Введите e-mail'?></span>
      </div>
      <div class="form__item <?= $errors['password']['IsError'] ?? false ?'form__item--invalid':''?>">
        <label for="password">Пароль <sup>*</sup></label>
        <input id="password" type="password" name="password" placeholder="Введите пароль" value = "<?= $formData['password'] ?? "";?>">
        <span class="form__error"><?= $errors['password']['IsError'] ?? false ?$errors['password']['errorDescription']:'Введите пароль'?></span>
      </div>
      <div class="form__item <?= $errors['name']['IsError'] ?? false ?'form__item--invalid':''?>">
        <label for="name">Имя <sup>*</sup></label>
        <input id="name" type="text" name="name" placeholder="Введите имя" value = "<?= $formData['name'] ?? "";?>">
        <span class="form__error"><?= $errors['name']['IsError'] ?? false ?$errors['name']['errorDescription']:'Введите имя'?></span>
      </div>
      <div class="form__item <?= $errors['message']['IsError'] ?? false ?'form__item--invalid':''?>">
        <label for="message">Контактные данные <sup>*</sup></label>
        <textarea id="message" name="message" placeholder="Напишите как с вами связаться"><?= $formData['message'] ?? "";?></textarea>
        <span class="form__error"><?= $errors['message']['IsError'] ?? false ?$errors['message']['errorDescription']:'Напишите как с вами связаться'?></span>
      </div>
      <span class="form__error form__error--bottom">Пожалуйста, исправьте ошибки в форме.</span>
      <button type="submit" class="button">Зарегистрироваться</button>
      <a class="text-link" href="pages/login.html">Уже есть аккаунт</a>
    </form>