# Laravel Azure Deployment Utilities (marchie/lad-utils)

## TL; DR
Artisan commands to aid deployment of Laravel applications in Microsoft Azure.

## Backstory
Deploying Laravel applications into Azure is a little bit of a dark art.

There are [guides](http://blog.bobbyallen.me/2015/06/26/configuring-and-hosting-laravel-5-x-applications-on-windows-azure/) [out there](http://stackoverflow.com/questions/32109245/deploy-laravel-to-azure) on how to do it, but for me, the end result wasn't quite what I'd hoped for.

What do I mean by that?  Well, I'm deploying my applications using [SyntaxC4's Composer Extension](https://github.com/SyntaxC4-MSFT/ComposerExtension/), which is great.  However, the standard Laravel optimization command is problematic:-

`php artisan optimize` *runs like a dog*.  I'm talking about half an hour to complete on a dual-core Azure instance, or *never* (read: over 24 hours before I killed it) finishing on a single-core instance, compared with a couple of seconds on my modest development box.  Worse still, the major time-consuming thing that the `php artisan optimize` command does is to run `composer dump-autoload -o`, which has *already been done* by the Composer Extension on Azure.

(NB: The reason seems to be something to do with the number of processes that end up running through the post install command. Composer is a PHP application, which calls the post install commands on the command line. The `php artisan optimize` post install command kicks off another PHP process, which then calls `composer dump-autoload -o` on the command line. This starts up yet another PHP process and everything just seems to grind to a halt.)

## What this package does
This package gives you an additional Artisan command, which you can call as post install commands in your `composer.json`:-

`azure:optimize-classes` extends the standard Artisan `optimize` command, except it *doesn't* call `composer dump-autoload -o`.  Note that if you have your application in debug mode, the classes will **not** be compiled.

(NB: The `composer dump-autoload -o` command is already called by the Azure Composer Extension)

(Credit to [@22media on Laracasts](https://laracasts.com/discuss/channels/servers/deploying-as-an-azure-web-app) for the legwork on this)

## Usage

You need to add the package to your `composer.json` file:

```json
{
    ...
    "require": {
        ...
        "marchie/lad-utils": "dev-master",
        ...
    },
    ...
}
```

Then, run `composer update` to pull in the package.

After the package has been pulled in, add the package's service provider into your Laravel application's `config/app.php` file:

```php
return [
    ...
    'providers' => [
        ...
        Marchie\LaravelAzureDeploymentUtilities\ServiceProvider::class,
        ...
    ]
    ...
]
```

With that done, you can use the commands in your `composer.json` `post-install-cmd`:

```json
{
    ...
    "scripts": {
        ...
        "post-install-cmd": [
            "php -r \"copy('%HOME\\site\\.env', '.env');\"",
            ...
            "php artisan azure:optimize-classes",
            ...
            "php -r \"unlink('.env');\""
        ],
        ...
    },
    ...
}
```

Note that we are copying the `.env` file into the repository first (Laravel needs this file). Then, we run the other post-install commands. Finally, we delete the copied `.env` file from the repository.