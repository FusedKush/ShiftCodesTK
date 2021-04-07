const gulp = require('gulp');
var currentProcess = null;

/** Shared Gulp Plugins */
const plugins = (function () {
  let plugins = {};
      // Global Plugins
      plugins.dayjs = require('dayjs');
      plugins.newer = require('gulp-newer');
      plugins.path = require('path').posix;
      plugins.concat = require('gulp-concat');
      plugins.rename = require('gulp-rename');
      plugins.browsersync = require('browser-sync').create('ShiftCodesTK');
      plugins.spawn = require('child_process').spawn;
      plugins.kill = require('tree-kill');
      plugins.vinyl_source = require('vinyl-source-stream');
      plugins.vinyl_buffer = require('vinyl-buffer');

      /** CSS Plugins */
      plugins.css = {};
      plugins.css.sass = require('gulp-sass');
        plugins.css.sass.compiler = require('node-sass');
      plugins.css.postcss = {
        base: require('gulp-postcss'),
        plugins: {
          autoprefixer: require('autoprefixer'),
          cssnano: require('cssnano')
        }
      };
      
      /** JS Plugins */
      plugins.js = {};
      plugins.js.sourcemaps = require('gulp-sourcemaps');
      plugins.js.babel = require('gulp-babel');
      plugins.js.uglify = require('gulp-uglify-es').default;
      plugins.js.jsdoc = require('gulp-jsdoc3');
      plugins.js.watchify = require('watchify');
      plugins.js.browserify = require('browserify');

      /** HTML Plugins */
      plugins.html = {};
      plugins.html.htmlmin = require('gulp-htmlmin');

  return plugins;
})();
/** Directories & File Paths/Globs */
const files = (function () {
  let files = {};

      // Root
      (function () {
        files.root = {};
        files.root.rootdir = '.';
        files.root.src = `${files.root.rootdir}/src`,
        files.root.private = `${files.root.src}/private`;
        files.root.public = `${files.root.src}/public`;
      })();
      // CSS
      (function () {
        files.css = {};
        files.css.files = `${files.root.private}/css`;
        files.css.min = `${files.root.public}/assets/css`;
  
        files.css.sass = {};
        files.css.sass.root = `${files.css.files}/sass`;
        files.css.sass.glob = `${files.css.sass.root}/**/*.scss`;
        files.css.sass.partialsGlob = `${files.css.files}/sass/partials/**/*.scss`;
  
        files.css.css = {};
        files.css.css.files = `${files.css.files}/.css`;
        files.css.css.filesGlob = `${files.css.css.files}/**/*.css`
        files.css.css.minGlob = `${files.css.min}/**/*.css`;
      })();
      // JS
      (function () {
        files.js = {};

        files.js.root = `${files.root.private}/js`;
        files.js.files = `${files.js.root}/files`;
        files.js.filesGlob = `${files.js.files}/**/*.js`;
        files.js.generated = `${files.js.root}/.generated`;
        files.js.generatedGlob = `${files.js.generated}/**/*.js`;
        
        files.js.min = `${files.root.public}/assets/js`;
        files.js.minGlob = `${files.js.min}/**/*.js`;
        files.js.sourcemaps = `${files.js.min}/sourcemaps`;
      })();
      // PHP-HTML
      (function () {
        files.html = {};
        files.html.root = `${files.root.private}/php-html`;

        files.html.files = `${files.html.root}/files`;
        files.html.filesGlob = `${files.html.files}/**/*.php`;
        
        files.html.min = `${files.html.root}/.min`;
        files.html.minGlob = `${files.html.min}/**/*.php`;
      })();

  return files;
})();

function logEvent (message) {
  console.log(`[${plugins.dayjs().format('HH:mm:ss.SSS')}] ${message}`);
}

