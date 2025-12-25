# Security Audit (Data-Layer & Business-Logic)

Scope: customer-facing endpoints where request parameters influence database queries or order/download state.

## Phase 1 — DB/State-Reaching Entry Points
- `download.php` → `DownloadProcess` (`system/classes/downloads/DownloadProcess.inc.php`)
- `print_order.php` → `PrintOrderThemeContentView` (`GXMainComponents/View/ThemeContentViews/orders/PrintOrderThemeContentView.inc.php`)
- `product_info.php`
- `advanced_search_result.php`
- `findologic_export.php`

## Phase 2 — Parameter Trace
- `download.php`: GET `id`, `order`, session `customer_id` → cast to int via setter validation (`set_validation_rules`, ints enforced) → used in query `orders_products_download_id = <int>` and `orders_id = <int>` inside `DownloadProcess::proceed` (lines ~60–79). DB effect: select & decrement download counter.
- `print_order.php`: GET `oID` (passed into setter that enforces int in `set_validation_rules`) → query `SELECT customers_id FROM orders WHERE orders_id = <int>` (PrintOrderThemeContentView `prepare_data`, line ~85). DB effect: read-only fetch of order and totals.
- `product_info.php`: GET `products_id` → cast to int → query `SELECT categories_id FROM products_to_categories WHERE products_id = <int>`. DB effect: read-only lookup.
- `advanced_search_result.php`: GET parameters (`keywords`, `pfrom`, `pto`, `categories_id`, etc.) sanitized via `htmlspecialchars_wrapper` and numeric casts before passing to listing controller setters.
- `findologic_export.php`: GET `shop` escaped with `xtc_db_input` in query `SELECT key FROM gx_configurations WHERE value=':shopkey'` (lines ~32–35); `start`/`limit`/`debug` cast to int before use.

## Phase 3 — Control / Validation
- All traced parameters are cast to integers or escaped (`xtc_db_input`/`htmlspecialchars_wrapper`) before query building (via explicit casting and `set_validation_rules`). No dynamic SQL fragments remain attacker-controlled.
- Access control enforced where state is sensitive:
  - Downloads require an authenticated session and matching `customers_id` + order (`DownloadProcess`, lines ~53–86); aborts if mismatch.
  - Order print view checks session customer matches order owner before rendering, otherwise returns “Access denied!” (`PrintOrderThemeContentView`, lines ~85–103).

## Phase 4 — Proof of Exploitability
- No parameters remained unbound or unvalidated. Queries use integer casts or escaping; business actions gate on authenticated ownership. No observable state change or data exposure could be triggered with crafted input.

### Conclusion
No externally reachable, data-layer or business-logic vulnerabilities were identified in the reviewed entry points.
