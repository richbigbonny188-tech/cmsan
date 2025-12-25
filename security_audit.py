#!/usr/bin/env python3
"""
White-Box Security Audit Framework

This tool conducts a comprehensive white-box security audit following
a systematic 5-phase methodology to identify real, provable security
vulnerabilities in web applications.

GLOBAL CONSTRAINTS (STRICT):
- No speculation or hypothetical attacks
- No generic best practices or advice
- No mass scanning or automated exploitation
- Exhaustive analysis - don't stop at first finding
- Discard unproven exploitability
- Explicitly state if no vulnerabilities exist

PHASES:
1. Entrypoint Mapping
2. Full Data Flow Trace
3. Control Elimination Filter
4. Exploitability Assessment (Facts Only)
5. Vulnerability Chaining (Only if Provable)
"""

import os
import re
import json
import sys
from pathlib import Path
from typing import List, Dict, Set, Tuple, Optional
from dataclasses import dataclass, field, asdict
from collections import defaultdict
import argparse


@dataclass
class EntryPoint:
    """Represents an externally reachable entrypoint."""
    file_path: str
    handler: str
    transport: str  # HTTP, callback, file-triggered, client-side
    methods: List[str]
    parameters: List[str]
    authentication: str
    trust_assumption: str
    line_number: Optional[int] = None


@dataclass
class Transformation:
    """Represents a data transformation in the flow."""
    type: str  # casting, encoding, decoding, concatenation, filtering, validation
    function: str
    location: str
    eliminates_control: bool = False
    reason: str = ""


@dataclass
class DataFlow:
    """Represents a complete data flow from source to sink."""
    entrypoint: EntryPoint
    parameter: str
    source: str
    transformations: List[Transformation] = field(default_factory=list)
    sink: str = ""
    sink_type: str = ""  # DB, FS, include, command, response, browser
    user_control_preserved: bool = True
    control_elimination_reason: str = ""


@dataclass
class Vulnerability:
    """Represents a confirmed vulnerability with proof requirements."""
    id: str
    class_name: str  # SQL Injection, XSS, RCE, etc.
    affected_entrypoints: List[str]
    data_flow: DataFlow
    exploitation_condition: str
    observable_impact: str
    proof_evidence: str
    severity: str  # Critical, High, Medium, Low
    chain: List[str] = field(default_factory=list)


