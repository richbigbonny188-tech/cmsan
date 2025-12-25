#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Фаза 3: Фильтр устранения контроля
Отбрасывает потоки, где контроль пользователя полностью устранён
"""

import os
import json
import yaml
from typing import List, Dict, Any
from dataclasses import dataclass, asdict


@dataclass
class DiscardedFlow:
    """Отброшенный поток данных"""
    entrypoint: str
    parameter: str
    source: str
    reason: str
    control_lost_at: str  # line/function where control is lost
    control_lost_line: int
    details: str


class ControlFilter:
    """Фильтр устранения контроля пользователя"""
    
    def __init__(self, config_path: str, dataflows_path: str):
        with open(config_path, 'r', encoding='utf-8') as f:
            self.config = yaml.safe_load(f)
        
        with open(dataflows_path, 'r', encoding='utf-8') as f:
            self.dataflows_data = json.load(f)
        
        self.filtered_flows: List[Dict] = []
        self.discarded_flows: List[DiscardedFlow] = []
    
    def filter_dataflows(self):
        """Фильтровать потоки данных"""
        print("[ФАЗА 3] Начало фильтрации потоков данных...")
        
        dataflows = self.dataflows_data['dataflows']
        total = len(dataflows)
        
        for idx, dataflow in enumerate(dataflows, 1):
            if self._should_keep_flow(dataflow):
                self.filtered_flows.append(dataflow)
            else:
                # Создать запись об отброшенном потоке
                discard_info = self._create_discard_record(dataflow)
                self.discarded_flows.append(discard_info)
        
        print(f"[ФАЗА 3] Сохранено потоков: {len(self.filtered_flows)}")
        print(f"[ФАЗА 3] Отброшено потоков: {len(self.discarded_flows)}")
        
        return self.filtered_flows, self.discarded_flows
    
    def _should_keep_flow(self, dataflow: Dict) -> bool:
        """Определить, следует ли сохранить поток"""
        
        # Если контроль пользователя не сохранён (NO), отбросить
        if dataflow['user_control_preserved'] == 'NO':
            return False
        
        # Если нет стока, это не представляет интереса для безопасности
        if dataflow['sink'] is None:
            return False
        
        # Проверить наличие полного устранения контроля через трансформации
        for transform in dataflow['transformations']:
            if transform['eliminates_control']:
                # Проверить, применяется ли это ПЕРЕД стоком
                if transform['line_number'] < dataflow['sink_line'] or dataflow['sink_line'] == 0:
                    return False
        
        # Сохранить поток
        return True
    
    def _create_discard_record(self, dataflow: Dict) -> DiscardedFlow:
        """Создать запись об отброшенном потоке"""
        
        # Найти причину отбрасывания
        reason = ""
        control_lost_at = ""
        control_lost_line = 0
        details = ""
        
        if dataflow['user_control_preserved'] == 'NO':
            # Найти трансформацию, которая устранила контроль
            for transform in dataflow['transformations']:
                if transform['eliminates_control']:
                    control_lost_at = transform['function']
                    control_lost_line = transform['line_number']
                    
                    if transform['type'] == 'casting':
                        reason = "type_cast"
                        details = f"Приведение типа через {transform['function']} полностью устраняет контроль пользователя"
                    elif transform['type'] == 'sanitization':
                        reason = "full_sanitization"
                        details = f"Полная санитизация через {transform['function']} устраняет возможность инъекции"
                    elif transform['type'] == 'filtering':
                        reason = "whitelist_filter"
                        details = f"Фильтрация через {transform['function']} ограничивает ввод белым списком"
                    break
        elif dataflow['sink'] is None:
            reason = "no_sink"
            details = "Параметр не достигает опасного стока"
            control_lost_at = "N/A"
        
        return DiscardedFlow(
            entrypoint=dataflow['entrypoint'],
            parameter=dataflow['parameter'],
            source=dataflow['source'],
            reason=reason,
            control_lost_at=control_lost_at,
            control_lost_line=control_lost_line,
            details=details
        )
    
    def save_results(self, filtered_path: str, discarded_path: str):
        """Сохранить результаты"""
        
        # Сохранить отфильтрованные потоки
        filtered_results = {
            "total_filtered_flows": len(self.filtered_flows),
            "filtered_flows": self.filtered_flows
        }
        
        with open(filtered_path, 'w', encoding='utf-8') as f:
            json.dump(filtered_results, f, indent=2, ensure_ascii=False)
        
        print(f"[ФАЗА 3] Отфильтрованные потоки сохранены в {filtered_path}")
        
        # Сохранить отброшенные потоки
        discarded_results = {
            "total_discarded_flows": len(self.discarded_flows),
            "discarded_flows": [asdict(df) for df in self.discarded_flows]
        }
        
        with open(discarded_path, 'w', encoding='utf-8') as f:
            json.dump(discarded_results, f, indent=2, ensure_ascii=False)
        
        print(f"[ФАЗА 3] Отброшенные потоки сохранены в {discarded_path}")


def main():
    import argparse
    
    parser = argparse.ArgumentParser(
        description='Фаза 3: Фильтр устранения контроля'
    )
    parser.add_argument(
        '--config',
        default='config.yaml',
        help='Путь к файлу конфигурации'
    )
    parser.add_argument(
        '--dataflows',
        default='results/dataflows.json',
        help='Путь к результатам фазы 2'
    )
    parser.add_argument(
        '--output-filtered',
        default='results/filtered_flows.json',
        help='Путь для сохранения отфильтрованных потоков'
    )
    parser.add_argument(
        '--output-discarded',
        default='results/discarded_flows.json',
        help='Путь для сохранения отброшенных потоков'
    )
    
    args = parser.parse_args()
    
    # Создать директорию для результатов
    os.makedirs(os.path.dirname(args.output_filtered), exist_ok=True)
    
    # Запустить фильтрацию
    filter_obj = ControlFilter(args.config, args.dataflows)
    filtered, discarded = filter_obj.filter_dataflows()
    filter_obj.save_results(args.output_filtered, args.output_discarded)
    
    print(f"\n[ФАЗА 3] Завершено.")
    print(f"  Сохранено потоков: {len(filtered)}")
    print(f"  Отброшено потоков: {len(discarded)}")


if __name__ == "__main__":
    main()
