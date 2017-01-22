# Frequently Asked Questions

## Common Errors

If you see an error like the following in the logs

```
'ErrorException' with message 'file_get_contents(/var/www/deployer/public/build/rev-manifest.json): failed to open stream: No such file or directory' in /var/www/deployer/vendor/laravel/framework/src/Illuminate/Foundation/helpers.php:343
```

or the following on the page

```
ErrorException (E_ERROR) file_get_contents(/var/www/deployer/public/build/rev-manifest.json): failed to open stream: No such file or directory
```

it means you are not using a production build. You either need to checkout the `release` branch or a specific release, or install the additional development dependencies

```shell
$ composer install
$ npm install (or yarn install)
```

and then build the assets

```shell
$ gulp
```
