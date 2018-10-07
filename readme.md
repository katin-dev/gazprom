# Тестовое задание на вакансию «Разработчик PHP»
## Задание
### ДАНО
2 файла с логами.

В 1-ом файле в каждой строке выводится 5 атрибутов разделенные символом «|»: 
* дата
* время
* IP-адрес пользователя
* URL с которого зашел
* URL куда зашел.

Во 2-ом — в каждой строке 3 атрибута, также разделенные символом «|»: 
* ip aдрес пользователя
* наименование используемого браузера
* наименование используемой ОС.

### ЗАДАНИЯ
1. Необходимо считать данные из этих файлов и записать в базу данных (MySQL или Postgresql).
2. Затем с помощью одного запроса отдать данные на клиент для их отображения в таблице со следующими колонками: IP-адрес, браузер (возможность сортировки), ос(возможность сортировки), URL с которого зашел первый раз, URL на который зашел последний раз, кол-во просмотренных уникальных URL-адресов. Таблица должна предусматривать постраничный просмотр. Требуется также фильтрацию данных по IP.

### ВАЖНЫЕ ЗАМЕЧАНИЯ
На фронтенде JS+HTML, на бэкенде PHP. Можно использовать любые фреймворки, можно обойтись без фреймворков, но не решайте тупо в лоб, покажите свой уровень, сделайте архитектурно красиво. SQL-инъекций и прочих уязвимостей в коде быть не должно, на безопасность кода мы будем обращать самое пристальное внимание. Также просим уделить время стилю кода, комментариям - нужно сделать все красиво, не торопясь.

### РЕЗУЛЬТАТ ВЫПОЛНЕНИЯ ЗАДАНИЯ
Микроприложение, состоящее из следующих файлов и сценариев:
1. sql-файл для создания таблицы в БД
2. два файла логов для тестирования
3. скрипт парсинга файлов логов и записи результатов в БД
4. серверный скрипт для отдачи данных на клиент
5. фронтенд-скрипт на JS + HTML

Приложение должно быть рабочим после создания таблицы в БД и запуска скрипта парсинга лог-файлов.

## Решение

### Скачать код и установить библиотеки
```
git clone git@github.com:katin-dev/gazprom.git
cd gazprom
composer install
```

### Указать доступ к БД
```
cp app/config.php app/config.loc.php
```
Укажите доступ к ваше БД в файле `app/config.loc.php`

### Загрузить данные в БД
```
php app/console.php app:import
```

### Запустить веб-сервер
```
cd public
php -S localhost:8080
```

### Открыть страницу
Открыть страницу по адресу [http://localhost:8080/](http://localhost:8080/)