/** Methods for keeping files in sync */
const sync = {
  /**
   * Add a sync watcher to a file or directory
   * 
   * @param {string} src The source file or directory to watch.
   * @param {string|array} dest The destination(s) of the source file.
   * @param {"sync"|"add"|"update"|"delete"} mode The type of activity that will cause the files to sync.
   */
  addWatcher (src, dest, mode = 'sync') {
    return gulp.watch(src, { ignoreInitial: true })
             .on('all', function (event, file) {
               sync.update(src, dest, mode, event, file);
           });
  },
  /**
   * Synchronize two files 
   * 
   * @param {string} src The original file or directory.
   * @param {string|array} dest The path to the synced files or directories.
   * @param {"sync"|"add"|"update"|"delete"} mode The type of activity that should cause the files to sync.
   * @param {string} event The type of activity that occurred.
   * @param {string} file The file or directory that was updated.
   */
  async update (src, dest, mode, event, file) {
    const eventAction = (function () {
      if (event == 'add' || event == 'addDir')            { return 'Added'; }
      else if (event == 'change')                         { return 'Modified'; }
      else if (event == 'unlink' || event == 'unlinkDir') { return 'Removed'; }
    })();
    const eventDest = typeof dest == 'string'
                      ? dest
                      : dest.join(', ');

    function logUpdateEvent() {
      logEvent(`<-> Synced "${file}" ${eventAction} -> Updated "${eventDest}".`);
    };

    if (file == 'src') {
      file = src;
    }
    else {
      file = file.replace(new RegExp('\\\\', 'g'), '/');
    }

    if ([ 'add', 'addDir', 'change' ].indexOf(event) != -1 && [ 'add', 'update', 'sync'].indexOf(mode) != -1) {
      gulp.src(src)
          .pipe(plugins.newer(dest))
          .pipe(gulp.dest(dest));
      logUpdateEvent();
    }
    else if ([ 'unlink', 'unlinkDir' ].indexOf(event) != -1 && [ 'delete', 'sync' ].indexOf(mode) != -1) {
      logEvent(`[!] Files "${eventDest}" need to be manually removed.`);
      logUpdateEvent();
    }

    return Promise.resolve(true);
  }
};

