Source Code for [ShiftCodesTK](https://shiftcodestk.com), a site about SHiFT Codes for [Borderlands](https://borderlands.com).

# Building the Project Files #
ShiftCodesTK uses [npm](https://www.npmjs.com/) and [Composer](https://getcomposer.org) to manage project dependencies. [Gulp](https://gulpjs.com/) is used for building the project files.

To get started
```
npm install
composer install
```

## Building ##
Once everything has been downloaded, you can build the project files
```
gulp build
```
`build` is also the *Default Task*, and will also be run if you invoke `gulp` without providing a task.
The `css`, `js`, and `html` sub-tasks can also be invoked to only build their respective part of the project files.

## The *Builder* ##
You can also start a *builder*
```
gulp builder
```
`builder` will build the current project files, configure a [`browsersync`](https://github.com/BrowserSync/browser-sync) instance, and initialize *Gulp Watchers* to automatically build the updated files as you work.