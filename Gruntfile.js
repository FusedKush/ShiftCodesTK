module.exports = function(grunt) {
  grunt.initConfig({
    pkg: grunt.file.readJSON("package.json"),
    sass: {
      options: {
        sourcemap: "none"
      },
      default: {
        files: [{
          expand: true,
          cwd: "assets/styles/sass",
          src: ["**/*.scss"],
          dest: "assets/styles/css/files",
          ext: ".css"
        }]
      }
    },
    cssmin: {
      shared: {
        files: {
        "assets/styles/css/min/shared-styles.min.css": ["assets/styles/css/min/shared/global.min.css",
                                                        "assets/styles/css/min/shared/**/*.min.css"]
        }
      }
    },
    postcss: {
      prefix: {
        options: {
          processors: [
            require('autoprefixer')({browsers: 'defaults'})
          ]
        },
        files: [{
          expand: true,
          cwd: "assets/styles/css/files",
          src: ["**/*.css"],
          dest: "assets/styles/css/files"
        }]
      },
      minify: {
        options: {
          processors: [
            require('cssnano')()
          ]
        },
        files: [{
          expand: true,
          cwd: "assets/styles/css/files",
          src: ["**/*.css"],
          dest: "assets/styles/css/min",
          ext: ".min.css"
        }]
      }
    },
    uglify: {
      default: {
        files: [{
          expand: true,
          cwd: "assets/scripts/files",
          src: ["**/*.js"],
          dest: "assets/scripts/min",
          ext: ".min.js"
        }]
      }
    },
    concat: {
      shared: {
        files: {
          "assets/scripts/min/shared-scripts.min.js": ["assets/scripts/min/shared/global.min.js",
                                                       "assets/scripts/min/shared/**/*.min.js"]
        }
      }
    },
    htmlmin: {
      options: {
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
        removeRedundantAttributes: true,
        removeScriptTypeAttributes: true,
        removeStyleLinkTypeAttributes: true,
        removeTagWhitespace: true,
        sortAttributes: true,
        sortClassName: true,
        trimCustomFragments: true,
        useShortDoctype: true
      },
      default: {
        files: [{
          expand: true,
          cwd: "assets/php/html/files",
          src: ["**/*.php"],
          dest: "assets/php/html/min"
        }]
      }
    },
    copy: {
      html: {
        files: [{
          expand: true,
          cwd: "assets/php/html/min/builds",
          src: ["**/*.php"],
          dest: ""
        }]
      },
      public: {
        files: [{
          expand: true,
          src: ["**/*",
                "!.*",
                ".htaccess",
                "!.*/**/*",
                "!Gruntfile.js",
                "!package-lock.json",
                "!package.json",
                "!README.md",
                "!node_modules",
                "!node_modules/**/*",
                "!public",
                "!public/**/*",
                "!assets/php/html/files",
                "!assets/php/html/files/**/*",
                "!assets/php/html/min/builds",
                "!assets/php/html/min/builds/**/*",
                "!assets/scripts/files",
                "!assets/scripts/files/**/*",
                "!assets/styles/css/files",
                "!assets/styles/css/files/**/*",
                "!assets/styles/sass",
                "!assets/styles/sass/**/*"],
          dest: "public"
        }]
      }
    },
    watch: {
      sassPartials: {
        files: ["assets/styles/sass/partials/**/*.scss"],
        tasks: ["processAllCSS"]
      },
      sass: {
        files: ["assets/styles/sass/**/*.scss",
                "!assets/styles/sass/partials/**/*.scss"],
        tasks: ["processCSS"]
      },
      js: {
        files: ["assets/scripts/files/**/*.js"],
        tasks: ["processJS"]
      },
      html: {
        files: ["assets/php/html/files/**/*.php"],
        tasks: ["processHTML"]
      },
      copy: {
        files: ["**/*",
                "!.*",
                ".htaccess",
                "!.*/**/*",
                "!Gruntfile.js",
                "!package-lock.json",
                "!package.json",
                "!README.md",
                "!node_modules/**/*",
                "!public/**/*",
                "!assets/php/html/files/**/*",
                "!assets/php/html/min/builds/**/*",
                "!assets/scripts/files/**/*",
                "!assets/styles/css/files/**/*",
                "!assets/styles/sass/**/*"],
        tasks: ["publicCopy"]
      },
      config: {
        files: ["Gruntfile.js"],
        options: {
          reload: true
        }
      },
      livereload: {
        files: ["*.php",
                "assets/fonts/**/*",
                "assets/img/**/*",
                "assets/manifests/**/*",
                "assets/php/**/*",
                "assets/scripts/min/**/*.min.js",
                "assets/styles/css/min/**/*.min.css",
                "!assets/php/files/**/*",
                "!assets/php/min/builds/**/*"],
        options: {
          livereload: true
        }
      }
    }
  });
  grunt.loadNpmTasks("grunt-newer");
  grunt.loadNpmTasks("grunt-contrib-sass");
  grunt.loadNpmTasks("grunt-contrib-cssmin");
  grunt.loadNpmTasks("grunt-postcss");
  grunt.loadNpmTasks("grunt-contrib-uglify-es");
  grunt.loadNpmTasks("grunt-contrib-concat");
  grunt.loadNpmTasks("grunt-contrib-htmlmin");
  grunt.loadNpmTasks("grunt-contrib-copy");
  grunt.loadNpmTasks("grunt-contrib-watch");

  // Compile All SASS to CSS, Add Prefixes, Minify, and Concatenate shared stylesheets
  grunt.registerTask("processAllCSS", ["sass", "postcss", "cssmin"]);
  // Compile New SASS to CSS, Add Prefixes, Minify, and Concatenate shared stylesheets
  grunt.registerTask("processCSS", ["newer:sass", "newer:postcss", "newer:cssmin"]);
  // Minify JS and Concatenate shared scripts
  grunt.registerTask("processJS", ["newer:uglify", "newer:concat"]);
  // Minify HTML and Copy to required location
  grunt.registerTask("processHTML", ["newer:htmlmin", "newer:copy:html"]);
  // Copy required files to Public directory
  grunt.registerTask("publicCopy", ["newer:copy:public"]);
  // Update all changed files & start watch
  grunt.registerTask("default", ["processCSS", "processJS", "processHTML", "publicCopy", "watch"]);
};
