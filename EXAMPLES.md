# Security Audit Framework - Example Usage Guide

This guide provides practical examples of using the White-Box Security Audit Framework.

## Table of Contents

1. [Quick Start](#quick-start)
2. [Basic Examples](#basic-examples)
3. [Advanced Usage](#advanced-usage)
4. [Real-World Scenarios](#real-world-scenarios)
5. [Interpreting Results](#interpreting-results)
6. [Common Patterns](#common-patterns)

## Quick Start

### Installation Check

```bash
# Verify Python version (3.7+ required)
python3 --version

# Test the framework
python3 security_audit.py --help
```

### Your First Audit

```bash
# Run audit on current directory
python3 security_audit.py .

# View the results
cat security_audit_report.txt
```

## Basic Examples

### Example 1: Audit a Web Application

```bash
# Navigate to the application directory
cd /var/www/myshop

# Run the audit
python3 /path/to/security_audit.py .

# Check results
ls -la security_audit_report.*
```

### Example 2: Custom Output Location

```bash
# Save reports with custom names
python3 security_audit.py /var/www/myapp \
  -o myapp_security_audit_2025-12-25.json

# Reports will be:
# - myapp_security_audit_2025-12-25.json
# - security_audit_report.txt
```

### Example 3: Verbose Mode

```bash
# Run with detailed output
python3 security_audit.py . -v
```

## Advanced Usage

### Audit Specific Components

```bash
# Audit only the API directory
python3 security_audit.py ./api

# Audit admin panel
python3 security_audit.py ./admin

# Audit multiple directories (run separately and merge results)
python3 security_audit.py ./frontend -o frontend_audit.json
python3 security_audit.py ./backend -o backend_audit.json
```

### Scripted Audits

Create a script `run_audit.sh`:

```bash
#!/bin/bash
# run_audit.sh - Automated security audit script

APP_DIR="/var/www/myapp"
TIMESTAMP=$(date +%Y%m%d_%H%M%S)
REPORT_DIR="./audit_reports"
REPORT_FILE="${REPORT_DIR}/audit_${TIMESTAMP}.json"

# Create report directory
mkdir -p "$REPORT_DIR"

# Run audit
echo "Starting security audit at $(date)"
python3 security_audit.py "$APP_DIR" -o "$REPORT_FILE"

# Check results
if [ -f "$REPORT_FILE" ]; then
    echo "Audit complete. Report saved to: $REPORT_FILE"
    
    # Extract vulnerability count
    VULN_COUNT=$(python3 -c "import json; print(json.load(open('$REPORT_FILE'))['statistics']['vulnerabilities_found'])")
    
    echo "Vulnerabilities found: $VULN_COUNT"
    
    # Send notification (example)
    if [ "$VULN_COUNT" -gt 0 ]; then
        echo "WARNING: Vulnerabilities detected!"
        # Add notification logic here (email, Slack, etc.)
    fi
else
    echo "ERROR: Audit failed"
    exit 1
fi
```

Make it executable and run:

```bash
chmod +x run_audit.sh
./run_audit.sh
```

### CI/CD Integration

#### GitHub Actions Example

Create `.github/workflows/security-audit.yml`:

```yaml
name: Security Audit

on:
  push:
    branches: [ main, develop ]
  pull_request:
    branches: [ main ]
  schedule:
    - cron: '0 2 * * 1'  # Weekly on Monday at 2 AM

jobs:
  security-audit:
    runs-on: ubuntu-latest
    
    steps:
    - uses: actions/checkout@v3
    
    - name: Set up Python
      uses: actions/setup-python@v4
      with:
        python-version: '3.9'
    
    - name: Run Security Audit
      run: |
        python3 security_audit.py . -o audit_report.json
    
    - name: Check for vulnerabilities
      run: |
        VULNS=$(python3 -c "import json; print(json.load(open('audit_report.json'))['statistics']['vulnerabilities_found'])")
        echo "Found $VULNS vulnerabilities"
        
        if [ "$VULNS" -gt 0 ]; then
          echo "::warning::Security vulnerabilities detected!"
          exit 1
        fi
    
    - name: Upload audit report
      uses: actions/upload-artifact@v3
      if: always()
      with:
        name: security-audit-report
        path: |
          audit_report.json
          security_audit_report.txt
```

#### GitLab CI Example

Create `.gitlab-ci.yml`:

```yaml
security_audit:
  stage: test
  image: python:3.9
  script:
    - python3 security_audit.py . -o audit_report.json
    - |
      VULNS=$(python3 -c "import json; print(json.load(open('audit_report.json'))['statistics']['vulnerabilities_found'])")
      echo "Found $VULNS vulnerabilities"
      if [ "$VULNS" -gt 0 ]; then
        echo "WARNING: Security vulnerabilities detected!"
        exit 1
      fi
  artifacts:
    paths:
      - audit_report.json
      - security_audit_report.txt
    expire_in: 30 days
  only:
    - main
    - develop
```

## Real-World Scenarios

### Scenario 1: Pre-Release Security Review

```bash
# Before releasing version 2.0
cd /path/to/app

# Run comprehensive audit
python3 security_audit.py . -o release_2.0_security_audit.json

# Review the report
less security_audit_report.txt

# If vulnerabilities found:
# 1. Read each finding carefully
# 2. Validate in test environment
# 3. Fix vulnerabilities
# 4. Re-run audit
python3 security_audit.py . -o release_2.0_retest.json

# Compare results
diff <(jq '.statistics.vulnerabilities_found' release_2.0_security_audit.json) \
     <(jq '.statistics.vulnerabilities_found' release_2.0_retest.json)
```

### Scenario 2: Third-Party Code Audit

```bash
# Audit newly integrated library/module
cd /var/www/app/vendor/newmodule

# Run targeted audit
python3 /tools/security_audit.py . -o newmodule_audit.json

# Review results
cat security_audit_report.txt

# Document findings
cp security_audit_report.txt ../audit_reports/newmodule_$(date +%Y%m%d).txt
```

### Scenario 3: Post-Incident Analysis

```bash
# After security incident, audit specific component
COMPONENT="/var/www/app/payment_gateway"

# Run detailed audit
python3 security_audit.py "$COMPONENT" -o incident_audit.json

# Extract high-severity findings
python3 << EOF
import json
with open('incident_audit.json') as f:
    data = json.load(f)
    
high_vulns = [v for v in data['vulnerabilities'] if v['severity'] in ['Critical', 'High']]

print(f"High/Critical vulnerabilities: {len(high_vulns)}")
for v in high_vulns:
    print(f"  - {v['id']}: {v['class']} in {v['affected_entrypoints'][0]}")
EOF
```

### Scenario 4: Continuous Monitoring

Create a monitoring script `monitor_security.py`:

```python
#!/usr/bin/env python3
"""Continuous security monitoring script."""

import subprocess
import json
import time
from pathlib import Path
from datetime import datetime

APP_PATH = "/var/www/myapp"
BASELINE_FILE = "security_baseline.json"
ALERT_THRESHOLD = 0  # Alert on any new vulnerabilities

def run_audit():
    """Run security audit and return results."""
    timestamp = datetime.now().strftime("%Y%m%d_%H%M%S")
    output_file = f"audit_{timestamp}.json"
    
    subprocess.run([
        "python3", "security_audit.py",
        APP_PATH,
        "-o", output_file
    ])
    
    with open(output_file) as f:
        return json.load(f)

def compare_with_baseline(current, baseline_file):
    """Compare current results with baseline."""
    if not Path(baseline_file).exists():
        print("No baseline found. Creating baseline...")
        with open(baseline_file, 'w') as f:
            json.dump(current, f, indent=2)
        return False
    
    with open(baseline_file) as f:
        baseline = json.load(f)
    
    current_vulns = current['statistics']['vulnerabilities_found']
    baseline_vulns = baseline['statistics']['vulnerabilities_found']
    
    if current_vulns > baseline_vulns:
        print(f"⚠️  NEW VULNERABILITIES DETECTED!")
        print(f"   Baseline: {baseline_vulns}")
        print(f"   Current:  {current_vulns}")
        print(f"   New:      {current_vulns - baseline_vulns}")
        return True
    elif current_vulns < baseline_vulns:
        print(f"✅ VULNERABILITIES REDUCED!")
        print(f"   Baseline: {baseline_vulns}")
        print(f"   Current:  {current_vulns}")
        print(f"   Fixed:    {baseline_vulns - current_vulns}")
        return False
    else:
        print(f"ℹ️  No change in vulnerability count: {current_vulns}")
        return False

def main():
    """Main monitoring loop."""
    print("Starting security monitoring...")
    
    while True:
        print(f"\n[{datetime.now()}] Running security audit...")
        
        results = run_audit()
        alert = compare_with_baseline(results, BASELINE_FILE)
        
        if alert:
            # Add notification logic here
            print("Sending alert...")
            # send_email_alert()
            # send_slack_notification()
        
        # Wait before next check (e.g., 24 hours)
        print("Waiting 24 hours for next check...")
        time.sleep(86400)

if __name__ == "__main__":
    main()
```

## Interpreting Results

### Understanding the Report Structure

#### JSON Report Structure

```json
{
  "audit_metadata": {
    "target": ".",
    "timestamp": "2025-12-25T18:27:31.381Z",
    "framework_version": "1.0.0"
  },
  "statistics": {
    "total_entrypoints": 127,
    "total_dataflows": 211,
    "flows_eliminated": 83,
    "vulnerabilities_found": 9
  },
  "entrypoints": [...],
  "vulnerabilities": [...]
}
```

#### Reading Vulnerability Details

Each vulnerability contains:

1. **ID**: Unique identifier (e.g., VULN-001)
2. **Class**: Vulnerability type (SQL Injection, XSS, etc.)
3. **Severity**: Critical, High, Medium, or Low
4. **Affected Entrypoints**: Files containing the vulnerability
5. **Parameter**: The vulnerable input parameter
6. **Exploitation Condition**: How to exploit it
7. **Observable Impact**: What an attacker can achieve
8. **Proof Evidence**: What to look for to confirm

### Validating Findings

For each vulnerability, follow these steps:

1. **Review the exploitation condition**
   ```bash
   # From report: Parameter 'id' passed to mysqli_query without escaping
   # Affected file: api.php
   ```

2. **Locate the code**
   ```bash
   grep -n "mysqli_query" api.php
   ```

3. **Test in safe environment**
   - Set up isolated test instance
   - Attempt proof-of-concept
   - Document results

4. **Collect evidence**
   - Error messages
   - Modified responses
   - System logs
   - Network traces

### Prioritizing Fixes

#### Priority Matrix

```
Critical + Unauthenticated = FIX IMMEDIATELY
Critical + Authenticated   = FIX WITHIN 24 HOURS
High + Unauthenticated     = FIX WITHIN 48 HOURS
High + Authenticated       = FIX WITHIN 1 WEEK
Medium                     = FIX WITHIN 2 WEEKS
Low                        = FIX WHEN POSSIBLE
```

#### Example Prioritization Script

```python
#!/usr/bin/env python3
import json

with open('security_audit_report.json') as f:
    audit = json.load(f)

priority_list = []

for vuln in audit['vulnerabilities']:
    severity = vuln['severity']
    
    # Find authentication requirement from entrypoint
    entrypoint_path = vuln['affected_entrypoints'][0]
    entrypoint = next(
        (e for e in audit['entrypoints'] if e['file_path'] == entrypoint_path),
        None
    )
    
    auth = entrypoint['authentication'] if entrypoint else 'Unknown'
    
    # Calculate priority
    if severity == 'Critical' and auth in ['None', 'Unknown']:
        priority = 1
    elif severity == 'Critical':
        priority = 2
    elif severity == 'High' and auth in ['None', 'Unknown']:
        priority = 3
    elif severity == 'High':
        priority = 4
    elif severity == 'Medium':
        priority = 5
    else:
        priority = 6
    
    priority_list.append({
        'priority': priority,
        'id': vuln['id'],
        'class': vuln['class'],
        'file': entrypoint_path
    })

# Sort by priority
priority_list.sort(key=lambda x: x['priority'])

print("PRIORITIZED FIX LIST")
print("=" * 60)
for item in priority_list:
    print(f"P{item['priority']}: {item['id']} - {item['class']}")
    print(f"     File: {item['file']}")
```

## Common Patterns

### Pattern 1: SQL Injection Detection

The framework detects SQL injection when:
- User input from `$_GET`, `$_POST`, etc.
- Flows to database functions (`mysqli_query`, `execute`, etc.)
- Without proper escaping or parameterization

Example detection:
```php
// VULNERABLE
$id = $_GET['id'];
$query = "SELECT * FROM users WHERE id = $id";
mysqli_query($conn, $query);

// SAFE (would be eliminated in Phase 3)
$id = intval($_GET['id']);
$query = "SELECT * FROM users WHERE id = $id";
mysqli_query($conn, $query);
```

### Pattern 2: XSS Detection

Detected when:
- User input reaches output functions (`echo`, `print`)
- Without HTML encoding (`htmlspecialchars`, `htmlentities`)

Example:
```php
// VULNERABLE
$name = $_GET['name'];
echo "Hello, " . $name;

// SAFE
$name = $_GET['name'];
echo "Hello, " . htmlspecialchars($name, ENT_QUOTES);
```

### Pattern 3: Path Traversal Detection

Detected when:
- User input used in file operations
- Without path validation

Example:
```php
// VULNERABLE
$file = $_GET['file'];
$content = file_get_contents("uploads/" . $file);

// SAFE
$file = basename($_GET['file']);
$content = file_get_contents("uploads/" . $file);
```

## Tips and Best Practices

### 1. Regular Audits

Schedule audits:
- Before releases
- After major changes
- Monthly for active projects
- After security incidents

### 2. Baseline Maintenance

```bash
# Create baseline after fixes
python3 security_audit.py . -o baseline.json

# Compare future audits
python3 security_audit.py . -o current.json
diff <(jq -S . baseline.json) <(jq -S . current.json)
```

### 3. Documentation

Keep audit reports:
```bash
mkdir -p audit_history
cp security_audit_report.txt \
   audit_history/audit_$(date +%Y%m%d).txt
```

### 4. Team Collaboration

Share findings:
```bash
# Generate shareable report
python3 security_audit.py . -o shared_audit.json

# Create summary
echo "Security Audit Summary" > summary.txt
echo "=====================" >> summary.txt
jq -r '.statistics | to_entries | .[] | "\(.key): \(.value)"' \
   shared_audit.json >> summary.txt
```

### 5. False Positive Handling

Document false positives:
```bash
# Create exclusions list
cat > audit_exclusions.txt << EOF
# Known false positives
VULN-007: Type cast present but not detected - SAFE
VULN-012: Admin-only endpoint - LOW RISK
EOF
```

## Troubleshooting

### Issue: Too many false positives

**Solution**: The framework is conservative. Manually validate each finding.

### Issue: Missing vulnerabilities

**Solution**: The framework detects common patterns. Manual code review still recommended.

### Issue: Large codebase performance

**Solution**: 
```bash
# Audit in chunks
for dir in src/* ; do
    python3 security_audit.py "$dir" -o "audit_$(basename $dir).json"
done
```

## Conclusion

This framework provides a systematic approach to security auditing. Remember:

- ✅ Always validate findings
- ✅ Collect proof evidence
- ✅ Prioritize fixes appropriately
- ✅ Document your process
- ✅ Follow responsible disclosure

For more information, see `README_SECURITY_AUDIT.md`.
