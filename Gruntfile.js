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
      minify: {
        files: [{
          expand: true,
          cwd: "assets/styles/css/files",
          src: ["**/*.css"],
          dest: "assets/styles/css/min",
          ext: ".min.css"
        }]
      },
      concat: {
        files: {
          "assets/styles/css/global-styles.min.css": ["assets/styles/css/min/**/*.min.css",
                                                      "!assets/styles/css/min/errordocs/**/*.min.css",
                                                      "!assets/styles/css/min/local/**/*.min.css"]
        }
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
      default: {
        files: {
          "assets/scripts/global-scripts.min.js": ["assets/scripts/min/**/*.min.js",
                                                   "!assets/scripts/min/s/**/*.min.js",
                                                   "!assets/scripts/min/local/**/*.min.js"]
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
                "**/*.",
                ".htaccess",
                "!.gitignore",
                "!.updateChecklist",
                "!Gruntfile.js",
                "!package-lock.json",
                "!package.json",
                "!README.md",
                "!.git",
                "!.git/**/*",
                "!.sass-cache",
                "!.sass-cache/**/*",
                "!.credentials",
                "!.credentials/**/*",
                "!.deprecated",
                "!.deprecated/**/*",
                "!.tdb",
                "!.tbd/**/*",
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
        tasks: ["sass", "cssmin:minify", "cssmin:concat"]
      },
      sass: {
        files: ["assets/styles/sass/**/*.scss",
                "!assets/styles/sass/partials/**/*.scss"],
        tasks: ["newer:sass", "newer:cssmin:minify", "newer:cssmin:concat"]
      },
      js: {
        files: ["assets/scripts/files/**/*.js"],
        tasks: ["newer:uglify", "newer:concat"]
      },
      html: {
        files: ["assets/php/html/files/**/*.php"],
        tasks: ["newer:htmlmin", "newer:copy:html"]
      },
      copy: {
        files: ["**/*",
                "**/*.",
                ".htaccess",
                "!.gitignore",
                "!.updateChecklist",
                "!Gruntfile.js",
                "!package-lock.json",
                "!package.json",
                "!README.md",
                "!.git/**/*",
                "!.sass-cache/**/*",
                "!.credentials/**/*",
                "!.deprecated/**/*",
                "!.tbd/**/*",
                "!node_modules/**/*",
                "!public/**/*",
                "!assets/php/html/files/**/*",
                "!assets/php/html/min/builds/**/*",
                "!assets/scripts/files/**/*",
                "!assets/styles/css/files/**/*",
                "!assets/styles/sass/**/*"],
        tasks: ["newer:copy:public"]
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
                "assets/scripts/global-scripts.min.js",
                "assets/scripts/min/local/**/*.min.js",
                "assets/styles/css/global-styles.min.css",
                "assets/styles/css/min/local/**/*.min.css",
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
  grunt.loadNpmTasks("grunt-contrib-uglify-es");
  grunt.loadNpmTasks("grunt-contrib-concat");
  grunt.loadNpmTasks("grunt-contrib-htmlmin");
  grunt.loadNpmTasks("grunt-contrib-copy");
  grunt.loadNpmTasks("grunt-contrib-watch");
  grunt.registerTask("default", ["watch"]);
};
