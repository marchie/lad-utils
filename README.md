# Laravel Azure Deployment Utilities (marchie/lad-utils)

## TL; DR
Two Artisan commands to aid deployment of Laravel applications in Microsoft Azure.

## Backstory
Deploying Laravel applications into Azure is a little bit of a dark art.

There are [guides](http://blog.bobbyallen.me/2015/06/26/configuring-and-hosting-laravel-5-x-applications-on-windows-azure/) [http://stackoverflow.com/questions/32109245/deploy-laravel-to-azure](out there) on how to do it, but for me, the end result wasn't quite what I'd hoped for.

What do I mean by that?  Well, I'm deploying my applications using (SyntaxC4's Composer Extension)[https://github.com/SyntaxC4-MSFT/ComposerExtension/], which is great.  However, two of the standard Laravel optimization commands that I like to include in my `composer.json` file were problematic:-

`php artisan optimize` *runs like a dog*.  I'm talking about half an hour to complete on a dual-core Azure instance, or *never* (read: over 24 hours before I killed it) finishing on a single-core instance, compared with a couple of second on my modest development box.  Worse still, the major time-consuming thing that the `php artisan optimize` command does is to run `composer dump-autoload -o`, which has *already been done* by the Composer Extension on Azure.

(NB: The reason seems to be something to do with the number of processes that end up running through the post install command. Composer is a PHP application, which calls the post install commands on the command line. The `php artisan optimize` post install command kicks off another PHP process, which then calls `composer dump-autoload -o` on the command line. This starts up yet another PHP process and everything just seems to grind to a halt.)

`php artisan config:cache` isn't ideal as a post install command as standard. The deployment script bring the files into a repository folder and executes the post install commands on this folder; finally, it copies everything over to the site folder.  So, that means if you want to run `php artisan config:cache` on deployment, you either need to include your `.env` file in your repository (typically a **bad** thing) or you need to manually execute `php artisan config:cache` manually using the Kudu console.

## What this package does
This package gives you two additional Artisan commands, which you can call as post install commands in your `composer.json`:-

`azure:config-cache {dotenvpath}` takes a `.env` file from another location (e.g. `%HOME%\site\.env`) and temporarily stores it in the repository.  The standard `config:cache` command is then executed, generating the cached config in the repository with the correct values.  Then it cleans up after itself.

(NB: The Azure deployment process will then copy everything across to your site folder)

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
        Marchie\LaravelAzureDeploymentUtilities::class,
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
        "post-install-cmd": {
            ...
            "php artisan azure:optimize-classes",
            "php artisan azure:config-cache %HOME%\site\.env",
            ...
        },
        ...
    },
    ...
}
```

...

And now you can deploy changes to Azure quickly, without fear or hassle!