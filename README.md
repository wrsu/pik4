# Пикча - скрипт простого фото-хостинга

Скрипт «Пикча» предназначен для развёртывания фото-хостинга в рамках своего проекта, или же как отдельного сайта.

## Установка

* Распакуйте все файлы в корневой каталог сервера
* В файле `index.php` отредактируйте настройки сайта
  * Укажите домен сайта в параметре `DOMAIN`
  * Скорректируйте требуемые размеры изображений в `PFS`, `PMS` и `PTS`
  * Отредактируйте "языковую" функцию, заменив тексты на требуемые
  * Укажите своё веб-приложение ВКонтакте в параметре `VKAPP`
* Используйте свой файл стиля `style.css` в папке `style`

## API для загрузки картинок

Загрузка картинок на сайт выполняется простейшей функцией. Просто отправьте картинку в поле `newpic` POST-запросом на адрес http://sitename.ru/. Результат картинки будет получен в формате JSON. 

Поля успешного ответа:

* `status` - всегда *ok*
* `id` - идентификатор загруженной картинки
* `view` - ссылка на просмотр картинки на сайте
* `full` - ссылка на JPEG-файл картинки в полной версии
* `image` - ссылка на JPEG-файл картинки в средней версии
* `thumb` - ссылка на JPEG-файл картинки в миниатюре

В случае ошибки, поле `status` будет иметь значение *error*, а в поле `message` будет передан код ошибки:

* `badformat` - формат картинки не распознан
* `toobig` - картинка слишком большая, не хватает памяти на обработку
