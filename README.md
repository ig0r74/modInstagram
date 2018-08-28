## modInstagram

Компонент для MODX, который позволяет выводить на сайте последние посты из Instagram.

> Для работы необходим **pdoTools**, он устанавливается автоматически. Также нужно получить **ACCESS TOKEN**.

**Для получения ACCESS TOKEN:**
1. Авторизоваться под аккаунтом Instagram, чьи посты хотите выводить на сайте.
2. Зарегистрировать приложение https://www.instagram.com/developer/
    Сгенерировать ACCESS TOKEN:<br>
    https://www.instagram.com/oauth/authorize/?client_id=ВАШ_CLIENT_ID&redirect_uri=ВАШ_URL_АВТОРИЗАЦИИ&response_type=token&scope=basic

3. ACCESS TOKEN будет в url, на который вас перенаправит Instagram после авторизации

ACCESS TOKEN можно указать как глобально в системных настройках, так и в каждом вызове сниппета.

Кэширование реализовано через шаблонизатор.

Минимальный вызов (если токен указан в системных настройках):

```
{if !$modInstagram = $_modx->cacheManager->get('mod_instagram')}
    {set $modInstagram = '!modInstagram' | snippet }
    {set $null = $_modx->cacheManager->set('mod_instagram', $modInstagram, 1800)} {* кэш на 30 минут *}
{/if}

{$modInstagram}
```

Вызов со всеми параметрами:

```
{if !$modInstagram = $_modx->cacheManager->get('mod_instagram')}
    {set $modInstagram = '!modInstagram' | snippet : [
        'access_token' => '123123123123123',
        'tpl' => 'tpl.modInstagram.item',
        'tplWrapper' => 'tpl.modInstagram.wrapper',
        'limit' => 8,
        'maxId' => 13872296,
        'minId' => 13872200,
    ]}
    {set $null = $_modx->cacheManager->set('mod_instagram', $modInstagram, 1800)} {* кэш на 30 минут *}
{/if}

{$modInstagram}
```

Также доступны параметры **toPlaceholder** и **showLog**.

В чанке **tpl** доступны следующие плэйсхолдеры:
* **{$idx}** - порядковый номер
* **{$id}** - id поста
* **{$image_thumbnail}**, **{$image_low_resolution}**, **{$image_standard_resolution}** - изображения разных размеров
* **{$created_time}** - время создания
* **{$caption_text}** - текст описания
* **{$likes_count}** - кол-во лайков
* **{$comments_count}** - кол-во комментариев
* **{$type}** - тип (image, video, carousel)
* **{$link}** - ссылка на пост
* **{$location_name}** - Название гео-метки
* **{$video_standard_resolution}**, **{$video_low_bandwidth}**, **{$video_low_resolution}** - видео разных размеров
