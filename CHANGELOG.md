# AssetBundler Changelog

## 0.6.0 - 2018-04-12
### Changed
- Updated dependency `craftcms/cms:^3.0.0
- No need to set @web & @webroot alias anymore, Thanks: @timkelty                                                        
- Added `--verbose` / `-v` option to display the list of changed files (default `false)  

## 0.5.0 - 2018-04-10
### Changed
- New namespace for cli commands `craft asset-bundler/*`

### Added
- New command `craft asset-bundler/cleanup`
- Generate Asset Thumbs on-the-fly if they do not exist

## 0.4.0 - 2018-03-22
### Changed
- Assets from Volumes moved out of the cpresouces folder

## 0.3.0 - 2018-03-21
### Changed
- Revision timestamp in path
- Revision stored in file
- Revision changes if at least one file was modified

## 0.2.0 - 2018-03-20
### Added
- Rebrand folder gets published as well

## 0.1.0 - 2018-03-12
### Added
- Command to collect and publish all AssetBundle assets to the cpresouces folder