/** The tasks that can be executed */
const tasks = (function () {
  let tasks = {};

      // CSS
      (function () {
        /** Tasks related to the compilation, transformation, and concatentation of SCSS & CSS files */
        tasks.css = {
          /** Compile SASS to CSS */
          sass (allFiles = false) {
            if (!allFiles) {
              return gulp.src(files.css.sass.glob)
                         .pipe(plugins.newer({
                           dest: files.css.css.files,
                           ext: '.css' 
                          }))
                         .pipe(plugins.css.sass()
                                      .on('error', plugins.css.sass.logError))
                         .pipe(gulp.dest(files.css.css.files));
            } 
            else {
              return gulp.src(files.css.sass.glob)
                         .pipe(plugins.css.sass()
                                      .on('error', plugins.css.sass.logError))
                         .pipe(gulp.dest(files.css.css.files));
            }
          },
          /** Run Post-CSS transformations */
          postCSS: (function () {
            const microTasks = {
              autoprefixer () {
                return gulp.src(files.css.css.filesGlob)
                           .pipe(plugins.newer(files.css.css.files))
                           .pipe(plugins.css.postcss.base([plugins.css.postcss.plugins.autoprefixer()]))
                           .pipe(gulp.dest(files.css.css.files));
              },
              cssnano () {
                return gulp.src(files.css.css.filesGlob)
                           .pipe(plugins.newer(files.css.min))
                           .pipe(plugins.css.postcss.base([plugins.css.postcss.plugins.cssnano()]))
                           .pipe(gulp.dest(files.css.min));
              }
            };
  
            return gulp.series(microTasks.autoprefixer, microTasks.cssnano);
          })(),
          /** Concatenate shared stylesheets */
          concat () {
            const fileName = 'shared-styles.css';
    
            return gulp.src([ `${files.css.min}/shared/global.css`, `${files.css.min}/shared/**/*.css` ])
                       .pipe(plugins.newer(`${files.css.min}/${fileName}`))
                       .pipe(plugins.concat(fileName))
                       .pipe(gulp.dest(files.css.min));
          }
        };

        /** Run all of the CSS micro tasks */
        tasks.css.mainTask = gulp.series(tasks.css.sass, tasks.css.postCSS, tasks.css.concat);
        /** Run all of the CSS micro tasks for all files */
        tasks.css.mainTaskAllFiles = gulp.series(function () { return tasks.css.sass(true); }, tasks.css.postCSS, tasks.css.concat);
      })();
      // JS
      (function () {
        const sourcemapOptions = {
          init: {
            loadMaps: true
          },
          write: function (sourceDir, destDir, filename) {
            let path = plugins.path.relative(`${destDir}`, `${files.js.sourcemaps}/${filename}`);
            let options = {
              includeContent: false,
              sourceRoot: plugins.path.relative(files.js.sourcemaps, sourceDir),
              destPath: destDir
            };
  
            return [ path, options ];
          },
          sources: function (sourceDir, sourcePath, file) {
            const newSourcePath = /\w/.test(sourceDir)
                                  ? `./${sourcePath}`.replace(sourceDir, '')
                                  : sourcePath;

            return newSourcePath;
          }
        }

        /** Tasks related to the compilation, uglification, and concatenation of JavaScript files */
        tasks.js = {
          /** Bundle Node Modules to a Javascript File */
          browserify () {
            const sourceDir = files.root.rootdir;
            const destDir = files.js.generated;

            return plugins.js.browserify(
              `${files.root.rootdir}/browserify.js`,
                {
                  standalone: 'node_modules'
                }
            )
              .bundle()
              .pipe(plugins.vinyl_source(`browserify-bundle.js`))
              .pipe(plugins.vinyl_buffer())
              .pipe(plugins.js.sourcemaps.init(sourcemapOptions.init))
              .pipe(plugins.js.sourcemaps.mapSources(function (sourcePath, file) {
                return sourcemapOptions.sources(sourceDir, sourcePath, file) 
              }))
              .pipe(plugins.js.sourcemaps.write(...sourcemapOptions.write(sourceDir, destDir, 'browserify')))
              .pipe(gulp.dest(destDir));
          },
          /** Transform JavaScript using Babel */
          babel () {
            const sourceDir = files.js.files;
            const destDir = files.js.generated;

            return gulp.src(files.js.filesGlob)
                       .pipe(plugins.newer(destDir))
                       .pipe(plugins.js.sourcemaps.init(sourcemapOptions.init))
                       .pipe(plugins.js.babel())
                       .pipe(plugins.js.sourcemaps.mapSources(function (sourcePath, file) {
                          return sourcemapOptions.sources(sourceDir, sourcePath, file) 
                        }))
                       .pipe(plugins.js.sourcemaps.write(...sourcemapOptions.write(sourceDir, destDir, 'babel')))
                       .pipe(gulp.dest(destDir))
          },
          /** Uglify JavaScript */
          uglify () {
            const sourceDir = files.js.generated;
            const destDir = files.js.min;

            return gulp.src(files.js.generatedGlob)
                       .pipe(plugins.newer(destDir))
                       .pipe(plugins.js.sourcemaps.init(sourcemapOptions.init))
                       .pipe(plugins.js.uglify())
                       .pipe(plugins.js.sourcemaps.mapSources(function (sourcePath, file) {
                          return sourcemapOptions.sources(sourceDir, sourcePath, file) 
                        }))
                       .pipe(plugins.js.sourcemaps.write(...sourcemapOptions.write(sourceDir, destDir, 'uglify')))
                       .pipe(gulp.dest(destDir));
          },
          /** Concatenate scripts */
          concat () {
            const sourceDir = files.js.min;
            const destDir = files.js.min;
            const fileName = 'shared-scripts.js';
  
            return gulp.src(
                        [
                          `${sourceDir}/shared/global.js`,
                          `${sourceDir}/shared/layers.js`,
                          `${sourceDir}/shared/**/*.js`,
                          `!${sourceDir}/shared/**/files/**/*.js`
                        ]
                      )
                      .pipe(plugins.newer(`${files.js.min}/${fileName}`))
                      .pipe(plugins.js.sourcemaps.init(sourcemapOptions.init))
                      .pipe(plugins.concat(fileName))
                      .pipe(plugins.js.sourcemaps.mapSources(function (sourcePath, file) {
                         return sourcemapOptions.sources(sourceDir, sourcePath, file) 
                        }))
                      .pipe(plugins.js.sourcemaps.write(...sourcemapOptions.write(sourceDir, destDir, 'concat')))
                      .pipe(gulp.dest(destDir));
          }
        }

        // tasks.js.browserify.instance.on('update', tasks.js.browserify.bundle);

        /** Run all of the JS micro tasks */
        tasks.js.mainTask = gulp.series(tasks.js.babel, tasks.js.uglify, tasks.js.concat);
      })();
      // HTML
      (function () {
        /** Tasks related to the minification and synchronization of HTML pages */
        tasks.html = {
          /** Minify HTML */
          minify () {
            const minifierOptions = {
              caseSensitive: true,
              collapseBooleanAttributes: true,
              collapseInlineTagWhitespace: false,
              collapseWhitespace: true,
              continueOnParseError: true,
              conversativeCollapse: true,
              decodeEntities: true,
              ignoreCustomFragments: [ /<%[\s\S]*?%>/, /<\?[\s\S]*?\?>/, /(\<\<|\>\>)/, /for(?: {0,1})\(.+\)/ ],
              minifyCSS: true,
              minifyJS: true,
              minifyURLs: true,
              processConditionalComments: true,
              processScripts: ["text/html"],
              removeAttributeQuotes: false,
              removeComments: true,
              removeEmptyAttributes: true,
              removeRedundantAttributes: false,
              removeScriptTypeAttributes: true,
              removeStyleLinkTypeAttributes: true,
              sortAttributes: true,
              sortClassName: true,
              trimCustomFragments: true,
              useShortDoctype: true
            };

            return gulp.src(files.html.filesGlob)
                       .pipe(plugins.newer(files.html.min))
                       .pipe(plugins.html.htmlmin(minifierOptions))
                       .pipe(gulp.dest(files.html.min));
          },
          /** Sync with site root */
          async sync () {
            sync.update(`${files.html.min}/pages/**/*.php`, `${files.root.public}`, 'sync', 'change', 'src');
            Promise.resolve(true);
          }
        };

        /** Run all of the HTML micro tasks */
        tasks.html.mainTask = gulp.series(tasks.html.minify, tasks.html.sync);
      })();
      // Startup
      (function () {
        /** Tasks related to the startup configration of the default task */
        tasks.startup = {
          /** Start the Browsersync session */
          async startBrowsersync () {
            /** The ports to use with Browsersync */
            const ports = {
              web: 35729,
              ui: 35730
            };
    
            plugins.browsersync.init({
              // Paths & Ports
              port: ports.web,
              ui: {
                port: ports.ui
              },
              proxy: "localhost:2600",
              // Watcher
              files: [
                `${files.root.public}/**/*`,
                `${files.root.private}/php/**/*`,
                `${files.html.min}/includes/**/*`
              ],
              // Preferences
              open: "local",
              logLevel: "warn",
              notify: false
            });
    
            logEvent('Browsersync Service Started.');
            Promise.resolve(true);
          },
          /** Register all of the default project watchers */
          async registerWatchers () {
            // All files
            gulp.watch(`${files.root.src}**/*`)
              .on('all', function (event, path) {
                const info = (function () {
                  if (event == 'add' || event == 'addDir') {
                    return { name: 'added', icon: '+' };
                  }
                  else if (event == 'change') {
                    return { name: 'modified', icon: '/' };
                  }
                  else if (event == 'unlink' || event == 'unlinkDir') {
                    return { name: 'removed', icon: '-' };
                  }
                })();

                logEvent(`${info.icon} "${path.replace(new RegExp('\\\\', 'g'), '/')}" ${info.name}`);
                return Promise.resolve(true);
              });

            // CSS
            gulp.watch([ files.css.sass.glob, `!${files.css.sass.partialsGlob}` ], exports.css);
            gulp.watch(files.css.sass.partialsGlob, tasks.css.mainTaskAllFiles);
            sync.addWatcher(files.css.sass.glob, [files.css.css.files, files.css.min], 'delete');
    
            // JS
            gulp.watch(files.js.filesGlob, exports.js);
            gulp.watch([ `${files.root.rootdir}/browserify.js`, `${files.root.rootdir}/Gulpfile.js` ], gulp.series(tasks.js.browserify, exports.js));
            sync.addWatcher(files.js.filesGlob, [files.js.files, files.js.min], 'delete');
  
            // HTML
            gulp.watch(files.html.filesGlob, exports.html);
            sync.addWatcher(files.html.filesGlob, [files.html.files, files.html.min], 'delete');

            gulp.watch(`${files.root.rootdir}/Gulpfile.js`, tasks.startup.startProcess);

            logEvent('Registered Default Watchers.');
            Promise.resolve(true);
          },
          /** Log the completion of the default startup tasks */
          async log () {
            logEvent('Startup tasks completed.');
            Promise.resolve(true);
          }
        };
        tasks.startup.mainTask = gulp.series(
          gulp.parallel(
            tasks.startup.startBrowsersync,
            tasks.startup.registerWatchers
          ),
          tasks.startup.log
        );
        
        /** Run all of the startup micro tasks */
        tasks.startup.startProcess = (cb) => {
          if (currentProcess !== null) {
            plugins.browsersync.exit();
            // currentProcess.kill();
            plugins.kill(currentProcess.pid);
            currentProcess = null;
          }

          currentProcess = plugins.spawn(
            'gulp', 
            [ 'individualBuilder' ], 
            { 
              stdio: 'inherit', 
              shell: true
            }
          );
          cb();
          // return process;
        }
      })();

  return tasks;
})();

