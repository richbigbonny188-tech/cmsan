# Демонстрация Security Audit Framework

## Обзор системы

Данный инструмент представляет собой полнофункциональный framework для проведения white-box аудита безопасности веб-приложений на PHP. Он реализует строгую 5-фазную методологию, соответствующую требованиям профессионального аудита безопасности.

## Архитектура решения

### Модульная структура

Framework построен по модульному принципу, где каждая фаза аудита реализована как независимый Python-модуль:

```
Phase 1: Entrypoint Mapper → Phase 2: Dataflow Tracer → Phase 3: Control Filter
                                                              ↓
                                                    Phase 4: Exploitability
                                                              ↓
                                                    Phase 5: Chain Analyzer
                                                              ↓
                                                        Report Generator
```

### Ключевые компоненты

1. **audit_main.py** - Оркестратор, координирующий выполнение всех фаз
2. **phase1_entrypoint_mapper.py** - Идентифицирует все точки входа
3. **phase2_dataflow_tracer.py** - Отслеживает потоки данных
4. **phase3_control_filter.py** - Фильтрует безопасные потоки
5. **phase4_exploitability.py** - Определяет эксплуатируемость
6. **phase5_chain_analyzer.py** - Строит цепочки атак
7. **report_generator.py** - Генерирует финальный отчёт

## Методология анализа

### Фаза 1: Картирование точек входа

**Цель:** Идентифицировать ВСЕ внешне достижимые точки входа

**Процесс:**
1. Сканирование корневых PHP-файлов (HTTP controllers)
2. Анализ директории callback/ (webhook endpoints)
3. Поиск API endpoints (api*.php)
4. Обнаружение AJAX handlers (*ajax*.php)
5. Анализ admin endpoints (admin/, GambioAdmin/, login_admin.php)
6. Поиск maintenance endpoints (gambio_installer/, gambio_updater/)
7. Идентификация cron endpoints (*cron*.php)

**Выход:**
```json
{
  "total_entrypoints": 150,
  "entrypoints": [
    {
      "file_path": "/index.php",
      "handler": "index.php",
      "transport": "HTTP",
      "methods": ["GET", "POST"],
      "parameters": [
        {"name": "page", "source": "_GET", "type": "user_input"},
        {"name": "id", "source": "_GET", "type": "user_input"}
      ],
      "authentication": "none",
      "trust_assumption": "no_special_trust"
    }
  ]
}
```

### Фаза 2: Трассировка потока данных

**Цель:** Проследить каждый параметр от источника до стока

**Процесс:**
1. Для каждого параметра из фазы 1:
   - Найти все вхождения в коде
   - Зафиксировать трансформации (casting, encoding, filtering)
   - Идентифицировать сток (SQL, command, file, output)
   - Оценить сохранение контроля пользователя

**Трансформации отслеживаются:**
- Type casting: `(int)`, `(float)`, `intval()`, `floatval()`
- Encoding: `htmlspecialchars()`, `urlencode()`, `base64_encode()`
- Decoding: `urldecode()`, `base64_decode()`, `json_decode()`
- Filtering: `filter_var()`, `strip_tags()`, `preg_replace()`
- Sanitization: `mysqli_real_escape_string()`, `addslashes()`

**Выход:**
```json
{
  "total_dataflows": 500,
  "dataflows": [
    {
      "entrypoint": "product_info.php",
      "parameter": "id",
      "source": "$_GET['id']",
      "transformations": [
        {
          "type": "casting",
          "function": "intval",
          "line_number": 15,
          "eliminates_control": true
        }
      ],
      "sink": "mysqli_query",
      "sink_type": "sql",
      "sink_line": 20,
      "user_control_preserved": "NO"
    }
  ]
}
```

### Фаза 3: Фильтр устранения контроля

**Цель:** Отбросить потоки, где контроль пользователя устранён

**Критерии отбрасывания:**
- Type cast к безопасному типу (int, float, bool)
- Whitelist-фильтрация
- Полная санитизация
- Отсутствие стока

**Выход:**
```json
{
  "total_filtered_flows": 50,
  "filtered_flows": [...],
  "total_discarded_flows": 450,
  "discarded_flows": [
    {
      "entrypoint": "product_info.php",
      "parameter": "id",
      "reason": "type_cast",
      "control_lost_at": "intval",
      "control_lost_line": 15,
      "details": "Приведение типа через intval полностью устраняет контроль"
    }
  ]
}
```

### Фаза 4: Анализ эксплуатируемости

**Цель:** Определить реальную эксплуатируемость оставшихся потоков

**Классы уязвимостей:**
- SQL Injection
- Command Injection
- Path Traversal / File Inclusion
- Code Execution
- Cross-Site Scripting (XSS)
- Insecure File Upload
- Server-Side Request Forgery (SSRF)

**Для каждой уязвимости определяется:**
1. Точное условие эксплуатации
2. Наблюдаемое воздействие
3. Конкретные доказательства
4. Серьёзность (CRITICAL, HIGH, MEDIUM, LOW)
5. Уверенность (HIGH, MEDIUM, LOW)

