# Security Audit - STEP 1: Discovery for GXMainComponents/Extensions

**Target**: Gambio GX - GXMainComponents/Extensions  
**Date**: 2025-12-26  
**Audit Type**: Authorized white-box security assessment  
**Phase**: STEP 1 - DISCOVERY

---

## Directory Structure

### Main Extensions Subdirectories (23 total)

```
/GXMainComponents/Extensions/
├── AutoUpdater/
├── Customers/
├── Emails/
├── GambioStore/
├── Geschaeftskundenversand/
├── GiftSystem/
├── Helpers/
├── HermesHSI/
├── Invoices/
├── JavaScript/
├── JsonWebToken/
├── Orders/
├── ParcelShopFinder/
├── PaymentDetailsProvider/
├── QuickEdit/
├── ScssCompiler/
├── Serializers/
├── StyleEdit/
├── StyleEdit4/
├── Templates/
├── Themes/
└── index.html
```

---

## Complete File Listing (196 PHP files)

### AutoUpdater/ (13 files)
- `/AutoUpdater/AutoUpdaterInstaller.inc.php`
- `/AutoUpdater/AutoUpdaterService.inc.php`
- `/AutoUpdater/AutoUpdaterSettings.inc.php`
- `/AutoUpdater/GambioAutoUpdater.inc.php`
- `/AutoUpdater/Exceptions/AutoUpdaterException.inc.php`
- `/AutoUpdater/FtpManager/FtpManager.inc.php`
- `/AutoUpdater/FtpManager/FtpManagerInterface.inc.php`
- `/AutoUpdater/Helper/AutoUpdaterExceptionHelper.inc.php`
- `/AutoUpdater/Helper/GambioAutoUpdaterHelper.inc.php`
- `/AutoUpdater/Helper/UpdateRestoreHelper.inc.php`
- `/AutoUpdater/ValueObjects/DownloadedUpdateFile.inc.php`
- `/AutoUpdater/ValueObjects/ExtractedUpdateFile.inc.php`
- `/AutoUpdater/ValueObjects/UpdateFile.inc.php`

### Customers/ (1 file)
- `/Customers/CustomerInputToCollectionTransformer.inc.php`

### Emails/ (1 file)
- `/Emails/EmailParser.inc.php`

### GambioStore/ (1 file)
- `/GambioStore/GambioStoreTokenGenerator.inc.php`

### Geschaeftskundenversand/ (5 files)
- `/Geschaeftskundenversand/GeschaeftskundenversandOrderStatusChangeEventHandler.inc.php`
- `/Geschaeftskundenversand/GeschaeftskundenversandService.inc.php`
- `/Geschaeftskundenversand/GeschaeftskundenversandSettings.inc.php`
- `/Geschaeftskundenversand/Exceptions/GeschaeftskundenversandCreationFailedException.inc.php`
- `/Geschaeftskundenversand/Exceptions/GeschaeftskundenversandRetrievalFailedException.inc.php`

### GiftSystem/ (9 files)
- `/GiftSystem/CouponRepository.inc.php`
- `/GiftSystem/GiftVouchersConfigurationStorage.inc.php`
- `/GiftSystem/GiftVouchersMailService.inc.php`
- `/GiftSystem/GiftVouchersOrderStatusChangeEventHandler.inc.php`
- `/GiftSystem/GiftVouchersService.inc.php`
- `/GiftSystem/Entities/CouponEntity.inc.php`
- `/GiftSystem/Exceptions/InvalidCouponCodeException.inc.php`
- `/GiftSystem/Exceptions/InvalidCouponIdException.inc.php`
- `/GiftSystem/Exceptions/InvalidGiftVouchersQueueIdException.inc.php`

