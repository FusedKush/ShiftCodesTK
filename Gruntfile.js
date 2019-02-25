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
    watch: {
      sassPartials: {
        files: ["assets/styles/sass/partials/**/*.scss"],
        tasks: ["sass", "cssmin:minify", "cssmin:concat"]
      },
      sass: {
        files: ["assets/styles/sass/**/*.scss",
                "!assets/styles/sass/partials/**/*.scss",
                "!assets/styles/sass/errordocs/**/*.scss"],
        tasks: ["newer:sass", "newer:cssmin:minify", "newer:cssmin:concat"]
      },
      js: {
        files: ["assets/scripts/files/**/*.js"],
        tasks: ["newer:uglify", "newer:concat"]
      },
      config: {
        files: ["GruntFile.js"]
      },
      livereload: {
        files: ["**/*.php",
                "**/*.html",
                "**/*.txt",
                "assets/fonts/**/*",
                "assets/img/**/*",
                "assets/manifests/**/*",
                "assets/php/**/*",
                "assets/scripts/global-scripts.min.js",
                "assets/scripts/min/local/**/*.min.js",
                "assets/styles/css/global-styles.min.css",
                "assets/styles/css/min/local/**/*.min.css"],
        options: {
          livereload: true
        }
      }
    }
  });
  grunt.loadNpmTasks("grunt-contrib-sass");
  grunt.loadNpmTasks("grunt-contrib-cssmin");
  grunt.loadNpmTasks("grunt-contrib-uglify-es");
  grunt.loadNpmTasks("grunt-contrib-concat");
  grunt.loadNpmTasks("grunt-newer");
  grunt.loadNpmTasks("grunt-contrib-watch");
  grunt.registerTask("default", ["watch"]);
};