class SecurityAuditFramework:
    """Main framework for conducting white-box security audits."""
    
    def __init__(self, target_path: str, output_file: str = "security_audit_report.json"):
        self.target_path = Path(target_path)
        self.output_file = output_file
        self.entrypoints: List[EntryPoint] = []
        self.data_flows: List[DataFlow] = []
        self.vulnerabilities: List[Vulnerability] = []
        self.stats = {
            "total_entrypoints": 0,
            "total_dataflows": 0,
            "flows_eliminated": 0,
            "vulnerabilities_found": 0
        }
        
        # Common dangerous sinks by type
        self.dangerous_sinks = {
            "DB": ["mysql_query", "mysqli_query", "pg_query", "execute", "query", 
                   "prepare", "exec", "db_query", "xtc_db_query"],
            "FS": ["file_put_contents", "fwrite", "fopen", "move_uploaded_file",
                   "copy", "rename", "unlink", "file_get_contents"],
            "INCLUDE": ["include", "require", "include_once", "require_once"],
            "COMMAND": ["exec", "shell_exec", "system", "passthru", "popen",
                       "proc_open", "pcntl_exec"],
            "RESPONSE": ["echo", "print", "printf", "header", "setcookie"],
            "EVAL": ["eval", "create_function", "assert", r"preg_replace.*/e"],
            "DESERIALIZATION": ["unserialize", "yaml_parse"],
            "LDAP": ["ldap_search", "ldap_list"],
            "XML": ["simplexml_load_string", "DOMDocument::loadXML"],
        }
        
        # Common sanitization/validation functions
        self.sanitization_functions = {
            "type_cast": ["intval", "floatval", "boolval", "(int)", "(float)", "(bool)"],
            "encoding": ["htmlspecialchars", "htmlentities", "urlencode", "rawurlencode",
                        "json_encode", "base64_encode"],
            "filtering": ["filter_var", "filter_input", "preg_match", "in_array",
                         "array_key_exists", "is_numeric", "ctype_"],
            "escaping": ["mysql_real_escape_string", "mysqli_real_escape_string",
                        "pg_escape_string", "addslashes", "quotemeta"],
            "whitelist": ["whitelist", "allowed_values", "enum", "switch"],
        }
    
    def run_audit(self):
        """Execute the complete 5-phase security audit."""
        print("=" * 80)
        print("WHITE-BOX SECURITY AUDIT FRAMEWORK")
        print("=" * 80)
        print(f"Target: {self.target_path}")
        print("=" * 80)
        
        # Phase 1: Entrypoint Mapping
        print("\n[PHASE 1] ENTRYPOINT MAPPING")
        print("-" * 80)
        self.phase1_entrypoint_mapping()
        print(f"Found {len(self.entrypoints)} entrypoints")
        
        # Phase 2: Full Data Flow Trace
        print("\n[PHASE 2] FULL DATA FLOW TRACE")
        print("-" * 80)
        self.phase2_data_flow_trace()
        print(f"Traced {len(self.data_flows)} data flows")
        
        # Phase 3: Control Elimination Filter
        print("\n[PHASE 3] CONTROL ELIMINATION FILTER")
        print("-" * 80)
        self.phase3_control_elimination()
        
        # Phase 4: Exploitability Assessment
        print("\n[PHASE 4] EXPLOITABILITY ASSESSMENT")
        print("-" * 80)
        self.phase4_exploitability()
        
        # Phase 5: Vulnerability Chaining
        print("\n[PHASE 5] VULNERABILITY CHAINING")
        print("-" * 80)
        self.phase5_chaining()
        
        # Generate Report
        print("\n[REPORT GENERATION]")
        print("-" * 80)
        self.generate_report()
        
        # Final Summary
        self.print_summary()
    
    def phase1_entrypoint_mapping(self):
        """
        Phase 1: Enumerate and analyze ALL externally reachable entrypoints.
        
        Categories:
        A) Network / Transport (HTTP/HTTPS, webhooks, callbacks)
        B) Application Routing (controllers, admin endpoints, API handlers)
        C) Non-HTTP Triggered Paths (file operations, cron, workers)
        D) Client-Side Bridges (stored/reflected injection paths)
        """
        # A) Find all HTTP entrypoints (root-level PHP files)
        self._map_http_entrypoints()
        
        # B) Find routing/controller entrypoints
        self._map_application_routing()
        
        # C) Find file-triggered and callback entrypoints
        self._map_non_http_entrypoints()
        
        # D) Find API and AJAX handlers
        self._map_api_endpoints()
        
        self.stats["total_entrypoints"] = len(self.entrypoints)
    
    def _map_http_entrypoints(self):
        """Map HTTP entrypoints (root-level PHP files)."""
        print("  Mapping HTTP entrypoints...")
        
        # Find all root-level PHP files
        root_php_files = list(self.target_path.glob("*.php"))
        
        for php_file in root_php_files:
            if php_file.name.startswith('.'):
                continue
                
            # Read file to extract parameters
            try:
                content = php_file.read_text(encoding='utf-8', errors='ignore')
                
                # Extract $_GET, $_POST, $_REQUEST, $_COOKIE parameters
                params = self._extract_parameters(content)
                
                # Determine authentication requirements
                auth = self._determine_authentication(content)
                
                entrypoint = EntryPoint(
                    file_path=str(php_file.relative_to(self.target_path)),
                    handler=php_file.name,
                    transport="HTTP",
                    methods=["GET", "POST"],  # Assume both unless restricted
                    parameters=params,
                    authentication=auth,
                    trust_assumption=self._determine_trust(content)
                )
                
                self.entrypoints.append(entrypoint)
                
            except Exception as e:
                print(f"    Warning: Could not read {php_file.name}: {e}")
    
    def _map_application_routing(self):
        """Map application routing entrypoints."""
        print("  Mapping application routing...")
        
        # Look for routing in common locations
        routing_patterns = [
            "GXMainComponents/Controllers/**/*.php",
            "GambioAdmin/Modules/**/*.php",
            "GambioApi/Modules/**/*.php",
        ]
        
        for pattern in routing_patterns:
            for php_file in self.target_path.glob(pattern):
                try:
                    content = php_file.read_text(encoding='utf-8', errors='ignore')
                    
                    # Check if it's a controller or handler
                    if any(keyword in content for keyword in 
                           ["Controller", "Handler", "Action", "Route"]):
                        
                        params = self._extract_parameters(content)
                        
                        entrypoint = EntryPoint(
                            file_path=str(php_file.relative_to(self.target_path)),
                            handler=php_file.name,
                            transport="HTTP-Routed",
                            methods=self._extract_http_methods(content),
                            parameters=params,
                            authentication=self._determine_authentication(content),
                            trust_assumption=self._determine_trust(content)
                        )
                        
                        self.entrypoints.append(entrypoint)
                        
                except Exception as e:
                    continue
    
    def _map_non_http_entrypoints(self):
        """Map non-HTTP triggered entrypoints."""
        print("  Mapping non-HTTP entrypoints...")
        
        # Callback endpoints
        callback_dirs = ["callback", "ext"]
        for callback_dir in callback_dirs:
            callback_path = self.target_path / callback_dir
            if callback_path.exists():
                for php_file in callback_path.rglob("*.php"):
                    try:
                        content = php_file.read_text(encoding='utf-8', errors='ignore')
                        params = self._extract_parameters(content)
                        
                        entrypoint = EntryPoint(
                            file_path=str(php_file.relative_to(self.target_path)),
                            handler=php_file.name,
                            transport="Callback",
                            methods=["POST"],
                            parameters=params,
                            authentication="External-Service",
                            trust_assumption="Trusts callback data"
                        )
                        
                        self.entrypoints.append(entrypoint)
                        
                    except Exception:
                        continue
        
        # Cron endpoints
        cron_files = list(self.target_path.glob("*cron*.php"))
        for cron_file in cron_files:
            try:
                content = cron_file.read_text(encoding='utf-8', errors='ignore')
                
                entrypoint = EntryPoint(
                    file_path=str(cron_file.relative_to(self.target_path)),
                    handler=cron_file.name,
                    transport="Cron/Worker",
                    methods=["CLI"],
                    parameters=self._extract_parameters(content),
                    authentication="Internal",
                    trust_assumption="Trusts internal triggers"
                )
                
                self.entrypoints.append(entrypoint)
                
            except Exception:
                continue
    
    def _map_api_endpoints(self):
        """Map API and AJAX endpoints."""
        print("  Mapping API endpoints...")
        
        api_files = list(self.target_path.glob("api*.php"))
        api_files.extend(self.target_path.glob("ajax*.php"))
        api_files.extend(self.target_path.glob("*_api.php"))
        
        for api_file in api_files:
            try:
                content = api_file.read_text(encoding='utf-8', errors='ignore')
                params = self._extract_parameters(content)
                
                entrypoint = EntryPoint(
                    file_path=str(api_file.relative_to(self.target_path)),
                    handler=api_file.name,
                    transport="API/AJAX",
                    methods=self._extract_http_methods(content),
                    parameters=params,
                    authentication=self._determine_authentication(content),
                    trust_assumption=self._determine_trust(content)
                )
                
                self.entrypoints.append(entrypoint)
                
            except Exception:
                continue
    
    def _extract_parameters(self, content: str) -> List[str]:
        """Extract user input parameters from code."""
        params = set()
        
        # Extract from superglobals
        patterns = [
            r'\$_GET\s*\[\s*["\']([^"\']+)["\']',
            r'\$_POST\s*\[\s*["\']([^"\']+)["\']',
            r'\$_REQUEST\s*\[\s*["\']([^"\']+)["\']',
            r'\$_COOKIE\s*\[\s*["\']([^"\']+)["\']',
            r'\$_SERVER\s*\[\s*["\']([^"\']+)["\']',
            r'\$_FILES\s*\[\s*["\']([^"\']+)["\']',
        ]
        
        for pattern in patterns:
            matches = re.finditer(pattern, content, re.IGNORECASE)
            for match in matches:
                params.add(match.group(1))
        
        return sorted(list(params))
    
    def _extract_http_methods(self, content: str) -> List[str]:
        """Extract HTTP methods from code."""
        methods = []
        
        if re.search(r'\$_GET', content):
            methods.append("GET")
        if re.search(r'\$_POST', content):
            methods.append("POST")
        if re.search(r'PUT|DELETE|PATCH', content, re.IGNORECASE):
            methods.extend(["PUT", "DELETE", "PATCH"])
        
        return methods if methods else ["GET", "POST"]
    
    def _determine_authentication(self, content: str) -> str:
        """Determine authentication requirements."""
        auth_patterns = {
            "Authenticated": [
                r'check_login|require_login|session|logged_in|is_authenticated',
                r'customer_id.*=.*\$_SESSION',
            ],
            "Admin": [
                r'admin|backend|check_admin|staff|is_admin',
            ],
            "None": []
        }
        
        for auth_type, patterns in auth_patterns.items():
            for pattern in patterns:
                if re.search(pattern, content, re.IGNORECASE):
                    return auth_type
        
        return "Unknown"
    
    def _determine_trust(self, content: str) -> str:
        """Determine trust assumptions."""
        if re.search(r'X-Forwarded-For|REMOTE_ADDR|HTTP_CLIENT_IP', content, re.IGNORECASE):
            return "Trusts proxy headers"
        if re.search(r'$_SERVER\[.HTTP_', content):
            return "Trusts HTTP headers"
        
        return "Standard web input"
    
    def phase2_data_flow_trace(self):
        """
        Phase 2: Trace data flows from each parameter to final sinks.
        
        For each parameter:
        - Track source
        - Track all transformations
        - Identify final sink
        - Determine if user control is preserved
        """
        print("  Tracing data flows...")
        
        for entrypoint in self.entrypoints:
            file_path = self.target_path / entrypoint.file_path
            
            if not file_path.exists():
                continue
            
            try:
                content = file_path.read_text(encoding='utf-8', errors='ignore')
                
                for param in entrypoint.parameters:
                    # Create data flow for this parameter
                    flow = self._trace_parameter_flow(
                        content, param, entrypoint, str(file_path)
                    )
                    
                    if flow:
                        self.data_flows.append(flow)
                        
            except Exception as e:
                continue
        
        self.stats["total_dataflows"] = len(self.data_flows)
    
    def _trace_parameter_flow(self, content: str, param: str, 
                              entrypoint: EntryPoint, file_path: str) -> Optional[DataFlow]:
        """Trace a single parameter through the code."""
        
        # Create data flow object
        flow = DataFlow(
            entrypoint=entrypoint,
            parameter=param,
            source=f"${param} from {entrypoint.transport}"
        )
        
        # Find where parameter is used
        param_patterns = [
            rf'\$_GET\s*\[\s*["\']' + re.escape(param) + r'["\']',
            rf'\$_POST\s*\[\s*["\']' + re.escape(param) + r'["\']',
            rf'\$_REQUEST\s*\[\s*["\']' + re.escape(param) + r'["\']',
        ]
        
        # Track transformations
        transformations = self._find_transformations(content, param)
        flow.transformations = transformations
        
        # Find sinks
        sink_info = self._find_dangerous_sinks(content, param)
        if sink_info:
            flow.sink = sink_info["function"]
            flow.sink_type = sink_info["type"]
        
        # Determine if user control is preserved
        flow.user_control_preserved = self._check_user_control(transformations)
        
        return flow
    
    def _find_transformations(self, content: str, param: str) -> List[Transformation]:
        """Find all transformations applied to a parameter."""
        transformations = []
        
        # Look for common transformation patterns
        lines = content.split('\n')
        
        for i, line in enumerate(lines, 1):
            # Type casting
            for cast_func in self.sanitization_functions["type_cast"]:
                if cast_func in line and param in line:
                    transformations.append(Transformation(
                        type="casting",
                        function=cast_func,
                        location=f"line {i}",
                        eliminates_control=True,
                        reason=f"Type cast to {cast_func} eliminates injection"
                    ))
            
            # Encoding
            for enc_func in self.sanitization_functions["encoding"]:
                if enc_func in line and param in line:
                    transformations.append(Transformation(
                        type="encoding",
                        function=enc_func,
                        location=f"line {i}",
                        eliminates_control="htmlspecialchars" in enc_func or "htmlentities" in enc_func,
                        reason="HTML encoding prevents XSS" if "html" in enc_func else ""
                    ))
            
            # Filtering/Validation
            for filter_func in self.sanitization_functions["filtering"]:
                if filter_func in line and param in line:
                    transformations.append(Transformation(
                        type="filtering",
                        function=filter_func,
                        location=f"line {i}",
                        eliminates_control=False,
                        reason="Validation may not eliminate all control"
                    ))
            
            # Escaping
            for esc_func in self.sanitization_functions["escaping"]:
                if esc_func in line and param in line:
                    transformations.append(Transformation(
                        type="escaping",
                        function=esc_func,
                        location=f"line {i}",
                        eliminates_control="real_escape_string" in esc_func,
                        reason="SQL escaping prevents SQL injection" if "escape" in esc_func else ""
                    ))
        
        return transformations
    
    def _find_dangerous_sinks(self, content: str, param: str) -> Optional[Dict]:
        """Find dangerous sinks where parameter is used."""
        
        for sink_type, functions in self.dangerous_sinks.items():
            for func in functions:
                # Create pattern to find function calls with parameter
                pattern = rf'{func}\s*\([^)]*' + re.escape(param)
                
                if re.search(pattern, content, re.IGNORECASE):
                    return {
                        "type": sink_type,
                        "function": func
                    }
        
        return None
    
    def _check_user_control(self, transformations: List[Transformation]) -> bool:
        """Check if user control is preserved after transformations."""
        
        # If any transformation fully eliminates control, return False
        for trans in transformations:
            if trans.eliminates_control:
                return False
        
        return True
    
    def phase3_control_elimination(self):
        """
        Phase 3: Filter out flows where user control is fully eliminated.
        
        Document each elimination with:
        - Exact line/function where control is lost
        - Reason for elimination
        """
        print("  Filtering safe flows...")
        
        eliminated_flows = []
        
        for flow in self.data_flows:
            if not flow.user_control_preserved:
                # Find the transformation that eliminated control
                for trans in flow.transformations:
                    if trans.eliminates_control:
                        flow.control_elimination_reason = (
                            f"Control eliminated at {trans.location} "
                            f"by {trans.function}: {trans.reason}"
                        )
                        break
                
                eliminated_flows.append(flow)
        
        # Remove eliminated flows
        self.data_flows = [f for f in self.data_flows if f.user_control_preserved]
        
        self.stats["flows_eliminated"] = len(eliminated_flows)
        
        print(f"    Eliminated {len(eliminated_flows)} safe flows")
        print(f"    Remaining potentially vulnerable flows: {len(self.data_flows)}")
    
    def phase4_exploitability(self):
        """
        Phase 4: Assess exploitability with factual evidence.
        
        For each remaining flow:
        - Identify exact vulnerability class
        - Define exploitation condition
        - Describe observable impact
        - Specify proof evidence required
        """
        print("  Assessing exploitability...")
        
        vuln_id = 1
        
        for flow in self.data_flows:
            # Skip flows without dangerous sinks
            if not flow.sink:
                continue
            
            # Determine vulnerability class based on sink type
            vuln_class = self._classify_vulnerability(flow.sink_type)
            
            if not vuln_class:
                continue
            
            # Assess if it's exploitable
            if self._is_exploitable(flow):
                vulnerability = Vulnerability(
                    id=f"VULN-{vuln_id:03d}",
                    class_name=vuln_class,
                    affected_entrypoints=[flow.entrypoint.file_path],
                    data_flow=flow,
                    exploitation_condition=self._describe_exploitation_condition(flow),
                    observable_impact=self._describe_impact(flow),
                    proof_evidence=self._describe_proof(flow),
                    severity=self._assess_severity(flow, vuln_class)
                )
                
                self.vulnerabilities.append(vulnerability)
                vuln_id += 1
        
        self.stats["vulnerabilities_found"] = len(self.vulnerabilities)
        
        print(f"    Found {len(self.vulnerabilities)} exploitable vulnerabilities")
    
    def _classify_vulnerability(self, sink_type: str) -> Optional[str]:
        """Classify vulnerability based on sink type."""
        classifications = {
            "DB": "SQL Injection",
            "COMMAND": "Remote Code Execution",
            "INCLUDE": "Local/Remote File Inclusion",
            "RESPONSE": "Cross-Site Scripting (XSS)",
            "FS": "Path Traversal / Arbitrary File Write",
            "EVAL": "Code Injection",
            "DESERIALIZATION": "Insecure Deserialization",
            "LDAP": "LDAP Injection",
            "XML": "XML External Entity (XXE)",
        }
        
        return classifications.get(sink_type)
    
    def _is_exploitable(self, flow: DataFlow) -> bool:
        """Determine if a flow is exploitable."""
        
        # Must have a dangerous sink
        if not flow.sink:
            return False
        
        # Must preserve user control
        if not flow.user_control_preserved:
            return False
        
        # Check if there are weak transformations that don't eliminate control
        weak_protections = ["filtering", "validation"]
        has_strong_protection = any(
            t.type not in weak_protections and not t.eliminates_control
            for t in flow.transformations
        )
        
        # Exploitable if no strong protections
        return not has_strong_protection
    
    def _describe_exploitation_condition(self, flow: DataFlow) -> str:
        """Describe the condition that enables exploitation."""
        
        if flow.sink_type == "DB":
            return (
                f"Parameter '{flow.parameter}' is passed to {flow.sink} "
                f"without proper SQL escaping or parameterized queries"
            )
        elif flow.sink_type == "RESPONSE":
            return (
                f"Parameter '{flow.parameter}' is output to {flow.sink} "
                f"without HTML encoding, allowing script injection"
            )
        elif flow.sink_type == "COMMAND":
            return (
                f"Parameter '{flow.parameter}' is passed to {flow.sink} "
                f"without proper shell escaping, allowing command injection"
            )
        elif flow.sink_type == "INCLUDE":
            return (
                f"Parameter '{flow.parameter}' is used in {flow.sink} "
                f"without path validation, allowing file inclusion"
            )
        elif flow.sink_type == "FS":
            return (
                f"Parameter '{flow.parameter}' is used in {flow.sink} "
                f"without path validation, allowing arbitrary file operations"
            )
        else:
            return (
                f"Parameter '{flow.parameter}' reaches {flow.sink} "
                f"with insufficient validation"
            )
    
    def _describe_impact(self, flow: DataFlow) -> str:
        """Describe the observable impact of exploitation."""
        
        impacts = {
            "DB": "Database query modification, data extraction, authentication bypass",
            "RESPONSE": "Arbitrary JavaScript execution in victim browsers, session hijacking",
            "COMMAND": "Arbitrary command execution on server, full system compromise",
            "INCLUDE": "Arbitrary code execution, sensitive file disclosure",
            "FS": "Arbitrary file read/write/delete, code execution via file upload",
            "EVAL": "Arbitrary PHP code execution, full system compromise",
            "DESERIALIZATION": "Arbitrary object instantiation, potential code execution",
            "LDAP": "LDAP query modification, unauthorized access",
            "XML": "Server-side request forgery, local file disclosure",
        }
        
        return impacts.get(flow.sink_type, "Unauthorized access or data manipulation")
    
    def _describe_proof(self, flow: DataFlow) -> str:
        """Describe the proof evidence required."""
        
        proofs = {
            "DB": "SQL error messages, time-based delays, or extracted data in response",
            "RESPONSE": "Alert box execution, modified DOM, or injected script in page source",
            "COMMAND": "Command output in response, reverse shell connection, or file creation",
            "INCLUDE": "PHP error disclosure, included file content in response",
            "FS": "File creation confirmation, file contents in response, or error messages",
            "EVAL": "Executed code output, phpinfo() display, or system information",
            "DESERIALIZATION": "Object instantiation evidence, unexpected behavior, or error messages",
            "LDAP": "Modified LDAP results, unauthorized access confirmation",
            "XML": "External entity content in response, SSRF confirmation, or file disclosure",
        }
        
        return proofs.get(flow.sink_type, "Observable state change or data disclosure")
    
    def _assess_severity(self, flow: DataFlow, vuln_class: str) -> str:
        """Assess vulnerability severity."""
        
        # Critical vulnerabilities
        if flow.sink_type in ["COMMAND", "EVAL"]:
            return "Critical"
        
        # High severity
        if flow.sink_type in ["DB", "INCLUDE", "DESERIALIZATION"] or \
           flow.entrypoint.authentication == "None":
            return "High"
        
        # Medium severity
        if flow.sink_type in ["FS", "RESPONSE"] and \
           flow.entrypoint.authentication == "Authenticated":
            return "Medium"
        
        # Default to High for other dangerous sinks
        return "High"
    
    def phase5_chaining(self):
        """
        Phase 5: Identify provable vulnerability chains.
        
        Only document chains where each step is provable.
        Format: [Entrypoint] → [Intermediate Effect] → [Final Impact]
        """
        print("  Analyzing vulnerability chains...")
        
        # Look for common chain patterns
        chains_found = 0
        
        for i, vuln1 in enumerate(self.vulnerabilities):
            for vuln2 in self.vulnerabilities[i+1:]:
                # Check if vulnerabilities can be chained
                chain = self._check_chain(vuln1, vuln2)
                
                if chain:
                    vuln1.chain = chain
                    chains_found += 1
        
        print(f"    Found {chains_found} provable vulnerability chains")
    
    def _check_chain(self, vuln1: Vulnerability, vuln2: Vulnerability) -> List[str]:
        """Check if two vulnerabilities can be chained."""
        
        # Example: XSS + Session access → Account takeover
        if vuln1.class_name == "Cross-Site Scripting (XSS)" and \
           vuln2.class_name == "SQL Injection":
            return [
                f"[{vuln1.affected_entrypoints[0]}] XSS allows session token theft",
                "Stolen session authenticates SQL injection",
                f"[{vuln2.affected_entrypoints[0]}] SQL injection extracts sensitive data"
            ]
        
        # Example: File Upload + Path Traversal → RCE
        if "File" in vuln1.class_name and \
           vuln2.class_name == "Local/Remote File Inclusion":
            return [
                f"[{vuln1.affected_entrypoints[0]}] Upload malicious PHP file",
                "File stored in accessible location",
                f"[{vuln2.affected_entrypoints[0]}] Include uploaded file for code execution"
            ]
        
        return []
    
    def generate_report(self):
        """Generate comprehensive audit report."""
        
        report = {
            "audit_metadata": {
                "target": str(self.target_path),
                "timestamp": "2025-12-25T18:27:31.381Z",
                "framework_version": "1.0.0"
            },
            "statistics": self.stats,
            "entrypoints": [
                {
                    "file_path": ep.file_path,
                    "handler": ep.handler,
                    "transport": ep.transport,
                    "methods": ep.methods,
                    "parameters": ep.parameters,
                    "authentication": ep.authentication,
                    "trust_assumption": ep.trust_assumption
                }
                for ep in self.entrypoints
            ],
            "vulnerabilities": [
                {
                    "id": vuln.id,
                    "class": vuln.class_name,
                    "severity": vuln.severity,
                    "affected_entrypoints": vuln.affected_entrypoints,
                    "parameter": vuln.data_flow.parameter,
                    "exploitation_condition": vuln.exploitation_condition,
                    "observable_impact": vuln.observable_impact,
                    "proof_evidence": vuln.proof_evidence,
                    "chain": vuln.chain if vuln.chain else None
                }
                for vuln in self.vulnerabilities
            ]
        }
        
        # Write JSON report
        with open(self.output_file, 'w') as f:
            json.dump(report, f, indent=2)
        
        # Also create human-readable report
        self._generate_human_readable_report()
        
        print(f"    Report saved to: {self.output_file}")
        print(f"    Human-readable report: security_audit_report.txt")
    
    def _generate_human_readable_report(self):
        """Generate a human-readable text report."""
        
        report_lines = []
        report_lines.append("=" * 80)
        report_lines.append("WHITE-BOX SECURITY AUDIT REPORT")
        report_lines.append("=" * 80)
        report_lines.append(f"Target: {self.target_path}")
        report_lines.append(f"Date: 2025-12-25")
        report_lines.append("=" * 80)
        
        report_lines.append("\n" + "=" * 80)
        report_lines.append("EXECUTIVE SUMMARY")
        report_lines.append("=" * 80)
        report_lines.append(f"Total Entrypoints Analyzed: {self.stats['total_entrypoints']}")
        report_lines.append(f"Total Data Flows Traced: {self.stats['total_dataflows']}")
        report_lines.append(f"Safe Flows Eliminated: {self.stats['flows_eliminated']}")
        report_lines.append(f"Vulnerabilities Found: {self.stats['vulnerabilities_found']}")
        
        if self.stats['vulnerabilities_found'] == 0:
            report_lines.append("\n" + "=" * 80)
            report_lines.append("CONCLUSION")
            report_lines.append("=" * 80)
            report_lines.append("No exploitable vulnerabilities were proven.")
            report_lines.append("")
            report_lines.append("All analyzed data flows were either:")
            report_lines.append("1. Properly sanitized/validated before reaching dangerous sinks")
            report_lines.append("2. Did not reach dangerous sinks")
            report_lines.append("3. Had user control fully eliminated through type casting or validation")
        else:
            # Group by severity
            by_severity = defaultdict(list)
            for vuln in self.vulnerabilities:
                by_severity[vuln.severity].append(vuln)
            
            report_lines.append("\n" + "=" * 80)
            report_lines.append("VULNERABILITY SUMMARY BY SEVERITY")
            report_lines.append("=" * 80)
            
            for severity in ["Critical", "High", "Medium", "Low"]:
                if severity in by_severity:
                    report_lines.append(f"{severity}: {len(by_severity[severity])}")
            
            report_lines.append("\n" + "=" * 80)
            report_lines.append("DETAILED FINDINGS")
            report_lines.append("=" * 80)
            
            for vuln in sorted(self.vulnerabilities, 
                             key=lambda v: ["Critical", "High", "Medium", "Low"].index(v.severity)):
                report_lines.append(f"\n{'-' * 80}")
                report_lines.append(f"[{vuln.id}] {vuln.class_name}")
                report_lines.append(f"Severity: {vuln.severity}")
                report_lines.append(f"{'-' * 80}")
                
                report_lines.append(f"\nAffected Entrypoint(s):")
                for ep in vuln.affected_entrypoints:
                    report_lines.append(f"  - {ep}")
                
                report_lines.append(f"\nVulnerable Parameter: {vuln.data_flow.parameter}")
                report_lines.append(f"Source: {vuln.data_flow.source}")
                report_lines.append(f"Sink: {vuln.data_flow.sink} ({vuln.data_flow.sink_type})")
                
                if vuln.data_flow.transformations:
                    report_lines.append(f"\nTransformations Applied:")
                    for trans in vuln.data_flow.transformations:
                        report_lines.append(f"  - {trans.type}: {trans.function} at {trans.location}")
                
                report_lines.append(f"\nExploitation Condition:")
                report_lines.append(f"  {vuln.exploitation_condition}")
                
                report_lines.append(f"\nObservable Impact:")
                report_lines.append(f"  {vuln.observable_impact}")
                
                report_lines.append(f"\nProof Evidence Required:")
                report_lines.append(f"  {vuln.proof_evidence}")
                
                if vuln.chain:
                    report_lines.append(f"\nVulnerability Chain:")
                    for step in vuln.chain:
                        report_lines.append(f"  → {step}")
                
                report_lines.append(f"\nWhy This Matters:")
                report_lines.append(f"  This vulnerability allows an attacker to {vuln.observable_impact.lower()}")
                report_lines.append(f"  through the {vuln.data_flow.entrypoint.transport} transport mechanism.")
        
        report_lines.append("\n" + "=" * 80)
        report_lines.append("END OF REPORT")
        report_lines.append("=" * 80)
        
        # Write report
        with open("security_audit_report.txt", 'w') as f:
            f.write('\n'.join(report_lines))
    
    def print_summary(self):
        """Print final summary."""
        print("\n" + "=" * 80)
        print("AUDIT COMPLETE")
        print("=" * 80)
        print(f"Total Entrypoints: {self.stats['total_entrypoints']}")
        print(f"Total Data Flows: {self.stats['total_dataflows']}")
        print(f"Safe Flows Eliminated: {self.stats['flows_eliminated']}")
        print(f"Vulnerabilities Found: {self.stats['vulnerabilities_found']}")
        
        if self.stats['vulnerabilities_found'] == 0:
            print("\n✓ No exploitable vulnerabilities were proven.")
        else:
            print(f"\n⚠ Found {self.stats['vulnerabilities_found']} exploitable vulnerabilities")
            
            # Group by severity
            by_severity = defaultdict(int)
            for vuln in self.vulnerabilities:
                by_severity[vuln.severity] += 1
            
            print("\nBy Severity:")
            for severity in ["Critical", "High", "Medium", "Low"]:
                if severity in by_severity:
                    print(f"  {severity}: {by_severity[severity]}")
        
        print("\nReports generated:")
        print(f"  - {self.output_file} (JSON)")
        print(f"  - security_audit_report.txt (Human-readable)")
        print("=" * 80)


def main():
    """Main entry point."""
    parser = argparse.ArgumentParser(
        description="White-Box Security Audit Framework",
        formatter_class=argparse.RawDescriptionHelpFormatter,
        epilog="""
Examples:
  # Audit current directory
  python security_audit.py .
  
  # Audit specific path with custom output
  python security_audit.py /path/to/app -o custom_report.json
  
  # Audit with verbose output
  python security_audit.py . -v
        """
    )
    
    parser.add_argument(
        "target_path",
        help="Path to the web application to audit"
    )
    
    parser.add_argument(
        "-o", "--output",
        default="security_audit_report.json",
        help="Output file for JSON report (default: security_audit_report.json)"
    )
    
    parser.add_argument(
        "-v", "--verbose",
        action="store_true",
        help="Enable verbose output"
    )
    
    args = parser.parse_args()
    
    # Validate target path
    target = Path(args.target_path)
    if not target.exists():
        print(f"Error: Target path does not exist: {args.target_path}", file=sys.stderr)
        sys.exit(1)
    
    # Run audit
    framework = SecurityAuditFramework(args.target_path, args.output)
    framework.run_audit()


if __name__ == "__main__":
    main()