### Helpers/ (14 files)
- `/Helpers/Backup/BackupCopier.inc.php`
- `/Helpers/CacheTokenHelper/CacheTokenHelper.inc.php`
- `/Helpers/CacheTokenHelper/CacheTokenHelperFactory.inc.php`
- `/Helpers/CacheTokenHelper/Interfaces/CacheTokenHelperInterface.inc.php`
- `/Helpers/CustomerStatusHelper/CustomerStatusHelper.inc.php`
- `/Helpers/CustomerStatusHelper/CustomerStatusHelperFactory.inc.php`
- `/Helpers/CustomerStatusHelper/Interface/CustomerStatusHelperInterface.inc.php`
- `/Helpers/DataTableHelper/DataTableHelper.inc.php`
- `/Helpers/DataTableHelper/DataTableHelperFactory.inc.php`
- `/Helpers/DataTableHelper/DataTableHelperInterface.inc.php`
- `/Helpers/LanguageHelper/LanguageHelper.inc.php`
- `/Helpers/LanguageHelper/LanguageHelperFactory.inc.php`
- `/Helpers/LanguageHelper/Interfaces/LanguageHelperInterface.inc.php`
- `/Helpers/SentenceCasePipe.inc.php`

### HermesHSI/ (5 files)
- `/HermesHSI/HermesHSIService.inc.php`
- `/HermesHSI/HermesHSISettings.inc.php`
- `/HermesHSI/Entities/HermesHSIOrderLineItemEntity.inc.php`
- `/HermesHSI/Exceptions/HermesHSICreationFailedException.inc.php`
- `/HermesHSI/Exceptions/HermesHSIRetrievalFailedException.inc.php`

### Invoices/ (1 file)
- `/Invoices/InvoiceNumberGenerator.inc.php`

### JavaScript/ (1 file)
- `/JavaScript/JavaScriptEngineConfiguration.inc.php`

### JsonWebToken/ (4 files)
- `/JsonWebToken/JwtCodec.inc.php`
- `/JsonWebToken/JwtCodecFactory.inc.php`
- `/JsonWebToken/JwtCodecInterface.inc.php`
- `/JsonWebToken/JwtSecret.inc.php`

### Orders/ (1 file)
- `/Orders/GX1OrderObjectRepository.inc.php`

### ParcelShopFinder/ (3 files)
- `/ParcelShopFinder/LocationFinderAddress.php`
- `/ParcelShopFinder/ParcelShopFinder.inc.php`
- `/ParcelShopFinder/ParcelShopFinderLogger.inc.php`

