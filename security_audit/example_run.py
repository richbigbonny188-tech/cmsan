#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Пример запуска аудита безопасности
Демонстрирует базовое использование framework
"""

import os
import sys

# Добавить текущую директорию в путь
sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))

from audit_main import SecurityAuditOrchestrator


def main():
    """Пример запуска аудита"""
    
    print("""
╔══════════════════════════════════════════════════════════════════════════╗
║                                                                          ║
║          ПРИМЕР ЗАПУСКА АУДИТА БЕЗОПАСНОСТИ                             ║
║          Security Audit Framework для Gambio E-commerce                 ║
║                                                                          ║
╚══════════════════════════════════════════════════════════════════════════╝
    """)
    
    # Путь к конфигурации
    config_path = 'config.yaml'
    results_dir = 'results'
    
    print(f"Конфигурация: {config_path}")
    print(f"Результаты:   {results_dir}/")
    print()
    
    # Проверить наличие конфигурации
    if not os.path.exists(config_path):
        print(f"❌ Ошибка: файл конфигурации не найден: {config_path}")
        print()
        print("Пожалуйста, убедитесь, что файл config.yaml существует")
        print("и содержит корректные настройки для вашего приложения.")
        return 1
    
    try:
        # Запустить аудит
        orchestrator = SecurityAuditOrchestrator(config_path, results_dir)
        orchestrator.run_full_audit()
        
        print()
        print("✅ Аудит успешно завершён!")
        print()
        print(f"📄 Откройте отчёт: {results_dir}/SECURITY_AUDIT_REPORT.md")
        print()
        
        return 0
        
    except KeyboardInterrupt:
        print("\n\n⚠️  Аудит прерван пользователем")
        return 130
    except Exception as e:
        print(f"\n\n❌ Критическая ошибка: {e}")
        import traceback
        traceback.print_exc()
        return 1


if __name__ == "__main__":
    sys.exit(main())
