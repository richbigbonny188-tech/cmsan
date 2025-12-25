# White-Box Security Audit Framework

A comprehensive security audit framework for conducting authorized white-box security audits of web applications. This framework follows a strict 5-phase methodology to identify real, provable security vulnerabilities with factual evidence.

## Overview

This framework implements a systematic approach to security auditing that:

- **Eliminates speculation** - Only reports vulnerabilities that can be proven
- **Provides evidence** - Specifies exact proof requirements for each finding
- **Follows methodology** - Structured 5-phase analysis process
- **Focuses on facts** - No hypothetical attacks or generic advice
- **Exhaustive analysis** - Analyzes all entrypoints and data flows

## Key Principles

### GLOBAL CONSTRAINTS (STRICT)

- ❌ No speculation or hypothetical attacks
- ❌ No generic best practices or advice
- ❌ No mass scanning or automated exploitation
- ✅ Exhaustive analysis - don't stop at first finding
- ✅ Discard issues if exploitability cannot be proven
- ✅ Explicitly state if no vulnerabilities exist

## The 5-Phase Methodology

### Phase 1: Entrypoint Mapping

Enumerate and analyze ALL externally reachable entrypoints:

**A) Network / Transport**
- HTTP/HTTPS endpoints (all methods)
- Server-to-server callbacks (webhooks, IPN)
- Internal HTTP reachable via SSRF
- TLS termination / proxy-trusted headers

**B) Application Routing**
- Frontend controllers
- Admin-adjacent endpoints
- Installer/updater/maintenance endpoints
- AJAX/JSON/XHR handlers
- API-like endpoints

**C) Non-HTTP Triggered Paths**
- File system writes (uploads, backups, cache)
- Includes/requires/stream wrappers
- Cron/worker/task endpoints
- Email ingestion paths
- External services (FTP, S3, update mirrors)

**D) Client-Side Bridges**
- Stored/reflected injection reaching browsers
- Token/session exposure enabling privilege replay

### Phase 2: Full Data Flow Trace

For EACH parameter, trace:

1. **[ENTRYPOINT]** - Where data enters
2. **[SOURCE]** - The input source
3. **[TRANSFORMATIONS]** - All operations:
   - Type casting
   - Encoding/decoding
   - Concatenation
   - Filtering/validation
   - Trust assumptions
4. **[SINK]** - Where data is used
5. **[USER CONTROL PRESERVED]** - Yes/No determination

### Phase 3: Control Elimination Filter

Discard flows where user control is fully eliminated:

- Document exact line/function where control is lost
- Specify reason (type cast, whitelist, hard stop)
- Filter out safe flows

### Phase 4: Exploitability Assessment (Facts Only)

For remaining flows, document:

- **Exact vulnerability class** (SQL Injection, XSS, RCE, etc.)
- **Exact condition** that enables exploitation
- **Observable impact** (what changes, where)
- **Evidence required** to prove it (logs, response change, file created)

**NO** attack optimization, mass exploitation, or hypothetical chaining.

### Phase 5: Vulnerability Chaining (Only if Provable)

If and only if each step is provable:

```
[Entrypoint] → [Intermediate Effect] → [Final Impact]
```

Stop immediately if any link cannot be proven.

## Installation

### Requirements

- Python 3.7 or higher
- No external dependencies required (uses Python standard library)

### Setup

```bash
# Clone or download the framework
git clone <repository-url>
cd cmsan

# Make the script executable
chmod +x security_audit.py
```

## Usage

### Basic Usage

```bash
# Audit current directory
python3 security_audit.py .

# Audit specific path
python3 security_audit.py /path/to/web/application

# Specify custom output file
python3 security_audit.py . -o my_audit_report.json

# Enable verbose output
python3 security_audit.py . -v
```

### Command-Line Options

```
usage: security_audit.py [-h] [-o OUTPUT] [-v] target_path

White-Box Security Audit Framework

positional arguments:
  target_path           Path to the web application to audit

optional arguments:
  -h, --help            show this help message and exit
  -o OUTPUT, --output OUTPUT
                        Output file for JSON report (default: security_audit_report.json)
  -v, --verbose         Enable verbose output
```

## Output

The framework generates two report files:

### 1. JSON Report (`security_audit_report.json`)

Machine-readable JSON format containing:
- Audit metadata
- Statistics
- All entrypoints discovered
- All vulnerabilities with complete details

### 2. Human-Readable Report (`security_audit_report.txt`)

Formatted text report with:
- Executive summary
- Vulnerability counts by severity
- Detailed findings with proof requirements
- Exploitation conditions and impact descriptions

### Report Structure

#### If Vulnerabilities Found:

