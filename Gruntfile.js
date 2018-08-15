module.exports = function(grunt) {
  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),
    cssCompiler: {
      target1: ['cssmin']
    },
    cssmin: {
      target: {
        files: {
          'alpha/assets/styles.min.css': ['alpha/assets/test.css']
        }
      }
    },
    watch: {
      css: {
        files: ['alpha/assets/*.css'],
        tasks: ['cssCompiler']
      }
    }
  });
  grunt.loadNpmTasks('grunt-concurrent');
  grunt.loadNpmTasks('grunt-contrib-cssmin');
  grunt.loadNpmTasks('grunt-contrib-watch');
  grunt.registerTask('default', ['watch']);
};
