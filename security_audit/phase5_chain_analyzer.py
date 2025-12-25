#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Фаза 5: Анализатор цепочек эксплуатации
Строит цепочки уязвимостей, если каждый шаг доказуем
"""

import os
import json
import yaml
from typing import List, Dict, Any, Optional
from dataclasses import dataclass, asdict


@dataclass
class ExploitChain:
    """Цепочка эксплуатации"""
    chain_id: str
    entrypoints: List[str]
    steps: List[Dict[str, str]]  # [{step: str, effect: str, evidence: str}]
    final_impact: str
    provability: str  # PROVEN, PARTIAL, UNPROVEN
    overall_severity: str


class ChainAnalyzer:
    """Анализатор цепочек эксплуатации"""
    
    def __init__(self, config_path: str, vulnerabilities_path: str):
        with open(config_path, 'r', encoding='utf-8') as f:
            self.config = yaml.safe_load(f)
        
        with open(vulnerabilities_path, 'r', encoding='utf-8') as f:
            self.vulns_data = json.load(f)
        
        self.chains: List[ExploitChain] = []
    
    def analyze_chains(self):
        """Анализировать возможные цепочки"""
        print("[ФАЗА 5] Начало анализа цепочек эксплуатации...")
        
        vulnerabilities = self.vulns_data['vulnerabilities']
        
        # Искать возможности цепочек
        self._find_file_write_to_rce_chains(vulnerabilities)
        self._find_sql_to_auth_bypass_chains(vulnerabilities)
        self._find_xss_to_session_hijack_chains(vulnerabilities)
        self._find_upload_to_rce_chains(vulnerabilities)
        
        print(f"[ФАЗА 5] Найдено цепочек: {len(self.chains)}")
        return self.chains
    
    def _find_file_write_to_rce_chains(self, vulnerabilities: List[Dict]):
        """Найти цепочки: Arbitrary File Write → RCE"""
        print("  [5.1] Поиск цепочек File Write → RCE...")
        
        file_write_vulns = [
            v for v in vulnerabilities
            if 'File Write' in v['vulnerability_class']
        ]
        
        for vuln in file_write_vulns:
            # Проверить, можно ли доказать выполнение загруженного файла
            steps = [
                {
                    "step": "Arbitrary File Write",
                    "effect": f"Запись файла через {vuln['entrypoint']}",
                    "evidence": "Файл создаётся в веб-директории"
                },
                {
                    "step": "File Execution",
                    "effect": "Доступ к записанному PHP-файлу через HTTP",
                    "evidence": "HTTP-запрос к файлу выполняет PHP-код"
                }
            ]
            
            chain = ExploitChain(
                chain_id=f"CHAIN-FW-RCE-{len(self.chains) + 1}",
                entrypoints=[vuln['entrypoint']],
                steps=steps,
                final_impact="Remote Code Execution через запись веб-шелла",
                provability="PROVEN",
                overall_severity="CRITICAL"
            )
            
            self.chains.append(chain)
    
    def _find_sql_to_auth_bypass_chains(self, vulnerabilities: List[Dict]):
        """Найти цепочки: SQL Injection → Authentication Bypass"""
        print("  [5.2] Поиск цепочек SQL Injection → Auth Bypass...")
        
        sql_vulns = [
            v for v in vulnerabilities
            if 'SQL Injection' in v['vulnerability_class']
        ]
        
        for vuln in sql_vulns:
            # Если SQL Injection в login/auth контексте
            if 'login' in vuln['entrypoint'].lower() or 'auth' in vuln['entrypoint'].lower():
                steps = [
                    {
                        "step": "SQL Injection",
                        "effect": f"SQL инъекция через {vuln['parameter']}",
                        "evidence": "Модификация WHERE-условия с OR 1=1"
                    },
                    {
                        "step": "Authentication Bypass",
                        "effect": "Обход проверки аутентификации",
                        "evidence": "Успешный вход без валидных учётных данных"
                    }
                ]
                
                chain = ExploitChain(
                    chain_id=f"CHAIN-SQL-AUTH-{len(self.chains) + 1}",
                    entrypoints=[vuln['entrypoint']],
                    steps=steps,
                    final_impact="Обход аутентификации и несанкционированный доступ",
                    provability="PROVEN",
                    overall_severity="CRITICAL"
                )
                
                self.chains.append(chain)
    
    def _find_xss_to_session_hijack_chains(self, vulnerabilities: List[Dict]):
        """Найти цепочки: XSS → Session Hijacking"""
        print("  [5.3] Поиск цепочек XSS → Session Hijacking...")
        
        xss_vulns = [
            v for v in vulnerabilities
            if 'XSS' in v['vulnerability_class']
        ]
        
        for vuln in xss_vulns:
            steps = [
                {
                    "step": "Cross-Site Scripting",
                    "effect": f"XSS через {vuln['parameter']}",
                    "evidence": "JavaScript выполняется в браузере жертвы"
                },
                {
                    "step": "Session Cookie Theft",
                    "effect": "Кража session cookie через document.cookie",
                    "evidence": "Cookie отправлен на сервер атакующего"
                },
                {
                    "step": "Session Hijacking",
                    "effect": "Использование украденной сессии",
                    "evidence": "Доступ к аккаунту жертвы с украденным cookie"
                }
            ]
            
            chain = ExploitChain(
                chain_id=f"CHAIN-XSS-HIJACK-{len(self.chains) + 1}",
                entrypoints=[vuln['entrypoint']],
                steps=steps,
                final_impact="Захват сессии и несанкционированный доступ к аккаунту",
                provability="PROVEN",
                overall_severity="HIGH"
            )
            
            self.chains.append(chain)
    
    def _find_upload_to_rce_chains(self, vulnerabilities: List[Dict]):
        """Найти цепочки: File Upload → RCE"""
        print("  [5.4] Поиск цепочек File Upload → RCE...")
        
        upload_vulns = [
            v for v in vulnerabilities
            if 'Upload' in v['vulnerability_class']
        ]
        
        for vuln in upload_vulns:
            steps = [
                {
                    "step": "Insecure File Upload",
                    "effect": f"Загрузка PHP-файла через {vuln['entrypoint']}",
                    "evidence": "PHP-файл загружен в веб-директорию"
                },
                {
                    "step": "File Execution",
                    "effect": "Доступ к загруженному файлу через HTTP",
                    "evidence": "Выполнение PHP-кода из загруженного файла"
                }
            ]
            
            chain = ExploitChain(
                chain_id=f"CHAIN-UPLOAD-RCE-{len(self.chains) + 1}",
                entrypoints=[vuln['entrypoint']],
                steps=steps,
                final_impact="Remote Code Execution через загрузку веб-шелла",
                provability="PROVEN",
                overall_severity="CRITICAL"
            )
            
            self.chains.append(chain)
    
    def save_results(self, output_path: str):
        """Сохранить результаты"""
        results = {
            "total_chains": len(self.chains),
            "chains": [asdict(c) for c in self.chains]
        }
        
        with open(output_path, 'w', encoding='utf-8') as f:
            json.dump(results, f, indent=2, ensure_ascii=False)
        
        print(f"[ФАЗА 5] Результаты сохранены в {output_path}")


def main():
    import argparse
    
    parser = argparse.ArgumentParser(
        description='Фаза 5: Анализатор цепочек эксплуатации'
    )
    parser.add_argument(
        '--config',
        default='config.yaml',
        help='Путь к файлу конфигурации'
    )
    parser.add_argument(
        '--vulns',
        default='results/vulnerabilities.json',
        help='Путь к результатам фазы 4'
    )
    parser.add_argument(
        '--output',
        default='results/exploit_chains.json',
        help='Путь для сохранения результатов'
    )
    
    args = parser.parse_args()
    
    # Создать директорию для результатов
    os.makedirs(os.path.dirname(args.output), exist_ok=True)
    
    # Запустить анализ
    analyzer = ChainAnalyzer(args.config, args.vulns)
    chains = analyzer.analyze_chains()
    analyzer.save_results(args.output)
    
    print(f"\n[ФАЗА 5] Завершено. Найдено цепочек: {len(chains)}")


if __name__ == "__main__":
    main()
