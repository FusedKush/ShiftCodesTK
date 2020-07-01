/** Core Gulp Methods */
const gulp = require('gulp');
/** Shared Gulp Plugins */
const plugins = (function () {
  let plugins = {};
      // Global Plugins
      plugins.moment = require('moment');
      plugins.newer = require('gulp-newer');
      plugins.path = require('path');
      plugins.concat = require('gulp-concat');
      plugins.rename = require('gulp-rename');
      plugins.browsersync = require('browser-sync').create('ShiftCodesTK');

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
      plugins.js.babel = require('gulp-babel');
      plugins.js.uglify = require('gulp-uglify');

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
        files.root.site = './site/',
        files.root.private = `${files.root.site}private/`;
        files.root.public = `${files.root.site}public/`;
      })();
      // CSS
      (function () {
        files.css = {};
        files.css.files = `${files.root.private}css/`;
        files.css.min = `${files.root.public}assets/css/`;
  
        files.css.sass = {};
        files.css.sass.root = `${files.css.files}sass/`;
        files.css.sass.glob = `${files.css.sass.root}**/*.scss`;
  
        files.css.css = {};
        files.css.css.files = `${files.css.files}.css/`;
        files.css.css.filesGlob = `${files.css.css.files}**/*.css`
        files.css.css.minGlob = `${files.css.min}**/*.css`;
      })();
      // JS
      (function () {
        files.js = {};

        files.js.root = `${files.root.private}js/`;
        files.js.files = `${files.js.root}files/`;
        files.js.filesGlob = `${files.js.files}**/*.js`;
        files.js.generated = `${files.js.root}.generated/`;
        files.js.generatedGlob = `${files.js.generated}**/*.js`;

        files.js.min = `${files.root.public}assets/js/`;
        files.js.minGlob = `${files.js.min}**/*.js`;
      })();
      // HTML
      (function () {
        files.html = {};
        files.html.root = `${files.root.private}php/html/`;

        files.html.files = `${files.html.root}files/`;
        files.html.filesGlob = `${files.html.files}**/*.php`;
        
        files.html.min = `${files.html.root}.min/`;
        files.html.minGlob = `${files.html.min}**/*.php`;
      })();

  return files;
})();

function logEvent (message) {
  console.log(`[${plugins.moment().format('kk:mm:ss')}] ${message}`);
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
    return gulp.watch(src, { ignoreInitial: false })
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

    if ([ 'add', 'addDir', 'change' ].indexOf(event) != -1 && [ 'update', 'sync'].indexOf(mode) != -1) {
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
          sass () {
            return gulp.src(files.css.sass.glob)
                       .pipe(plugins.newer({
                         dest: files.css.css.files,
                         ext: '.css' 
                        }))
                       .pipe(plugins.css.sass()
                                    .on('error', plugins.css.sass.logError))
                       .pipe(gulp.dest(files.css.css.files));
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
    
            return gulp.src([ `${files.css.min}shared/global.css`, `${files.css.min}shared/**/*.css` ])
                       .pipe(plugins.newer(`${files.css.min}${fileName}`))
                       .pipe(plugins.concat(fileName))
                       .pipe(gulp.dest(files.css.min));
          }
        };

        /** Run all of the CSS micro tasks */
        tasks.css.mainTask = gulp.series(tasks.css.sass, tasks.css.postCSS, tasks.css.concat);
      })();
      // JS
      (function () {
        /** Tasks related to the compilation, uglification, and concatenation of JavaScript files */
        tasks.js = {
          /** Compile JavaScript using Babel */
          babel () {
            return gulp.src(files.js.filesGlob)
                       .pipe(plugins.newer(files.js.generated))
                       .pipe(plugins.js.babel())
                       .pipe(gulp.dest(files.js.generated));
          },
          /** Uglify JavaScript */
          uglify () {
            return gulp.src(files.js.generatedGlob)
                       .pipe(plugins.newer(files.js.min))
                       .pipe(plugins.js.uglify())
                       .pipe(gulp.dest(files.js.min));
          },
          /** Concatenate scripts */
          async concat () {
            // Moment
            (function () {
              const fileName = 'moment.js';
    
              gulp.src([`${files.js.min}global/libs/moment.js/files/moment.js`, `${files.js.min}global/libs/moment.js/files/moment-timezone-with-data-10-year-range.js`])
                      .pipe(plugins.newer(`${files.js.min}global/libs/moment.js/${fileName}`))
                      .pipe(plugins.concat(fileName))
                      .pipe(gulp.dest(`${files.js.min}global/libs/moment.js`));
            })();
            // Shared Scripts
            (function () {
              const fileName = 'shared-scripts.js';
    
              gulp.src([
                        `${files.js.min}functions.js`,
                        `${files.js.min}shared/global.js`,
                        `${files.js.min}shared/**/*.js`,
                      ])
                      .pipe(plugins.newer(`${files.js.min}${fileName}`))
                      .pipe(plugins.concat(fileName))
                      .pipe(gulp.dest(files.js.min));
            })();
          }
        };

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
              collapseInlineTagWhitespace: true,
              collapseWhitespace: true,
              conversativeCollapse: true,
              decodeEntities: true,
              minifyCSS: true,
              minifyJS: true,
              minifyURLs: true,
              processConditionalComments: true,
              processScripts: ["text/html"],
              removeAttributeQuotes: true,
              removeComments: true,
              removeEmptyAttributes: true,
              removeOptionalTags: true,
              removeScriptTypeAttributes: true,
              removeStyleLinkTypeAttributes: true,
              removeTagWhitespace: true,
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
            sync.update(`${files.html.min}pages/**/*.php`, `${files.root.public}`, 'sync', 'change', 'src');
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
                files.root.public,
                `${files.root.private}/php/**/*`
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
            gulp.watch(`${files.root.site}**/*`)
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
            gulp.watch(files.css.sass.glob, exports.css);
            sync.addWatcher(files.css.sass.glob, [files.css.css.files, files.css.min], 'delete');
    
            // JS
            gulp.watch(files.js.filesGlob, exports.js);
            sync.addWatcher(files.js.filesGlob, [files.js.files, files.js.min], 'delete');
  
            // HTML
            gulp.watch(files.html.filesGlob, exports.html);
            sync.addWatcher(files.html.filesGlob, [files.html.files, files.html.min], 'delete');

            logEvent('Registered Default Watchers.');
            Promise.resolve(true);
          },
          /** Log the completion of the default startup tasks */
          async log () {
            logEvent('Startup tasks completed.');
            Promise.resolve(true);
          }
        };
        
        /** Run all of the startup micro tasks */
        tasks.startup.mainTask = gulp.series(
                                   gulp.parallel(
                                     tasks.startup.startBrowsersync,
                                     tasks.startup.registerWatchers
                                   ),
                                   tasks.startup.log
                                 );
      })();

  return tasks;
})();

// Register Tasks
(function () {
  for (const taskName in tasks) {
    let task = tasks[taskName];

    exports[taskName] = task.mainTask;
  }

  exports.default = gulp.series(tasks.css.mainTask, tasks.js.mainTask, tasks.html.mainTask, tasks.startup.mainTask);
})();