// Register Tasks
(function () {
  for (const taskName in tasks) {
    let task = tasks[taskName];

    exports[taskName] = task.mainTask;
  }

  /** Responsible for building the main project files.
   * 
   * Invokes the following sub-tasks:
   * - `css`
   * - `js`
   * - `html`
   */
  exports.build = gulp.series(tasks.css.mainTask, tasks.js.browserify, tasks.js.mainTask, tasks.html.mainTask);
  /** A process responsible for managing the workspace while the user is working.
   * 
   * - Invokes `build` to compile the project files
   * - Starts a `browsersync` instance
   * - Adds `gulp` *Watchers* to the project files to build them appropriately while working.
   */
  exports.individualBuilder = gulp.series(exports.build, tasks.startup.mainTask);
  /** Responsible for managing the workspace while the user is working.
   * 
   * This is a wrapper for the `individualBuilder` that automatically relaunches a new `individualBuilder` whenever the `gulpfile.js` is modified.
   */
  exports.builder = tasks.startup.startProcess;
  /** The default task for Gulp, `build`. */
  exports.default = exports.build;
  // exports.jsdoc = function (cb) {
  //   const config = require(`${files.root.rootdir}/jsdoc.json`);

  //   gulp.src([ files.js.filesGlob, `${files.js.files}README.md` ], { read: false })
  //       .pipe(plugins.js.jsdoc(config, cb));
  // }
})();