```
==================================================
EXECUTIVE SUMMARY
==================================================
Total Entrypoints Analyzed: X
Total Data Flows Traced: Y
Safe Flows Eliminated: Z
Vulnerabilities Found: N

==================================================
VULNERABILITY SUMMARY BY SEVERITY
==================================================
Critical: X
High: Y
Medium: Z

==================================================
DETAILED FINDINGS
==================================================

[VULN-001] SQL Injection
Severity: High
--------------------------------------------------
Affected Entrypoint(s):
  - api.php

Vulnerable Parameter: user_id
Source: $user_id from HTTP
Sink: mysqli_query (DB)

Transformations Applied:
  - filtering: filter_var at line 45

Exploitation Condition:
  Parameter 'user_id' is passed to mysqli_query without 
  proper SQL escaping or parameterized queries

Observable Impact:
  Database query modification, data extraction, 
  authentication bypass

Proof Evidence Required:
  SQL error messages, time-based delays, or extracted 
  data in response

Why This Matters:
  This vulnerability allows an attacker to modify database 
  queries through the HTTP transport mechanism.
```

#### If No Vulnerabilities Found:

```
==================================================
CONCLUSION
==================================================
No exploitable vulnerabilities were proven.

All analyzed data flows were either:
1. Properly sanitized/validated before reaching dangerous sinks
2. Did not reach dangerous sinks
3. Had user control fully eliminated through type casting or validation
```

## Configuration

The framework uses `security_audit_config.json` for configuration. Key sections:

### Dangerous Sinks

Functions that are potentially dangerous if reached with user input:

```json
{
  "dangerous_sinks": {
    "database": ["mysql_query", "mysqli_query", "execute"],
    "code_execution": ["eval", "exec", "system"],
    "filesystem": ["file_put_contents", "fopen"]
  }
}
```

### Sanitization Functions

Functions that eliminate or reduce user control:

```json
{
  "sanitization_functions": {
    "type_cast": ["intval", "(int)", "(float)"],
    "encoding": ["htmlspecialchars", "urlencode"],
    "escaping": ["mysqli_real_escape_string"]
  }
}
```

### Vulnerability Classes

Mapped vulnerability types with impact and proof requirements:

```json
{
  "vulnerability_classes": {
    "SQL_INJECTION": {
      "impact": "Database query modification, data extraction",
      "proof": "SQL error messages, time-based delays"
    }
  }
}
```

## Framework Features

### ✅ Comprehensive Entrypoint Detection

- Root-level PHP files (HTTP entrypoints)
- Application controllers and routing
- API and AJAX handlers
- Callback endpoints (webhooks, payment gateways)
- Cron jobs and worker scripts
- File-triggered operations

### ✅ Advanced Data Flow Analysis

- Parameter extraction from superglobals ($_GET, $_POST, $_REQUEST, etc.)
- Transformation tracking (casting, encoding, filtering, escaping)
- Dangerous sink detection (DB, FS, command execution, etc.)
- User control preservation analysis

### ✅ Smart Control Elimination

- Type casting detection (intval, floatval, etc.)
- HTML encoding validation (htmlspecialchars, htmlentities)
- SQL escaping verification (mysqli_real_escape_string)
- Whitelist and validation checks

### ✅ Evidence-Based Exploitability

- Severity assessment (Critical, High, Medium, Low)
- Exploitation condition documentation
- Observable impact description
- Specific proof requirements

### ✅ Vulnerability Chaining

- Detects provable multi-step attack chains
- XSS → Session theft → SQL injection
- File upload → Path traversal → RCE
- Only reports chains where each step is provable

## Supported Vulnerability Classes

The framework detects the following vulnerability types:

| Class | Severity | Detection Method |
|-------|----------|------------------|
| **SQL Injection** | High/Critical | Unescaped DB queries |
| **Cross-Site Scripting (XSS)** | Medium/High | Unencoded output to browser |
| **Remote Code Execution** | Critical | User input in exec/system/eval |
| **Local/Remote File Inclusion** | High | User input in include/require |
| **Path Traversal** | Medium/High | User input in file operations |
| **Code Injection** | Critical | User input in eval/create_function |
| **Insecure Deserialization** | High | User input in unserialize |
| **LDAP Injection** | Medium | User input in LDAP queries |
| **XML External Entity (XXE)** | High | User input in XML parsing |

## Example Workflow

### 1. Run the Audit

```bash
python3 security_audit.py /var/www/myapp
```

### 2. Review the Output