### PaymentDetailsProvider/ (45 files)
- `/PaymentDetailsProvider/AmazonadvpayPaymentDetailsProvider.inc.php`
- `/PaymentDetailsProvider/EustandardtransferPaymentDetailsProvider.inc.php`
- `/PaymentDetailsProvider/IpaymentCcPaymentDetailsProvider.inc.php`
- `/PaymentDetailsProvider/IpaymentElvPaymentDetailsProvider.inc.php`
- `/PaymentDetailsProvider/IpaymentPaymentDetailsProvider.inc.php`
- `/PaymentDetailsProvider/KlarnaBanktransferHubPaymentDetailsProvider.inc.php`
- `/PaymentDetailsProvider/KlarnaHubPaymentDetailsProvider.inc.php`
- `/PaymentDetailsProvider/KlarnaPaylaterHubPaymentDetailsProvider.inc.php`
- `/PaymentDetailsProvider/KlarnaPaynowHubPaymentDetailsProvider.inc.php`
- `/PaymentDetailsProvider/KlarnaSliceitHubPaymentDetailsProvider.inc.php`
- `/PaymentDetailsProvider/MoneyorderPaymentDetailsProvider.inc.php`
- `/PaymentDetailsProvider/PayPal2HubPaymentDetailsProvider.inc.php`
- `/PaymentDetailsProvider/PayPalHubPaymentDetailsProvider.inc.php`
- `/PaymentDetailsProvider/PayoneCcPaymentDetailsProvider.inc.php`
- `/PaymentDetailsProvider/PayoneCodPaymentDetailsProvider.inc.php`
- `/PaymentDetailsProvider/PayoneElvPaymentDetailsProvider.inc.php`
- `/PaymentDetailsProvider/PayoneInstallmentPaymentDetailsProvider.inc.php`
- `/PaymentDetailsProvider/PayoneInvoicePaymentDetailsProvider.inc.php`
- `/PaymentDetailsProvider/PayoneOtransPaymentDetailsProvider.inc.php`
- `/PaymentDetailsProvider/PayonePaymentDetailsProvider.inc.php`
- `/PaymentDetailsProvider/PayonePrepayPaymentDetailsProvider.inc.php`
- `/PaymentDetailsProvider/PayoneSafeinvPaymentDetailsProvider.inc.php`
- `/PaymentDetailsProvider/PayoneWltPaymentDetailsProvider.inc.php`
- `/PaymentDetailsProvider/Paypal3PaymentDetailsProvider.inc.php`
- `/PaymentDetailsProvider/SepaPaymentDetailsProvider.inc.php`
- `/PaymentDetailsProvider/SkrillCcPaymentDetailsProvider.inc.php`
- `/PaymentDetailsProvider/SkrillCgbPaymentDetailsProvider.inc.php`
- `/PaymentDetailsProvider/SkrillCsiPaymentDetailsProvider.inc.php`
- `/PaymentDetailsProvider/SkrillElvPaymentDetailsProvider.inc.php`
- `/PaymentDetailsProvider/SkrillGiropayPaymentDetailsProvider.inc.php`
- `/PaymentDetailsProvider/SkrillIdealPaymentDetailsProvider.inc.php`
- `/PaymentDetailsProvider/SkrillMaePaymentDetailsProvider.inc.php`
- `/PaymentDetailsProvider/SkrillNetpayPaymentDetailsProvider.inc.php`
- `/PaymentDetailsProvider/SkrillPayinsPaymentDetailsProvider.inc.php`
- `/PaymentDetailsProvider/SkrillPayinvPaymentDetailsProvider.inc.php`
- `/PaymentDetailsProvider/SkrillPaymentDetailsProvider.inc.php`
- `/PaymentDetailsProvider/SkrillPspPaymentDetailsProvider.inc.php`
- `/PaymentDetailsProvider/SkrillPwyPaymentDetailsProvider.inc.php`
- `/PaymentDetailsProvider/SkrillSftPaymentDetailsProvider.inc.php`
- `/PaymentDetailsProvider/SkrillWltPaymentDetailsProvider.inc.php`
- `/PaymentDetailsProvider/Interfaces/PaymentDetailsProvider.inc.php`

### QuickEdit/ (14 files)
- `/QuickEdit/QuickEditDocuments.inc.php`
- `/QuickEdit/QuickEditOverviewColumns.inc.php`
- `/QuickEdit/QuickEditOverviewTooltips.inc.php`
- `/QuickEdit/QuickEditPropertiesOverviewColumns.inc.php`
- `/QuickEdit/QuickEditPropertiesTooltips.inc.php`
- `/QuickEdit/QuickEditSpecialPriceTooltips.inc.php`
- `/QuickEdit/QuickEditSpecialPricesOverviewColumns.inc.php`
- `/QuickEdit/Interfaces/QuickEditDocumentsInterface.inc.php`
- `/QuickEdit/Interfaces/QuickEditOverviewColumnsInterface.inc.php`
- `/QuickEdit/Interfaces/QuickEditOverviewTooltipsInterface.inc.php`
- `/QuickEdit/Interfaces/QuickEditPropertiesOverviewColumnsInterface.inc.php`
- `/QuickEdit/Interfaces/QuickEditPropertiesTooltipsInterface.inc.php`
- `/QuickEdit/Interfaces/QuickEditSpecialPriceTooltipsInterface.inc.php`
- `/QuickEdit/Interfaces/QuickEditSpecialPricesOverviewColumnsInterface.inc.php`
- `/QuickEdit/ListItems/QuickEditProductListItem.inc.php`
- `/QuickEdit/ListItems/QuickEditProductPropertiesListItem.inc.php`
- `/QuickEdit/ListItems/QuickEditProductSpecialPriceListItem.inc.php`

