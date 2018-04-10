# AssetBundler plugin for Craft CMS 3.x

The plugin provides a console command to publish web/cpresources. This is useful in "cloud environments" like [fortrabbit's Professional Stack](https://www.fortrabbit.com/pricing-pro) or when using multiple Dynos on Heroku.

The purpose of this command is to publish AssetBundles files in a publicly accessible `/web` directory.
This happens already on-the-fly, e.g when you access Craft's control panel in the browser the first time. 
However, in load balanced environments, it happens that files do not exist on all servers after you deployed.

Craft also stores thumbnails of Volume Assets in `web/cpresources` to make them accessible in the control panel. The plugin changes this behaviour: 

* (default pattern)  web/cpresources/{hash}/thumb-{width}x{height}.{ext}?v={modified_date}
* (modified pattern) web/t/{asset_id}/thumb-{width}x{height}.{ext}?v={modified_date}

This way we can create thumbnails on-the-fly if they don't exist on a certain server. (TODO)


# Usage

Use this command in your build process before you distribute your code to all servers:
```
php craft asset-bundler/publish
```

When `composer install` is part of your deployment process anyways, you can include the command in your composer.json to run it automatically:
```
"scripts": {    
    "post-install-cmd": [
      "@php craft asset-bundler/publish"
    ]
  }
```

# How it works

* All `AssetBundle` classes that exist in the composer autoload class map get registered.
* In a `/web/cpresources.rev` file the timestamp of the latest revision is stored
* Files are located in `web/cpresources/{revision}/{hash}/file.ext`
* `{revision}` only changes if file is modified


# Edge cases

The `craft\web\AssetManager::getPublishedUrl()` method allows to publish single files that are not part of an `AssetBundle`.
As the plugin is not aware of these files, you may need to add them to a bundle.


# TODOs

* Config option to include additional classes
* Config option to exclude classes form class map 