**Выход:**
```json
{
  "total_vulnerabilities": 5,
  "vulnerabilities": [
    {
      "entrypoint": "search.php",
      "parameter": "query",
      "vulnerability_class": "SQL Injection",
      "sink_function": "mysqli_query",
      "exploitation_condition": "Пользовательский ввод напрямую конкатенируется в SQL-запрос",
      "observable_impact": "Выполнение произвольных SQL-команд",
      "proof_evidence": [
        "SQL-ошибка при отправке символа '",
        "Изменение логики через OR 1=1",
        "Извлечение данных через UNION SELECT"
      ],
      "severity": "CRITICAL",
      "confidence": "HIGH"
    }
  ]
}
```

### Фаза 5: Анализ цепочек

**Цель:** Построить цепочки эксплуатации, где каждый шаг доказуем

**Типы цепочек:**
1. File Write → RCE
2. SQL Injection → Authentication Bypass
3. XSS → Session Hijacking
4. File Upload → RCE

**Выход:**
```json
{
  "total_chains": 2,
  "chains": [
    {
      "chain_id": "CHAIN-FW-RCE-1",
      "entrypoints": ["backup_restore.php"],
      "steps": [
        {
          "step": "Arbitrary File Write",
          "effect": "Запись файла в веб-директорию",
          "evidence": "Файл создаётся в /public_html/"
        },
        {
          "step": "File Execution",
          "effect": "Доступ к PHP-файлу через HTTP",
          "evidence": "HTTP-запрос выполняет PHP-код"
        }
      ],
      "final_impact": "Remote Code Execution",
      "provability": "PROVEN",
      "overall_severity": "CRITICAL"
    }
  ]
}
```

## Финальный отчёт

Отчёт генерируется в формате Markdown на русском языке и включает:

### Структура отчёта

1. **Заголовок и метаданные**
   - Название приложения и версия
   - Дата аудита
   - Тип аудита

2. **Исполнительное резюме**
   - Общее количество уязвимостей
   - Распределение по серьёзности
   - Ключевые выводы

3. **Методология**
   - Описание 5-фазного подхода
   - Глобальные ограничения

4. **Статистика анализа**
   - Точки входа
   - Потоки данных
   - Отфильтрованные/отброшенные потоки

5. **Картирование точек входа**
   - Обзор по типам транспорта
   - Список endpoints

6. **Подтверждённые уязвимости**
   - Детальное описание каждой уязвимости
   - Условия эксплуатации
   - Необходимые доказательства
   - Реальное значение

7. **Цепочки эксплуатации** (если найдены)
   - Пошаговое описание
   - Доказательства для каждого шага

8. **Заключение**
   - Общая оценка безопасности
   - Приоритетные рекомендации
   - Следующие шаги

9. **Приложения**
   - Ссылки на исходные данные
   - Конфигурация аудита

## Пример использования

### Базовый запуск

```bash
cd security_audit
python3 audit_main.py
```

### Поэтапный запуск

```bash
# Фаза 1
python3 phase1_entrypoint_mapper.py

# Фаза 2
python3 phase2_dataflow_tracer.py --entrypoints results/entrypoints.json

# Фаза 3
python3 phase3_control_filter.py --dataflows results/dataflows.json

# Фаза 4
python3 phase4_exploitability.py --filtered results/filtered_flows.json

# Фаза 5
python3 phase5_chain_analyzer.py --vulns results/vulnerabilities.json

# Генерация отчёта
python3 report_generator.py --results-dir results
```

## Преимущества решения

### 1. Строгая методология
- Никаких спекуляций
- Только доказуемые уязвимости
- Конкретные доказательства для каждой находки

### 2. Полнота анализа
- Все типы точек входа
- Все классы уязвимостей
- Цепочки эксплуатации

### 3. Модульность
- Независимые фазы
- Возможность расширения
- Лёгкая интеграция

### 4. Автоматизация
- Полностью автоматизированный процесс
- Воспроизводимые результаты
- Минимум ручного вмешательства

### 5. Детальная документация
- Русский язык
- Понятные объяснения
- Конкретные примеры

## Ограничения

### Что делает инструмент:
✅ Статический анализ кода
✅ Трассировка потоков данных
✅ Идентификация очевидных уязвимостей
✅ Документирование находок

### Что НЕ делает инструмент:
❌ Динамический анализ
❌ Автоматическая эксплуатация
❌ Полное покрытие всех векторов
❌ Замена ручного тестирования

## Расширение функциональности

Framework легко расширяется:

### Добавление новых стоков

```yaml
# config.yaml
dangerous_sinks:
  custom_category:
    - custom_dangerous_function
```

### Добавление новых паттернов

```python
# phase1_entrypoint_mapper.py
def _scan_custom_endpoints(self):
    # Ваша логика сканирования
    pass
```

### Добавление новых классов уязвимостей

```python
# phase4_exploitability.py
def _analyze_custom_vulnerability(self, flow, vuln_class):
    # Ваша логика анализа
    pass
```

## Заключение

Security Audit Framework представляет собой мощный инструмент для проведения профессионального аудита безопасности веб-приложений на PHP. Он следует строгой методологии, не допускает спекуляций и предоставляет конкретные, доказуемые результаты.

Инструмент идеально подходит для:
- Регулярных аудитов безопасности
- Pre-production проверок
- Ответственного раскрытия уязвимостей
- Обучения основам безопасности

**ВАЖНО:** Инструмент предназначен исключительно для авторизованного тестирования безопасности.