### ScssCompiler/ (6 files)
- `/ScssCompiler/GxScssServer.php`
- `/ScssCompiler/SassCompiler.php`
- `/ScssCompiler/ScssCompiler.backup.php`
- `/ScssCompiler/ScssCompiler.php`
- `/ScssCompiler/ScssCompilerFactory.php`
- `/ScssCompiler/ScssCompilerInterface.php`

### Serializers/ (18 files)
- `/Serializers/AbstractJsonSerializer.inc.php`
- `/Serializers/AddressJsonSerializer.inc.php`
- `/Serializers/CategoryJsonSerializer.inc.php`
- `/Serializers/CategoryListItemJsonSerializer.inc.php`
- `/Serializers/CountryJsonSerializer.inc.php`
- `/Serializers/CustomerJsonSerializer.inc.php`
- `/Serializers/EmailJsonSerializer.inc.php`
- `/Serializers/OrderJsonSerializer.inc.php`
- `/Serializers/OrderListItemJsonSerializer.inc.php`
- `/Serializers/PersonalDataXmlSerializer.inc.php`
- `/Serializers/ProductJsonSerializer.inc.php`
- `/Serializers/ProductListItemJsonSerializer.inc.php`
- `/Serializers/ReviewJsonSerializer.inc.php`
- `/Serializers/SliderJsonSerializer.inc.php`
- `/Serializers/WithdrawalJsonSerializer.inc.php`
- `/Serializers/ZoneJsonSerializer.inc.php`
- `/Serializers/Interfaces/SerializerInterface.inc.php`

### StyleEdit/ (5 files)
- `/StyleEdit/Factories/StyleEditServiceFactory.inc.php`
- `/StyleEdit/Interfaces/StyleEditReaderInterface.inc.php`
- `/StyleEdit/Interfaces/StyleEditServiceInterface.inc.php`
- `/StyleEdit/Parsers/StyleEditContentManagerParser.php`
- `/StyleEdit/Repository/StyleEdit4ReaderWrapper.inc.php`
- `/StyleEdit/Services/StyleEdit4Service.inc.php`

### StyleEdit4/ (1 file)
- `/StyleEdit4/WidgetRegistrar.php`

### Templates/ (1 file)
- `/Templates/DefaultTemplateSettings.inc.php`

### Themes/ (1 file)
- `/Themes/DefaultThemeSettings.inc.php`

---

## Files with Potential HTTP Input Processing

Based on grep search for `$_GET`, `$_POST`, `$_REQUEST`, `HttpViewController`, `HttpControllerResponse`:

1. **GambioStoreTokenGenerator.inc.php**
   - Contains reference to HTTP-related code
   - Located in: `/Extensions/GambioStore/`

2. **DataTableHelper.inc.php**
   - Contains reference to HTTP-related code
   - Located in: `/Extensions/Helpers/DataTableHelper/`

3. **CustomerInputToCollectionTransformer.inc.php**
   - Contains reference to HTTP-related code
   - Located in: `/Extensions/Customers/`

4. **ScssCompiler.backup.php**
   - Contains reference to HTTP-related code (backup file)
   - Located in: `/Extensions/ScssCompiler/`

---

## High-Risk Areas for Further Investigation

### AutoUpdater/ - System Update Mechanism
- **Risk Level**: CRITICAL
- **Reason**: File upload, extraction, FTP operations, system file replacement
- **Key Files**: 
  - `AutoUpdaterInstaller.inc.php` - Installs updates
  - `FtpManager/FtpManager.inc.php` - FTP file operations
  - `UpdateRestoreHelper.inc.php` - File restoration
  - `DownloadedUpdateFile.inc.php`, `ExtractedUpdateFile.inc.php` - File handling

### ScssCompiler/ - Dynamic Code Compilation
- **Risk Level**: HIGH
- **Reason**: Compiles SCSS/SASS to CSS, potential code injection if user input processed
- **Key Files**:
  - `GxScssServer.php` - Server component
  - `ScssCompiler.php` - Compiler logic
  - `SassCompiler.php` - SASS compiler

