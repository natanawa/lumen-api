# Lumen PHP Framework

[![Build Status](https://travis-ci.org/laravel/lumen-framework.svg)](https://travis-ci.org/laravel/lumen-framework)
[![Total Downloads](https://poser.pugx.org/laravel/lumen-framework/d/total.svg)](https://packagist.org/packages/laravel/lumen-framework)
[![Latest Stable Version](https://poser.pugx.org/laravel/lumen-framework/v/stable.svg)](https://packagist.org/packages/laravel/lumen-framework)
[![Latest Unstable Version](https://poser.pugx.org/laravel/lumen-framework/v/unstable.svg)](https://packagist.org/packages/laravel/lumen-framework)
[![License](https://poser.pugx.org/laravel/lumen-framework/license.svg)](https://packagist.org/packages/laravel/lumen-framework)

Laravel Lumen is a stunningly fast PHP micro-framework for building web applications with expressive, elegant syntax. We believe development must be an enjoyable, creative experience to be truly fulfilling. Lumen attempts to take the pain out of development by easing common tasks used in the majority of web projects, such as routing, database abstraction, queueing, and caching.

## Official Documentation

Documentation for the framework can be found on the [Lumen website](https://lumen.laravel.com/docs).

## Security Vulnerabilities

If you discover a security vulnerability within Lumen, please send an e-mail to Taylor Otwell at taylor@laravel.com. All security vulnerabilities will be promptly addressed.

## License

The Lumen framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## API Documentation
https://documenter.getpostman.com/view/4831036/SVtN3B8v?version=latest
Postman Version v6.2.4
/postman/lumen-api.postman_collection.json


## Task List
1.  List all checklists templates
2.  Create checklist template
3.  Get checklist template by given templateId
4.  Edit Checklist Template by given templateId
5.  Delete checklist template by given {templateId}
6.  Assign bulk checklists template by given templateId to many domains
7.  Get checklist by given checklistId. Note: We can include all items in checklist with by passing include=items
8.  Update checklist by given checklistId
9.  Delete checklist by given checklistId
10. This creates a Checklist object.
11. Get list of checklists.
12. Complete item(s)
13. Incomplete item(s)
14. Get all items by given {checklistId}
15. Create item by given checklistId
16. Get checklist item by given {checklistId} and {itemId}
17. Edit Checklist Item on given {checklistId} and {itemId}
18. Delete checklist item by given {checklistId} and {itemId}
19. Update Bulk Checklist
20. Count summary of checklist’s item
21. This endpoint will get all available items.
22. Get List History        
23. Get List History By Id

## Testing

PHPUnit 7.5.12 by Sebastian Bergmann and contributors.

............................                                      28 / 28 (100%)
Time: 10.04 seconds, Memory: 18.00 MB
OK (28 tests, 70 assertions)


## USER
- testRegisterUserSection
- testLoginUserWithCredentialSection
- testLoginUserWithOutCredentialSection
- testLogOutUserWithCredentialSection

## TEMPLATE
- testGetListOfChecklistTemplateSection
- testCreateChecklistTemplateSection
- testGetChecklistTemplateSection
- testUpdateChecklistTemplateSection
- testDeleteChecklistTemplateSection
- testAsignChecklistTemplateSection
 
## CHECKLIST
- testGetChecklistSection
- testUpdateChecklistSection
- testDeleteChecklistSection
- testCreateChecklistSection
- testGetListOfChecklistSection

## ITEM
- testDisplayCompleteItemsSection
- testDisplayIncompleteItemsSection
- testDisplayAllListItemsInGivenChecklistSection
- testCreateChecklistItemSection
- testGetChecklistItemSection
- testUpdateChecklistItemSection
- testDeleteChecklistItemSection
- testUpdateBulkChecklistSection
- testSummaryItemSection
- testGetAllItemsSection

## HISTORY
- testGetListAllHistorySection
- testGetHistoryByIdSection
