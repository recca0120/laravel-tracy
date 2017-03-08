# Changelog

All notable changes to `laravel-tracy` will be documented in this file

## 1.8.5 - 2017-03-08

- 除了 Terminal Panel之外，其他的 Panel 的 HTML 全部最小化
- Terminal Panel 增加最小高度
- Terminal Panel 無法使用時顯示錯誤訊息
- Database Panel highlight 重構

## 1.8.4 - 2017-03-06

- 修正 DatabasePanel bug
- 修正某些 php 版本當使用 header 時第二個參數設為 false 時會出錯

## 1.8.3 - 2017-03-06

- 在 console 模式下不啟用，避免無法執行 PHPUnit

## 1.8.2 - 2017-03-06

- 使用 interface IAjaxPanel 來判別在 ajax 的情況下是否使用 Panel

## 1.8.1 - 2017-03-05

- 修正 Panel 在 ajax 下的 bug

## 1.8.0 - 2017-02-27

- 重構
- 可在 config 設定 bar 要 append 在 html 或者 body
- AuthPanel 支援 Sentinel
- AuthPanel 可以使用 setUserResolver(Closure $userResolver) 設定使用者資訊