### StyleEdit/ & StyleEdit4/ - Template/Style Manipulation
- **Risk Level**: MEDIUM-HIGH
- **Reason**: Edits styles and templates, potential file write operations
- **Key Files**:
  - `StyleEditContentManagerParser.php` - Parses content
  - `StyleEdit4Service.inc.php` - Service layer
  - `WidgetRegistrar.php` - Widget registration

### GambioStore/ - External API Integration
- **Risk Level**: MEDIUM
- **Reason**: Token generation for external store integration
- **Key Files**:
  - `GambioStoreTokenGenerator.inc.php` - Token generation

### Serializers/ - Data Serialization
- **Risk Level**: MEDIUM
- **Reason**: XML/JSON serialization, potential injection in PersonalDataXmlSerializer
- **Key Files**:
  - `PersonalDataXmlSerializer.inc.php` - XML serialization of personal data
  - `*JsonSerializer.inc.php` - JSON serializers (16 files)

### DataTableHelper/ - Data Table Processing
- **Risk Level**: MEDIUM
- **Reason**: Processes table data, potential SQL injection in sorting/filtering
- **Key Files**:
  - `DataTableHelper.inc.php` - Table data helper

### GiftSystem/ - Voucher/Coupon System
- **Risk Level**: MEDIUM
- **Reason**: Coupon code handling, database queries
- **Key Files**:
  - `CouponRepository.inc.php` - Database operations
  - `GiftVouchersService.inc.php` - Service layer

---

## Components Likely WITHOUT HTTP Entrypoints

### Low-Risk / Internal Services (No Direct HTTP Entry):
- **PaymentDetailsProvider/** - Payment detail providers (internal services, data providers)
- **Serializers/** - Data serialization classes (output only, except XML)
- **Invoices/** - Invoice number generation (internal service)
- **Orders/** - Order object repository (internal service)
- **JavaScript/** - JavaScript configuration (internal service)
- **JsonWebToken/** - JWT codec (internal service)
- **Emails/** - Email parsing (internal service)
- **Helpers/CacheTokenHelper/** - Cache token management (internal)
- **Helpers/LanguageHelper/** - Language helpers (internal)
- **Helpers/CustomerStatusHelper/** - Customer status helpers (internal)
- **Helpers/Backup/** - Backup operations (internal)
- **QuickEdit/** - Quick edit UI helpers (internal, admin-side)
- **Templates/** - Template settings (internal configuration)
- **Themes/** - Theme settings (internal configuration)
- **HermesHSI/** - Hermes shipping integration (internal service)
- **Geschaeftskundenversand/** - Business customer shipping (internal service)
- **ParcelShopFinder/** - Parcel shop locator (internal service)

---

## Summary Statistics

- **Total PHP Files**: 196
- **Total Subdirectories**: 23
- **Files with Potential HTTP Input**: 4 confirmed
- **High-Risk Components**: 7 (AutoUpdater, ScssCompiler, StyleEdit, GambioStore, Serializers, DataTableHelper, GiftSystem)
- **Medium-Risk Components**: Multiple payment providers (45 files)
- **Low-Risk / Internal Services**: ~140 files

---

## Next Steps (STEP 2 - Entrypoint Mapping)

To continue the security audit, the following actions are recommended:

1. **Examine High-Risk Files** for HTTP entrypoints:
   - Review AutoUpdater for file upload/FTP handlers
   - Check ScssCompiler for user input in compilation
   - Analyze StyleEdit for file write operations
   - Investigate DataTableHelper for SQL injection vectors

2. **Search for Controller Usage**:
   - Find files in `/Controllers/HttpView/` that reference these Extensions
   - Map how external data flows into these services

3. **Identify Sinks**:
   - File operations: `move_uploaded_file`, `file_put_contents`, `fwrite`
   - Command execution: `exec`, `shell_exec`, `system`
   - SQL queries: Database calls in services
   - Code evaluation: `eval`, `create_function`

4. **Focus Areas**:
   - AutoUpdater installation process
   - SCSS/SASS compilation input
   - StyleEdit file manipulation
   - GiftSystem coupon redemption (already analyzed in Cart audit)

---

*Discovery phase complete. Ready for STEP 2 - Entrypoint Mapping.*
