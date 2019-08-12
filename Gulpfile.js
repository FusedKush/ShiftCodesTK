var gulp = require('gulp');
var newer = require('gulp-newer');
var path = require('path');
var browsersync = require('browser-sync').create('ShiftCodesTK');
var concat = require('gulp-concat');
var rename = require('gulp-rename');

var monitoredFiles = [
  './**/*',
  '!./*.*/**',
  '!./*node_modules/**',
  '!./*public/**'
];
// Watcher Event Logger
function watchLog (cb) {
  gulp.watch(monitoredFiles)
    .on('all', function (event, path) {
      var eventName = '';
      var eventIcon = '';
      var timestamp = new Date();
      var regex = new RegExp('\\\\', 'g');

      if (event == 'add' || event == 'addDir') {
        eventName =  'added';
        eventIcon = '+';
      }
      else if (event == 'change') {
        eventName = 'modified';
        eventIcon = '/';
      }
      else if (event == 'unlink' || event == 'unlinkDir') {
        eventName = 'removed';
        eventIcon = '-';
      }

      console.log(`${eventIcon} "${path.replace(regex, '/')}" ${eventName} at ${timestamp}`);
    });
  cb();
}
// Keep files in sync
async function sync (src, dest, mode, event, file) {
  var eventName = (function () {
    if (event == 'add' || event == 'addDir')            { return 'Added'; }
    else if (event == 'change')                         { return 'Modified'; }
    else if (event == 'unlink' || event == 'unlinkDir') { return 'Removed'; }
  })();

  if (file == 'src') {
    file = src;
  }
  else {
    var regex = new RegExp('\\\\', 'g');

    file = file.replace(regex, '/');
  }

  console.log(`<-> Synced "${file}" ${eventName} -> Updating linked files "${dest}".`);

  if (mode == 'update' || mode == 'sync') {
    if (event == 'add' || event == 'addDir' || event == 'change') {
      gulp.src(src)
        .pipe(newer(dest))
        .pipe(gulp.dest(dest));
    }
  }
  if (mode == 'delete' || mode == 'sync') {
    if (event == 'unlink' || event == 'unlinkDir') {
      console.log(`! Reminder: Linked files "${dest}" can not be automatically removed at this time, they will have to be removed manually.`);
    }
  }

  return Promise.resolve();
}
function keepInSync (src, dest, mode = 'sync') {
  return gulp.watch(src, { ignoreIntial: false })
    .on('all', function (event, file) {
      sync(src, dest, mode, event, file);
    });
}
async function taskFinished (task) {
  var timestamp = new Date();

  console.log(`Task "${task}" Finished at ${timestamp}`);
  return Promise.resolve();
}
async function configured (task) {
  console.log(`Task "${task}" Configured.`);
  return Promise.resolve();
}
function watcher (dir, task) {
  var options = {
    ignoreInitial: false
  };

  return gulp.watch(dir, options, task);
}

