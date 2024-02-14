# LUYA YII HELPERS

All notable changes to this project will be documented in this file. This project adheres to [Semantic Versioning](https://semver.org/).

## 1.6.0 (14. February 2024)

+ [#19](https://github.com/luyadev/yii-helpers/pull/19) Fixed issue with ordinal numbers.
+ [#21](https://github.com/luyadev/yii-helpers/pull/21) Added `ImportHelper::csvFromResource()` method to import CSV from a resource object like `fopen()`.

## 1.5.0 (26. October 2023)

+ Added new `StringHelper::toYouTubeEmbed()` function to extract YouTube links into an Embed links.

## 1.4.3 (9. August 2023)

+ Fixed bug with wrong quoted regex in StringHelper::highlightWord() method.

## 1.4.2 (25. January 2023)

+ [#16](https://github.com/luyadev/yii-helpers/pull/16) Fix PHP 8.1 compatibility bug in `ExportHelper::xlsx()`.

## 1.4.1 (18. January 2023)

+ [#15](https://github.com/luyadev/yii-helpers/pull/15) Fix PHP 8.1 compatibility bug in `ArrayHelper::searchColumns()`.

## 1.4.0 (20. October 2022)

+ [#13](https://github.com/luyadev/yii-helpers/pull/13) Added `Json::decodeSilent()` method which wont throw an exception and returns defined default value instead.

## 1.3.1 (15. October 2022)

+ [#12](https://github.com/luyadev/yii-helpers/pull/12) Fix issue with min php level. Unit Tests run from PHP 7.2 to 8.1, but the code should work also with 7.0 and 7.1. There is no min php version requirement in composer.json.

## 1.3.0 (14. October 2022)

+ [#10](https://github.com/luyadev/yii-helpers/pull/10) Add rector, phpstan and phpcsfixer, therefore raised php test version to 7.4
+ [#9](https://github.com/luyadev/yii-helpers/pull/9) Fixed issue when a word exists inside a highlight word in function `StringHelper::highlightWord()`

## 1.2.2 (20. July 2022)

+ [#8](https://github.com/luyadev/yii-helpers/pull/8) Added tests for PHP 8.1

## 1.2.1 (21. April 2022)

+ [#7](https://github.com/luyadev/yii-helpers/pull/7) Fixed security issue with csv injection for formulas and functions.

## 1.2.0 (15. June 2021)

+ [#4](https://github.com/luyadev/yii-helpers/pull/4) Added option to define the delimiter in `StringHelper::template` function.

## 1.1.1 (6. April 2021)

+ [#2](https://github.com/luyadev/yii-helpers/issues/2) Fix issue where the highlight text has been highlight to.

## 1.1.0 (17. March 2021)

+ [#1](https://github.com/luyadev/yii-helpers/pull/1) Highlight word function works with text transliteration.

## 1.0.0 (2. February 2021)

+ First stable release
