<form class="form container <?= ($formError)?'form--invalid':''?>" action="login.php" method="post"> <!-- form--invalid -->
      <h2>Вход</h2>
      <div class="form__item <?= $errors['email']['IsError'] ?? false ?'form__item--invalid':''?>"> <!-- form__item--invalid -->
        <label for="email">E-mail <sup>*</sup></label>
        <input id="email" type="text" name="email" placeholder="Введите e-mail" value = "<?= $formData['email'] ?? "";?>">
        <span class="form__error"><?= $errors['email']['IsError'] ?? false ?$errors['email']['errorDescription']:'Введите e-mail'?></span>
      </div>
      <div class="form__item form__item--last <?= $errors['password']['IsError'] ?? false ?'form__item--invalid':''?>">
        <label for="password">Пароль <sup>*</sup></label>
        <input id="password" type="password" name="password" placeholder="Введите пароль" value = "<?= $formData['password'] ?? "";?>">
        <span class="form__error"><?= $errors['password']['IsError'] ?? false ?$errors['password']['errorDescription']:'Введите пароль'?></span>
      </div>
      <span class="form__error form__error--bottom">Вы ввели неверный email/пароль.</span>
      <button type="submit" class="button">Войти</button>
    </form>