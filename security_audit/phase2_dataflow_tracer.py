#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Фаза 2: Трассировка потока данных
Выполняет полную трассировку каждого параметра от источника до стока
"""

import os
import re
import json
import yaml
from pathlib import Path
from typing import List, Dict, Set, Any, Optional
from dataclasses import dataclass, asdict, field


@dataclass
class Transformation:
    """Трансформация данных"""
    type: str  # casting, encoding, decoding, concatenation, filtering, validation
    function: str
    line_number: int
    code_snippet: str
    eliminates_control: bool = False


@dataclass
class DataFlow:
    """Поток данных от источника до стока"""
    entrypoint: str
    parameter: str
    source: str
    transformations: List[Transformation] = field(default_factory=list)
    sink: Optional[str] = None
    sink_type: Optional[str] = None
    sink_line: int = 0
    user_control_preserved: str = "UNKNOWN"  # YES, NO, PARTIAL
    trace_path: List[str] = field(default_factory=list)


class DataFlowTracer:
    """Трассировщик потока данных"""
    
    def __init__(self, config_path: str, entrypoints_path: str):
        with open(config_path, 'r', encoding='utf-8') as f:
            self.config = yaml.safe_load(f)
        
        with open(entrypoints_path, 'r', encoding='utf-8') as f:
            self.entrypoints_data = json.load(f)
        
        self.base_path = self.config['target_application']['base_path']
        self.dataflows: List[DataFlow] = []
        self.max_depth = self.config['scan_settings']['max_trace_depth']
        
        # Загрузить опасные стоки из конфига
        self.dangerous_sinks = self.config['dangerous_sinks']
        self.sanitization_functions = self.config['sanitization_functions']
        self.user_sources = self.config['user_input_sources']
    
    def trace_all_dataflows(self) -> List[DataFlow]:
        """Трассировать все потоки данных"""
        print("[ФАЗА 2] Начало трассировки потоков данных...")
        
        entrypoints = self.entrypoints_data['entrypoints']
        total = len(entrypoints)
        
        for idx, entrypoint in enumerate(entrypoints, 1):
            print(f"  [{idx}/{total}] Трассировка {entrypoint['handler']}...")
            self._trace_entrypoint(entrypoint)
        
        print(f"[ФАЗА 2] Найдено {len(self.dataflows)} потоков данных")
        return self.dataflows
    
    def _trace_entrypoint(self, entrypoint: Dict):
        """Трассировать точку входа"""
        file_path = self.base_path + entrypoint['file_path']
        
        try:
            with open(file_path, 'r', encoding='utf-8', errors='ignore') as f:
                content = f.read()
                lines = content.split('\n')
        except Exception as e:
            print(f"    Ошибка чтения файла: {e}")
            return
        
        # Трассировать каждый параметр
        for param in entrypoint['parameters']:
            self._trace_parameter(
                entrypoint,
                param,
                content,
                lines,
                file_path
            )
    
    def _trace_parameter(
        self,
        entrypoint: Dict,
        parameter: Dict,
        content: str,
        lines: List[str],
        file_path: str
    ):
        """Трассировать один параметр"""
        param_name = parameter['name']
        source = parameter['source']
        
        # Создать начальный поток данных
        dataflow = DataFlow(
            entrypoint=entrypoint['handler'],
            parameter=param_name,
            source=f"${source}['{param_name}']",
            trace_path=[file_path]
        )
        
        # Найти все использования параметра
        variable_patterns = [
            rf'\${re.escape(param_name)}\b',
            rf'\${source}\s*\[\s*[\'"]' + re.escape(param_name) + r'[\'"]\s*\]'
        ]
        
        for line_num, line in enumerate(lines, 1):
            for pattern in variable_patterns:
                if re.search(pattern, line):
                    # Проверить на трансформации
                    self._check_transformations(line, line_num, dataflow)
                    
                    # Проверить на стоки
                    self._check_sinks(line, line_num, dataflow)
        
        # Оценить сохранение контроля пользователя
        dataflow.user_control_preserved = self._evaluate_user_control(dataflow)
        
        self.dataflows.append(dataflow)
    
    def _check_transformations(self, line: str, line_num: int, dataflow: DataFlow):
        """Проверить строку на трансформации"""
        
        # Проверка на приведение типов
        casting_patterns = [
            (r'\(int\)', 'type_cast_int', True),
            (r'\(integer\)', 'type_cast_int', True),
            (r'\(float\)', 'type_cast_float', True),
            (r'\(double\)', 'type_cast_float', True),
            (r'\(bool\)', 'type_cast_bool', True),
            (r'\(boolean\)', 'type_cast_bool', True),
            (r'intval\s*\(', 'intval', True),
            (r'floatval\s*\(', 'floatval', True),
        ]
        
        for pattern, func_name, eliminates in casting_patterns:
            if re.search(pattern, line):
                dataflow.transformations.append(Transformation(
                    type='casting',
                    function=func_name,
                    line_number=line_num,
                    code_snippet=line.strip(),
                    eliminates_control=eliminates
                ))
        
        # Проверка на санитизацию
        for sanitize_func in self.sanitization_functions:
            if sanitize_func in line:
                # Определить, полностью ли устраняет контроль
                eliminates = sanitize_func in ['intval', 'floatval', '(int)', '(float)', '(bool)']
                
                dataflow.transformations.append(Transformation(
                    type='sanitization',
                    function=sanitize_func,
                    line_number=line_num,
                    code_snippet=line.strip(),
                    eliminates_control=eliminates
                ))
        
        # Проверка на кодирование
        encoding_patterns = [
            (r'htmlspecialchars\s*\(', 'htmlspecialchars', False),
            (r'htmlentities\s*\(', 'htmlentities', False),
            (r'urlencode\s*\(', 'urlencode', False),
            (r'base64_encode\s*\(', 'base64_encode', False),
            (r'json_encode\s*\(', 'json_encode', False),
        ]
        
        for pattern, func_name, eliminates in encoding_patterns:
            if re.search(pattern, line):
                dataflow.transformations.append(Transformation(
                    type='encoding',
                    function=func_name,
                    line_number=line_num,
                    code_snippet=line.strip(),
                    eliminates_control=eliminates
                ))
        
        # Проверка на декодирование
        decoding_patterns = [
            (r'urldecode\s*\(', 'urldecode', False),
            (r'base64_decode\s*\(', 'base64_decode', False),
            (r'json_decode\s*\(', 'json_decode', False),
        ]
        
        for pattern, func_name, eliminates in decoding_patterns:
            if re.search(pattern, line):
                dataflow.transformations.append(Transformation(
                    type='decoding',
                    function=func_name,
                    line_number=line_num,
                    code_snippet=line.strip(),
                    eliminates_control=eliminates
                ))
        
        # Проверка на конкатенацию
        if re.search(r'\..*\$|"\s*\.\s*\$|\$.*\.', line):
            dataflow.transformations.append(Transformation(
                type='concatenation',
                function='string_concatenation',
                line_number=line_num,
                code_snippet=line.strip(),
                eliminates_control=False
            ))
        
        # Проверка на фильтрацию
        filtering_patterns = [
            (r'filter_var\s*\(', 'filter_var', True),
            (r'filter_input\s*\(', 'filter_input', True),
            (r'preg_replace\s*\(', 'preg_replace', False),
            (r'str_replace\s*\(', 'str_replace', False),
            (r'strip_tags\s*\(', 'strip_tags', False),
        ]
        
        for pattern, func_name, eliminates in filtering_patterns:
            if re.search(pattern, line):
                dataflow.transformations.append(Transformation(
                    type='filtering',
                    function=func_name,
                    line_number=line_num,
                    code_snippet=line.strip(),
                    eliminates_control=eliminates
                ))
    
    def _check_sinks(self, line: str, line_num: int, dataflow: DataFlow):
        """Проверить строку на опасные стоки"""
        
        # Проверить все категории стоков
        for sink_category, sink_functions in self.dangerous_sinks.items():
            for sink_func in sink_functions:
                # Создать паттерн для поиска функции
                pattern = rf'\b{re.escape(sink_func)}\s*\('
                if re.search(pattern, line):
                    # Найден сток
                    if dataflow.sink is None:  # Записать только первый сток
                        dataflow.sink = sink_func
                        dataflow.sink_type = sink_category
                        dataflow.sink_line = line_num
    
    def _evaluate_user_control(self, dataflow: DataFlow) -> str:
        """Оценить сохранение контроля пользователя"""
        
        # Если нет трансформаций, контроль сохранён
        if not dataflow.transformations:
            return "YES"
        
        # Проверить, есть ли трансформации, которые устраняют контроль
        for transform in dataflow.transformations:
            if transform.eliminates_control:
                return "NO"
        
        # Если есть санитизация, но не полная
        has_sanitization = any(
            t.type in ['sanitization', 'filtering', 'encoding']
            for t in dataflow.transformations
        )
        
        if has_sanitization:
            return "PARTIAL"
        
        return "YES"
    
    def save_results(self, output_path: str):
        """Сохранить результаты в JSON"""
        results = {
            "total_dataflows": len(self.dataflows),
            "dataflows": [asdict(df) for df in self.dataflows]
        }
        
        with open(output_path, 'w', encoding='utf-8') as f:
            json.dump(results, f, indent=2, ensure_ascii=False)
        
        print(f"[ФАЗА 2] Результаты сохранены в {output_path}")


def main():
    import argparse
    
    parser = argparse.ArgumentParser(
        description='Фаза 2: Трассировка потока данных'
    )
    parser.add_argument(
        '--config',
        default='config.yaml',
        help='Путь к файлу конфигурации'
    )
    parser.add_argument(
        '--entrypoints',
        default='results/entrypoints.json',
        help='Путь к результатам фазы 1'
    )
    parser.add_argument(
        '--output',
        default='results/dataflows.json',
        help='Путь для сохранения результатов'
    )
    
    args = parser.parse_args()
    
    # Создать директорию для результатов
    os.makedirs(os.path.dirname(args.output), exist_ok=True)
    
    # Запустить трассировку
    tracer = DataFlowTracer(args.config, args.entrypoints)
    dataflows = tracer.trace_all_dataflows()
    tracer.save_results(args.output)
    
    print(f"\n[ФАЗА 2] Завершено. Найдено потоков данных: {len(dataflows)}")


if __name__ == "__main__":
    main()
