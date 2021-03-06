<p align="center"><img width="124" height="124" src="atom.png" /></p>

# PHP Composer Lib
A Github Project template for developing PHP Libraries distributed through Composer.

**Note** If you are seeing this message and aren't on `ssgglobal/php-composer-lib` The author has not updated this readme.

## Things to think about
While there are many ways to create a PHP Library, here are a few conventions that you should consider when creating a new Composer Library.

- Your package directory and github repo name should be prefixed with `php-<your-package-name>`.
- The library should be written to support vanilla PHP and Framework specific (e.g. Laravel) bindings as an add-on.
- All packages should exist in the same namespace `StepStone\<YourPackageName>` & `StepStone\<YourPackageName>\Tests`.
- Testing is highly encouraged!
- All config files should be stored in `config/`. 99% of the time config files are only used by Laravel, if you are writing an application that has configs that are used by different frameworks/flavors of PHP you should prefix that config with the frameworks name to avoid confusion. Vanilla PHP configs (server.php) should remain the same but (laravel-server.php, symphony-server.php) configs should be prefixed accordingly.
- Make sure you register your application within Satis. If you aren't sure what that means feel free to reach out to Jess Carlos or your team lead.
- All code should exist in src and it's good practice to combine any framework specific code inside another folder (e.g. Laravel/).
- If your component isn't for laravel (or a specific framework) use only do not require any framework specific packages inside your `composer.json` file.
- Always ignore your `package.lock` file.
- Always version your releases.
- If you are adding Laravel Support make use of Laravel's [Package Discovery](https://laravel.com/docs/8.x/packages#package-discovery) feature.

## Creating a new project
There are two methods for using this template to create a new project.

### Option A: Create Repo in Github (easiest)

1. Create a [new repository](https://github.com/organizations/ssgglobal/repositories/new) in StepStone's Organization.
2. For **Repository Template** choose `ssgglobal/php-composer-lib`. Make sure `Include all branches` is unchecked, unless you know what you're doing!
3. In the repository naming section make sure `ssgglobal` is set as the **owner**.
4. Set Visibility to `Private`.
5. Click `Create Repository`.
6. Enjoy your new repo!

### Option B: Clone Repo
1. In your favorite terminal `cd /to/the/folder/you/want/to/create/your/repo`.
2. Clone a bare version of this repo `git clone --bare https://github.com/ssgglobal/php-composer-lib php-awesomeness`.
3. `cd php-awesomeness`
4. Update readme, modify any files before enabling git.
5. `git init`
6. In Github create a new repo.
7. Follow Githubs instructions for setting the newly created repo's URL as the upstream for the local git you just installed.
8. Enjoy your new repo!

## Security & Access
Where's the fun in writing a Composer Library if you don't share? At minimum you should grant access to the following users/groups.

| User/Group  | Access  |
|---|---|
| dsebot | write |
| ssgglobal/DevOps  | maintain  |
| ssgglobal/engineering-leads  | maintain |
| ssgglobal/engineers | write |