#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Главный скрипт аудита безопасности
Оркестрирует выполнение всех 5 фаз анализа и генерацию отчёта
"""

import os
import sys
import argparse
from pathlib import Path

# Импортировать все фазы
from phase1_entrypoint_mapper import EntrypointMapper
from phase2_dataflow_tracer import DataFlowTracer
from phase3_control_filter import ControlFilter
from phase4_exploitability import ExploitabilityAnalyzer
from phase5_chain_analyzer import ChainAnalyzer
from report_generator import ReportGenerator


class SecurityAuditOrchestrator:
    """Оркестратор полного аудита безопасности"""
    
    def __init__(self, config_path: str, results_dir: str):
        self.config_path = config_path
        self.results_dir = results_dir
        
        # Создать директорию для результатов
        os.makedirs(results_dir, exist_ok=True)
        
        # Пути к результатам каждой фазы
        self.entrypoints_path = os.path.join(results_dir, 'entrypoints.json')
        self.dataflows_path = os.path.join(results_dir, 'dataflows.json')
        self.filtered_path = os.path.join(results_dir, 'filtered_flows.json')
        self.discarded_path = os.path.join(results_dir, 'discarded_flows.json')
        self.vulns_path = os.path.join(results_dir, 'vulnerabilities.json')
        self.chains_path = os.path.join(results_dir, 'exploit_chains.json')
        self.report_path = os.path.join(results_dir, 'SECURITY_AUDIT_REPORT.md')
    
    def run_full_audit(self):
        """Запустить полный аудит"""
        print("=" * 80)
        print("ПОЛНЫЙ АУДИТ БЕЗОПАСНОСТИ ВЕБА-ПРИЛОЖЕНИЯ")
        print("White-box Security Audit Framework")
        print("=" * 80)
        print()
        
        try:
            # Фаза 1: Картирование точек входа
            print("\n" + "=" * 80)
            self._run_phase1()
            
            # Фаза 2: Трассировка потока данных
            print("\n" + "=" * 80)
            self._run_phase2()
            
            # Фаза 3: Фильтр устранения контроля
            print("\n" + "=" * 80)
            self._run_phase3()
            
            # Фаза 4: Анализ эксплуатируемости
            print("\n" + "=" * 80)
            self._run_phase4()
            
            # Фаза 5: Анализ цепочек
            print("\n" + "=" * 80)
            self._run_phase5()
            
            # Генерация отчёта
            print("\n" + "=" * 80)
            self._generate_report()
            
            # Итоговая информация
            print("\n" + "=" * 80)
            self._print_summary()
            print("=" * 80)
            
        except KeyboardInterrupt:
            print("\n\n[!] Аудит прерван пользователем")
            sys.exit(1)
        except Exception as e:
            print(f"\n\n[!] Критическая ошибка: {e}")
            import traceback
            traceback.print_exc()
            sys.exit(1)
    
    def _run_phase1(self):
        """Запустить фазу 1: Картирование точек входа"""
        print("[ФАЗА 1/5] Картирование точек входа")
        print("-" * 80)
        
        mapper = EntrypointMapper(self.config_path)
        entrypoints = mapper.find_all_entrypoints()
        mapper.save_results(self.entrypoints_path)
        
        print(f"✓ Фаза 1 завершена: найдено {len(entrypoints)} точек входа")
    
    def _run_phase2(self):
        """Запустить фазу 2: Трассировка потока данных"""
        print("[ФАЗА 2/5] Трассировка потока данных")
        print("-" * 80)
        
        tracer = DataFlowTracer(self.config_path, self.entrypoints_path)
        dataflows = tracer.trace_all_dataflows()
        tracer.save_results(self.dataflows_path)
        
        print(f"✓ Фаза 2 завершена: проанализировано {len(dataflows)} потоков данных")
    
    def _run_phase3(self):
        """Запустить фазу 3: Фильтр устранения контроля"""
        print("[ФАЗА 3/5] Фильтр устранения контроля")
        print("-" * 80)
        
        filter_obj = ControlFilter(self.config_path, self.dataflows_path)
        filtered, discarded = filter_obj.filter_dataflows()
        filter_obj.save_results(self.filtered_path, self.discarded_path)
        
        print(f"✓ Фаза 3 завершена: сохранено {len(filtered)} потоков, "
              f"отброшено {len(discarded)} потоков")
    
    def _run_phase4(self):
        """Запустить фазу 4: Анализ эксплуатируемости"""
        print("[ФАЗА 4/5] Анализ эксплуатируемости")
        print("-" * 80)
        
        analyzer = ExploitabilityAnalyzer(self.config_path, self.filtered_path)
        vulns = analyzer.analyze_exploitability()
        analyzer.save_results(self.vulns_path)
        
        print(f"✓ Фаза 4 завершена: найдено {len(vulns)} уязвимостей")
    
    def _run_phase5(self):
        """Запустить фазу 5: Анализ цепочек"""
        print("[ФАЗА 5/5] Анализ цепочек эксплуатации")
        print("-" * 80)
        
        analyzer = ChainAnalyzer(self.config_path, self.vulns_path)
        chains = analyzer.analyze_chains()
        analyzer.save_results(self.chains_path)
        
        print(f"✓ Фаза 5 завершена: найдено {len(chains)} цепочек")
    
    def _generate_report(self):
        """Сгенерировать финальный отчёт"""
        print("[ОТЧЁТ] Генерация финального отчёта")
        print("-" * 80)
        
        generator = ReportGenerator(self.config_path, self.results_dir)
        report_path = generator.generate_report(self.report_path)
        
        print(f"✓ Отчёт создан: {report_path}")
    
    def _print_summary(self):
        """Вывести итоговую информацию"""
        import json
        
        # Загрузить результаты
        with open(self.vulns_path, 'r', encoding='utf-8') as f:
            vulns_data = json.load(f)
        
        total_vulns = vulns_data.get('total_vulnerabilities', 0)
        
        print("\nИТОГОВАЯ ИНФОРМАЦИЯ")
        print("-" * 80)
        
        if total_vulns == 0:
            print("✓ Эксплуатируемые уязвимости не были доказаны.")
            print()
            print("Все проанализированные потоки данных либо имеют адекватную защиту,")
            print("либо не достигают опасных стоков.")
        else:
            print(f"⚠ Обнаружено {total_vulns} подтверждённых уязвимостей")
            print()
            
            # Подсчитать по серьёзности
            severities = {'CRITICAL': 0, 'HIGH': 0, 'MEDIUM': 0, 'LOW': 0}
            for vuln in vulns_data.get('vulnerabilities', []):
                sev = vuln.get('severity', 'MEDIUM')
                severities[sev] = severities.get(sev, 0) + 1
            
            print("Распределение по серьёзности:")
            if severities['CRITICAL'] > 0:
                print(f"  🔴 CRITICAL: {severities['CRITICAL']}")
            if severities['HIGH'] > 0:
                print(f"  🟠 HIGH: {severities['HIGH']}")
            if severities['MEDIUM'] > 0:
                print(f"  🟡 MEDIUM: {severities['MEDIUM']}")
            if severities['LOW'] > 0:
                print(f"  🟢 LOW: {severities['LOW']}")
        
        print()
        print(f"Полный отчёт: {self.report_path}")
        print()


def main():
    parser = argparse.ArgumentParser(
        description='Полный аудит безопасности веб-приложения',
        formatter_class=argparse.RawDescriptionHelpFormatter,
        epilog="""
