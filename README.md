# PHP SDK для сервиса аутентификации 100 Юристов

### Требования
* PHP 7.2+
* Composer

## Установка
Этот репозиторий для внутреннего использования.
У вас должен быть доступ к репозиторию gitlab.com:mkrutikov/id-service-php-sdk.git

Для установки пропишите в файле `composer.json`

```
{
    "require": {
        "mkrutikov/id-service-php-sdk": "dev-master"
    },
    "repositories": [
        {
            "type": "vcs",
            "url":  "git@gitlab.com:mkrutikov/id-service-php-sdk.git"
        }
    ]
}
```
И выполните `composer update`
