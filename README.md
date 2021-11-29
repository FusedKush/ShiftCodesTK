Source Code for [ShiftCodesTK](https://shiftcodestk.com), a site about SHiFT Codes for [Borderlands](https://borderlands.com).

# Building #
> While the build process is overhauled in [Version `2.0.0`](https://github.com/FusedKush/ShiftCodesTK/tree/2.0.0),
the current build process is quite a bit less straightforward.

ShiftCodesTK uses [npm](https://www.npmjs.com/) to manage Node project dependencies and [Gulp](https://gulpjs.com/) to build the project files.
- The presence of a `.node-version` file in the repository makes installation of Node using a Node Version Manager such as [nvs](https://github.com/jasongin/nvs) easy.

Once the environment is ready to use npm and Gulp, run
```sh
npm install
```

## Automatically Created Files ##
Most files required by application are automatically generated and created by the builder during the build process.

Automatically created files include the following:
- `assets/php/html/min/`
- `assets/scripts/min/`
- `assets/scripts/parsed/`
- `assets/styles/css/`
- `.php` pages and associated sub-directories at the site root.

To build all of the required files, each individual `gulp` task will need to be invoked:
```sh
gulp css
gulp js
gulp html
```

## Public Files ##
All of the files intended and needed for deployment can be separately created by the builder. 

- Public files will be created in the `public/` directory
- Ensure that the Web Server is configured to point to `public/` as the *Site Root*.

To build all of the public files, run
```sh
gulp public
```

## External Dependencies ##
Some dependencies cannot easily and/or securely be stored in a repository and must instead be provivded separately. 

- An MySQL Database with the appropriate schema must be provided and configured.
- To authenticate with the database, a `dbConfig.php` file specifying the required credentials must be provided in `assets/php/scripts/dbConfig.php` or `public/assets/php/scripts/dbConfig.php` for deployment.

# Automatic Building #
When developing locally, the builder can watch and automatically rebuild the appropriate files as you work, as well as supporting Hot-Reloading via [BrowserSync](https://github.com/BrowserSync/browser-sync).

To start a development session, invoke the `startup` task manually or call `gulp` without specifying a task:

```sh
gulp
gulp startup
```