// CSS
function setupCSS (cb) {
  var runSass, runPostcss, runConcat;
  var dirs = {};
    (function () {
      dirs.styles = `./assets/styles`;
      dirs.sass = `${dirs.styles}/sass/`;
      dirs.css = `${dirs.styles}/css`;
      dirs.cssFiles = `${dirs.css}/files/`;
      dirs.cssMin = `${dirs.css}/min/`;
      dirs.sassGlob = `${dirs.sass}**/*.scss`;
      dirs.fileGlob = `${dirs.cssFiles}**/*.css`;
      dirs.minGlob = `${dirs.cssMin}**/*.min.css`;
    })();

  // SASS
  (function () {
    var sass = require('gulp-sass');
    sass.compiler = require('node-sass');

    runSass = function () {
      return gulp.src(dirs.sassGlob)
        .pipe(newer(dirs.cssFiles, { ext: '.css' }))
        .pipe(sass().on('error', sass.logError))
        .pipe(gulp.dest(dirs.cssFiles));
    }
  })();

  // PostCSS
  (function () {
    var postcss = require('gulp-postcss');
    var plugins = {
      autoprefixer: require('autoprefixer'),
      cssnano: require('cssnano')
    };
    var tasks = {
      autoprefixer: function () {
        return gulp.src(dirs.fileGlob)
          .pipe(newer(dirs.cssFiles))
          .pipe(postcss([plugins.autoprefixer()]))
          .pipe(gulp.dest(dirs.cssFiles));
      },
      cssnano: function () {
        return gulp.src(dirs.fileGlob)
          .pipe(newer(dirs.cssMin))
          .pipe(postcss([plugins.cssnano()]))
          .pipe(rename({extname: '.min.css'}))
          .pipe(gulp.dest(dirs.cssMin));
      }
    }

    runPostcss = gulp.series(tasks.autoprefixer, tasks.cssnano);
  })();
  // Concat
  (function () {
    var sharedName = 'shared-styles.min.css';

    runConcat = function () {
      return gulp.src([`${dirs.cssMin}shared/global.min.css`, dirs.minGlob])
        .pipe(newer(`${dirs.cssMin}/${sharedName}`))
        .pipe(concat(sharedName))
        .pipe(gulp.dest(dirs.cssMin));
    }
  })();
  // Task finishied
  function finished (cb) {
    taskFinished('CSS');
    cb();
  }

  exports.css = gulp.series(runSass, runPostcss, runConcat, finished);
  // Monitor CSS
  watcher(dirs.sassGlob, exports.css);
  // Sync
  keepInSync(dirs.sassGlob, [dirs.cssFiles, dirs.cssMin], 'delete');
  configured('CSS');
  cb();
}
// Javscript
function setupJS (cb) {
  var runBabel, runUglifier, runConcat;
  var dirs = {};
    (function () {
      dirs.scripts = './assets/scripts';
      dirs.files = `${dirs.scripts}/files/`;
      dirs.parsed = `${dirs.scripts}/parsed/`;
      dirs.min = `${dirs.scripts}/min/`;
      dirs.fileGlob = `${dirs.files}**/*.js`;
      dirs.parsedGlob = `${dirs.parsed}**/*.js`;
      dirs.minGlob = `${dirs.min}**/*.min.js`;
    })();

  // Babel
  (function () {
    var babel = require('gulp-babel');

    runBabel = function() {
      return gulp.src(dirs.fileGlob)
        .pipe(newer(dirs.parsed))
        .pipe(babel())
        .pipe(gulp.dest(dirs.parsed));
    }
  })();
  // Uglify
  (function () {
    var uglify = require('gulp-uglify');

    runUglifier = function () {
      return gulp.src(dirs.parsedGlob)
        .pipe(newer(dirs.min))
        .pipe(uglify())
        .pipe(rename({ extname: '.min.js' }))
        .pipe(gulp.dest(dirs.min));
    }
  })();
  // Concat
  (function () {
    var sharedName = 'shared-scripts.min.js';

    runConcat = function () {
      return gulp.src([`${dirs.min}shared/global.min.js`, `${dirs.min}shared/**/*.min.js`])
        .pipe(newer(`${dirs.min}/${sharedName}`))
        .pipe(concat(sharedName))
        .pipe(gulp.dest(dirs.min));
    }
  })();

  // Task finished
  function finished (cb) {
    taskFinished('JS');
    cb();
  }

  exports.js = gulp.series(runBabel, runUglifier, runConcat, finished);
  // Monitor JS
  watcher(dirs.fileGlob, exports.js);
  // Sync contents
  keepInSync(dirs.fileGlob, [dirs.parsed, dirs.min], 'delete');
  configured('JS');
  cb();
}
// HTML
function setupHTML (cb) {
  var runMinifier, runSync;
  var dirs = {};
    (function () {
      dirs.html = `./assets/php/html`;
      dirs.files = `${dirs.html}/files/`;
      dirs.min = `${dirs.html}/min/`;
      dirs.fileGlob = `${dirs.files}**/*.php`;
      dirs.minGlob = `${dirs.min}**/*.php`;
    })();

  // Minifier
  (function () {
    var minify = require('gulp-htmlmin');
    var options = {
      caseSensitive: true,
      collapseBooleanAttributes: true,
      collapseInlineTagWhitespace: true,
      collapseWhitespace: true,
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

    runMinifier = function () {
       return gulp.src(dirs.fileGlob)
        .pipe(newer(dirs.min))
        .pipe(minify(options))
        .pipe(gulp.dest(dirs.min));
      }
  })();
  // Sync with root
  (function () {
    runSync = function (cb) {
      sync(`${dirs.min}builds/**/*.php`, `./`, 'sync', 'change', 'src');
      cb();
    }
  })();
  // Task finished
  function finished (cb) {
    taskFinished('HTML');
    cb();
  }

  exports.html = gulp.series(runMinifier, runSync, finished);
  // Monitor JS
  watcher(dirs.fileGlob, exports.html);
  // Sync contents
  keepInSync(dirs.fileGlob, dirs.min, 'delete');
  configured('HTML');
  cb();
}
// Configure browserSync
function setupBrowserSync (cb) {
  var ports = {
    web: 35729,
    ui: 35730
  };

  browsersync.init({
    // Ports
    port: ports.web,
    ui: {
      port: ports.ui
    },
    // Server
    server: {
      baseDir: "./",
      index: "index.php"
    },
    /* proxy: "localhost:2600", */
    // Watch
    files: [
      './assets/**/min/**/*',
      './assets/php/scripts/**/*.php'
    ],
    // Preferences
    open: false,
    logLevel: "silent"
  });
  console.log(`|*| BrowserSync initialized on Ports ${ports.web} (Web) and ${ports.ui} (UI)`);
  cb();
}

function startup () {
  function startupFinished (cb) {
    console.log('Gulp - Initialized Defaults');
    cb();
  }

  return gulp.series(gulp.parallel(setupCSS, setupJS, setupHTML, setupBrowserSync, watchLog), startupFinished);
}
function copyToPublic (cb) {
  let glob = [
    './*assets/fonts/**/*',
    './*assets/img/**/*',
    './*assets/manifests/**/*',
    './*assets/**/min/**/*',
    './**/*.php',
    './**/*.ico',
    './**/*.xml',
    './.htaccess',
    '!./node_modules/**',
    '!./public/**',
    '!./assets/php/html/files/**'
  ]

  sync(glob, `./public/`, 'sync', 'change', 'src');
  cb();
}

exports.default = startup();
exports.public = copyToPublic;