Примеры использования:

  # Запустить полный аудит с конфигурацией по умолчанию
  python3 audit_main.py

  # Запустить с пользовательской конфигурацией
  python3 audit_main.py --config my_config.yaml

  # Сохранить результаты в другую директорию
  python3 audit_main.py --results-dir /tmp/audit_results

Все результаты сохраняются в директории results/:
  - entrypoints.json          - Точки входа
  - dataflows.json            - Потоки данных
  - filtered_flows.json       - Отфильтрованные потоки
  - discarded_flows.json      - Отброшенные потоки
  - vulnerabilities.json      - Уязвимости
  - exploit_chains.json       - Цепочки эксплуатации
  - SECURITY_AUDIT_REPORT.md  - Финальный отчёт
        """
    )
    
    parser.add_argument(
        '--config',
        default='config.yaml',
        help='Путь к файлу конфигурации (по умолчанию: config.yaml)'
    )
    
    parser.add_argument(
        '--results-dir',
        default='results',
        help='Директория для сохранения результатов (по умолчанию: results)'
    )
    
    args = parser.parse_args()
    
    # Проверить существование конфига
    if not os.path.exists(args.config):
        print(f"[!] Ошибка: файл конфигурации не найден: {args.config}")
        sys.exit(1)
    
    # Запустить аудит
    orchestrator = SecurityAuditOrchestrator(args.config, args.results_dir)
    orchestrator.run_full_audit()


if __name__ == "__main__":
    main()
