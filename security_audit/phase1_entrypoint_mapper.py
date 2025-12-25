#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Фаза 1: Картирование точек входа
Выполняет полное картирование всех внешних точек входа в приложение
"""

import os
import re
import json
import yaml
from pathlib import Path
from typing import List, Dict, Set, Any
from dataclasses import dataclass, asdict


@dataclass
class Entrypoint:
    """Представление точки входа"""
    file_path: str
    handler: str
    transport: str  # HTTP, callback, file-triggered, client-side
    methods: List[str]
    parameters: List[Dict[str, str]]
    authentication: str
    trust_assumption: str
    line_number: int = 0


class EntrypointMapper:
    """Картирование точек входа приложения"""
    
    def __init__(self, config_path: str):
        with open(config_path, 'r', encoding='utf-8') as f:
            self.config = yaml.safe_load(f)
        
        self.base_path = self.config['target_application']['base_path']
        self.entrypoints: List[Entrypoint] = []
        self.analyzed_files: Set[str] = set()
    
    def find_all_entrypoints(self) -> List[Entrypoint]:
        """Найти все точки входа в приложении"""
        print("[ФАЗА 1] Начало картирования точек входа...")
        
        # 1. HTTP controllers - прямые PHP файлы в корне
        self._scan_http_controllers()
        
        # 2. Callback endpoints
        self._scan_callback_endpoints()
        
        # 3. API endpoints
        self._scan_api_endpoints()
        
        # 4. AJAX handlers
        self._scan_ajax_handlers()
        
        # 5. Admin endpoints
        self._scan_admin_endpoints()
        
        # 6. Installer/Updater endpoints
        self._scan_maintenance_endpoints()
        
        # 7. Cron/Worker endpoints
        self._scan_cron_endpoints()
        
        print(f"[ФАЗА 1] Найдено {len(self.entrypoints)} точек входа")
        return self.entrypoints
    
    def _scan_http_controllers(self):
        """Сканировать HTTP контроллеры в корне"""
        print("  [1.1] Сканирование HTTP контроллеров...")
        
        root_path = Path(self.base_path)
        php_files = list(root_path.glob("*.php"))
        
        for php_file in php_files:
            if php_file.name.startswith('.'):
                continue
            
            self._analyze_php_entrypoint(
                str(php_file),
                transport="HTTP",
                handler_type="controller"
            )
    
    def _scan_callback_endpoints(self):
        """Сканировать callback endpoints"""
        print("  [1.2] Сканирование callback endpoints...")
        
        callback_path = Path(self.base_path) / "callback"
        if callback_path.exists():
            for php_file in callback_path.rglob("*.php"):
                self._analyze_php_entrypoint(
                    str(php_file),
                    transport="HTTP-Callback",
                    handler_type="callback"
                )
    
    def _scan_api_endpoints(self):
        """Сканировать API endpoints"""
        print("  [1.3] Сканирование API endpoints...")
        
        api_patterns = ["api*.php", "*api*.php"]
        for pattern in api_patterns:
            for php_file in Path(self.base_path).glob(pattern):
                if php_file.is_file():
                    self._analyze_php_entrypoint(
                        str(php_file),
                        transport="HTTP-API",
                        handler_type="api"
                    )
    
    def _scan_ajax_handlers(self):
        """Сканировать AJAX handlers"""
        print("  [1.4] Сканирование AJAX handlers...")
        
        ajax_patterns = ["*ajax*.php", "autocomplete*.php"]
        for pattern in ajax_patterns:
            for php_file in Path(self.base_path).rglob(pattern):
                if php_file.is_file():
                    self._analyze_php_entrypoint(
                        str(php_file),
                        transport="HTTP-AJAX",
                        handler_type="ajax"
                    )
    
    def _scan_admin_endpoints(self):
        """Сканировать admin endpoints"""
        print("  [1.5] Сканирование admin endpoints...")
        
        admin_paths = [
            "admin",
            "GambioAdmin",
            "login_admin.php"
        ]
        
        for admin_path in admin_paths:
            full_path = Path(self.base_path) / admin_path
            if full_path.exists():
                if full_path.is_file():
                    self._analyze_php_entrypoint(
                        str(full_path),
                        transport="HTTP",
                        handler_type="admin"
                    )
                else:
                    for php_file in full_path.rglob("*.php"):
                        self._analyze_php_entrypoint(
                            str(php_file),
                            transport="HTTP",
                            handler_type="admin"
                        )
    
    def _scan_maintenance_endpoints(self):
        """Сканировать installer/updater endpoints"""
        print("  [1.6] Сканирование maintenance endpoints...")
        
        maintenance_paths = [
            "gambio_installer",
            "gambio_updater"
        ]
        
        for maint_path in maintenance_paths:
            full_path = Path(self.base_path) / maint_path
            if full_path.exists():
                for php_file in full_path.rglob("*.php"):
                    self._analyze_php_entrypoint(
                        str(php_file),
                        transport="HTTP",
                        handler_type="maintenance"
                    )
    
    def _scan_cron_endpoints(self):
        """Сканировать cron/worker endpoints"""
        print("  [1.7] Сканирование cron/worker endpoints...")
        
        cron_pattern = "*cron*.php"
        for php_file in Path(self.base_path).glob(cron_pattern):
            if php_file.is_file():
                self._analyze_php_entrypoint(
                    str(php_file),
                    transport="HTTP-Cron",
                    handler_type="cron"
                )
    
    def _analyze_php_entrypoint(self, file_path: str, transport: str, handler_type: str):
        """Анализировать PHP файл как точку входа"""
        if file_path in self.analyzed_files:
            return
        
        self.analyzed_files.add(file_path)
        
        try:
            with open(file_path, 'r', encoding='utf-8', errors='ignore') as f:
                content = f.read()
            
            # Извлечь параметры из $_GET, $_POST, $_REQUEST
            parameters = self._extract_parameters(content)
            
            # Определить HTTP методы
            methods = self._detect_http_methods(content)
            
            # Определить требования аутентификации
            auth = self._detect_authentication(content, file_path)
            
            # Определить trust assumptions
            trust = self._detect_trust_assumptions(content, handler_type)
            
            # Создать entrypoint
            entrypoint = Entrypoint(
                file_path=file_path.replace(self.base_path, ""),
                handler=os.path.basename(file_path),
                transport=transport,
                methods=methods,
                parameters=parameters,
                authentication=auth,
                trust_assumption=trust,
                line_number=1
            )
            
            self.entrypoints.append(entrypoint)
            
        except Exception as e:
            print(f"    Ошибка при анализе {file_path}: {e}")
    
    def _extract_parameters(self, content: str) -> List[Dict[str, str]]:
        """Извлечь параметры из кода"""
        parameters = []
        
        # Паттерны для поиска параметров
        patterns = [
            r'\$_GET\s*\[\s*[\'"](\w+)[\'"]\s*\]',
            r'\$_POST\s*\[\s*[\'"](\w+)[\'"]\s*\]',
            r'\$_REQUEST\s*\[\s*[\'"](\w+)[\'"]\s*\]',
            r'\$_COOKIE\s*\[\s*[\'"](\w+)[\'"]\s*\]',
            r'\$_FILES\s*\[\s*[\'"](\w+)[\'"]\s*\]',
        ]
        
        seen_params = set()
        
        for pattern in patterns:
            matches = re.finditer(pattern, content)
            for match in matches:
                param_name = match.group(1)
                source = match.group(0).split('[')[0].replace('$', '')
                
                param_key = f"{source}:{param_name}"
                if param_key not in seen_params:
                    seen_params.add(param_key)
                    parameters.append({
                        "name": param_name,
                        "source": source,
                        "type": "user_input"
                    })
        
        return parameters
    
    def _detect_http_methods(self, content: str) -> List[str]:
        """Определить поддерживаемые HTTP методы"""
        methods = []
        
        if re.search(r'\$_GET', content):
            methods.append("GET")
        if re.search(r'\$_POST', content):
            methods.append("POST")
        if re.search(r'\$_REQUEST', content):
            methods.extend(["GET", "POST"])
        if re.search(r'REQUEST_METHOD.*PUT', content):
            methods.append("PUT")
        if re.search(r'REQUEST_METHOD.*DELETE', content):
            methods.append("DELETE")
        
        # Уникальные методы
        methods = list(set(methods))
        
        return methods if methods else ["GET", "POST"]
    
    def _detect_authentication(self, content: str, file_path: str) -> str:
        """Определить требования аутентификации"""
        # Проверка на admin-файлы
        if "admin" in file_path.lower() or "login_admin" in file_path:
            if re.search(r'(session|auth|login|password)', content, re.IGNORECASE):
                return "admin_required"
        
        # Проверка на пользовательскую аутентификацию
        if re.search(r'(customer.*login|user.*auth|\$_SESSION.*customer)', content, re.IGNORECASE):
            return "user_required"
        
        # Проверка на callback authentication
        if "callback" in file_path.lower():
            if re.search(r'(verify|signature|token|secret)', content, re.IGNORECASE):
                return "token_verification"
        
        return "none"
    
    def _detect_trust_assumptions(self, content: str, handler_type: str) -> str:
        """Определить trust assumptions"""
        assumptions = []
        
        # Callback endpoints часто доверяют внешним сервисам
        if handler_type == "callback":
            assumptions.append("trusts_external_service")
        
        # Проверка на доверие к proxy headers
        proxy_headers = [
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_REAL_IP',
            'HTTP_CLIENT_IP'
        ]
        for header in proxy_headers:
            if header in content:
                assumptions.append("trusts_proxy_headers")
                break
        
        # Проверка на доверие к referer
        if 'HTTP_REFERER' in content:
            assumptions.append("trusts_referer")
        
        # Проверка на доверие к user agent
        if 'HTTP_USER_AGENT' in content:
            assumptions.append("uses_user_agent")
        
        return ", ".join(assumptions) if assumptions else "no_special_trust"
    
    def save_results(self, output_path: str):
        """Сохранить результаты в JSON"""
        results = {
            "total_entrypoints": len(self.entrypoints),
            "entrypoints": [asdict(ep) for ep in self.entrypoints]
        }
        
        with open(output_path, 'w', encoding='utf-8') as f:
            json.dump(results, f, indent=2, ensure_ascii=False)
        
        print(f"[ФАЗА 1] Результаты сохранены в {output_path}")


def main():
    import argparse
    
    parser = argparse.ArgumentParser(
        description='Фаза 1: Картирование точек входа'
    )
    parser.add_argument(
        '--config',
        default='config.yaml',
        help='Путь к файлу конфигурации'
    )
    parser.add_argument(
        '--output',
        default='results/entrypoints.json',
        help='Путь для сохранения результатов'
    )
    
    args = parser.parse_args()
    
    # Создать директорию для результатов
    os.makedirs(os.path.dirname(args.output), exist_ok=True)
    
    # Запустить картирование
    mapper = EntrypointMapper(args.config)
    entrypoints = mapper.find_all_entrypoints()
    mapper.save_results(args.output)
    
    print(f"\n[ФАЗА 1] Завершено. Найдено точек входа: {len(entrypoints)}")


if __name__ == "__main__":
    main()