```
==================================================
[PHASE 1] ENTRYPOINT MAPPING
--------------------------------------------------
  Mapping HTTP entrypoints...
  Mapping application routing...
  Mapping non-HTTP entrypoints...
  Mapping API endpoints...
Found 157 entrypoints

[PHASE 2] FULL DATA FLOW TRACE
--------------------------------------------------
  Tracing data flows...
Traced 342 data flows

[PHASE 3] CONTROL ELIMINATION FILTER
--------------------------------------------------
  Filtering safe flows...
    Eliminated 298 safe flows
    Remaining potentially vulnerable flows: 44

[PHASE 4] EXPLOITABILITY ASSESSMENT
--------------------------------------------------
  Assessing exploitability...
    Found 3 exploitable vulnerabilities

[PHASE 5] VULNERABILITY CHAINING
--------------------------------------------------
  Analyzing vulnerability chains...
    Found 1 provable vulnerability chain

[REPORT GENERATION]
--------------------------------------------------
    Report saved to: security_audit_report.json
    Human-readable report: security_audit_report.txt
```

### 3. Examine the Reports

Review both JSON and text reports for:
- Complete vulnerability details
- Exploitation conditions
- Proof requirements
- Recommended evidence to collect

### 4. Validate Findings

For each vulnerability:
1. Review the exploitation condition
2. Collect the specified proof evidence
3. Document findings for responsible disclosure

## Limitations

### What This Framework Does NOT Do

- ❌ Automatically exploit vulnerabilities
- ❌ Provide penetration testing services
- ❌ Generate attack payloads
- ❌ Perform mass scanning
- ❌ Give generic security advice
- ❌ Report speculative or hypothetical issues

### What This Framework DOES Do

- ✅ Systematic code analysis
- ✅ Evidence-based vulnerability identification
- ✅ Proof requirement specification
- ✅ Exploitation condition documentation
- ✅ Impact assessment with facts

## Use Cases

### Authorized Security Audits

This framework is designed for:
- Internal security assessments
- Pre-release security reviews
- Compliance audits
- Responsible disclosure programs
- Security research (with permission)

### Legal Requirements

⚠️ **IMPORTANT**: Only use this framework on systems you own or have explicit written authorization to test.

Unauthorized security testing may be illegal in your jurisdiction.

## Extensibility

### Adding New Vulnerability Classes

Edit `security_audit_config.json`:

```json
{
  "vulnerability_classes": {
    "MY_VULN_TYPE": {
      "sink_type": "CUSTOM_SINK",
      "description": "My Custom Vulnerability",
      "impact": "Description of impact",
      "proof": "How to prove it"
    }
  }
}
```

### Adding New Dangerous Sinks

```json
{
  "dangerous_sinks": {
    "custom_category": [
      "dangerous_function_1",
      "dangerous_function_2"
    ]
  }
}
```

### Adding New Sanitization Functions

```json
{
  "sanitization_functions": {
    "custom_type": [
      "my_sanitize_function",
      "my_validation_function"
    ]
  }
}
```

## Best Practices

### Before Running the Audit

1. ✅ Obtain proper authorization
2. ✅ Review the target application structure
3. ✅ Ensure you have read access to all files
4. ✅ Backup the target if making any changes
5. ✅ Review and customize `security_audit_config.json`

### During the Audit

1. ✅ Review each phase's output
2. ✅ Investigate flagged data flows manually
3. ✅ Validate findings in a test environment
4. ✅ Document additional context

### After the Audit

1. ✅ Review both JSON and text reports
2. ✅ Validate findings before reporting
3. ✅ Collect proof evidence as specified
4. ✅ Follow responsible disclosure practices
5. ✅ Document remediation recommendations

## Troubleshooting

### Permission Errors

If you encounter permission errors:

```bash
# Ensure read access to all files
chmod +r -R /path/to/target

# Run with appropriate permissions
sudo python3 security_audit.py /path/to/target
```

### Large Codebases

For very large applications:

1. Split the audit by directory
2. Use the `-o` flag to separate reports
3. Merge reports manually if needed

### False Positives

The framework minimizes false positives by:
- Tracking all transformations
- Eliminating flows with type casting
- Requiring proof specifications

However, manual validation is always recommended.

## Contributing

Contributions are welcome! Areas for improvement:

- Additional vulnerability classes
- Enhanced data flow analysis
- Language support beyond PHP
- Additional output formats (HTML, PDF)
- Integration with CI/CD pipelines

## License

This framework is provided for authorized security testing purposes only.

## Disclaimer

This tool is provided "as is" without warranty of any kind. The authors are not responsible for any misuse or damage caused by this tool. Always obtain proper authorization before testing any system.

## Support

For issues, questions, or contributions, please refer to the repository documentation.

## Version History

### 1.0.0 (2025-12-25)
- Initial release
- 5-phase audit methodology
- PHP web application support
- JSON and text report generation
- Comprehensive entrypoint mapping
- Data flow analysis with transformation tracking
- Evidence-based vulnerability assessment
- Vulnerability chaining detection

---

**Remember**: This framework is for authorized security audits only. Always follow responsible disclosure practices and obtain proper authorization before testing any system.
