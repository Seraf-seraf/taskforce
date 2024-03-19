# Учебный проект: Фрилансбиржа Taskforce

## Описание проекта

Проект "Фрилансбиржа Taskforce" был создан в рамках изучения фреймворка Yii2 и ООП в PHP. В ходе работы над проектом были освоены основы работы с Yii2, настройка конфигурации приложения, использование виджетов фреймворка, а также разделение ролей пользователей с помощью Role Based Access Control (RBAC). Был разработан класс для определения карты действий, который позволяет разграничивать доступ к различным функциям для заказчиков и исполнителей.

Также в проекте был изучен и применен ORM для работы с базой данных. Использовались библиотеки Dropzone и YandexMap. Yandex Map был интегрирован для отображения места выполнения задачи и генерации подсказок адресов.

Добавлена возможность регистрации и авторизации на сайте с использованием VK ID. Была выявлена важность использования миграций при работе с базой данных во время разработки проекта.

Главной функциональностью является размещение задач и возможность для исполнителей откликаться и выполнять задачи. После размещения задания заказчик выбирает исполнителя, а по завершении задачи оставляет отзыв. Для каждого исполнителя расчитывается рейтинг и распределяются места.

Второстепенными возможностями пользователей являются поиск задач по фильтрам и настройка аккаунта. Валидация форм реализована с помощью Yii2.

Изучено кэширование Redis.

## Использованные технологии

- Yii2 Framework
- ООП в PHP
- ORM
- Dropzone
- YandexMap
