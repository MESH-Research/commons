var gulp = require('gulp'),
    sass = require('gulp-sass'),
    gutil = require('gulp-util'),
    concat = require('gulp-concat'),
    uglify = require('gulp-uglify'),
    rename = require('gulp-rename'),
    notify = require('gulp-notify'),
    minifycss = require('gulp-clean-css'),
    watch = require('gulp-watch'),
    browserSync = require('browser-sync'),
    shell = require('gulp-shell'),
    php = require('gulp-connect-php'),
    env = require('dotenv').config(),
    plumber = require('gulp-plumber'),
    portscanner = require('portscanner');

var config = {
    pluginsRoot: './web/app/plugins/',
    muPluginRoot: './web/app/mu-plugins/',
    themeRoot: './web/app/themes/boss-child/',
    sassRoot : './web/app/themes/boss-child/resources/assets/css/sass/',
    sassPartials: './web/app/themes/boss-child/resources/assets/css/sass/partials/',
    jsDir : './web/app/themes/boss-child/resources/assets/js/'
}

gulp.task('css', function() {

	return gulp.src( config.sassPathRoot + '/app.scss' )
        .pipe( plumber() )
         .pipe( sass({
             style: 'compressed',
             loadPath: [
                 config.themeRoot + 'resources/css/sass',
             ]
         }) 
        .on("error", notify.onError(function (error) {
          	return "Error: " + error.message;
          }))) 
		.pipe( minifycss() )
		.pipe( rename({
			suffix: '.min'
		}))
     .pipe( gulp.dest( config.themeRoot + 'css/' ) )
		.pipe( notify({ message: 'CSS compiled successfully!' }) ); 

});

gulp.task( 'compile-js', function() {
	return gulp.src( config.jsDir + "/*.js")
    .pipe( plumber() )
		.pipe( uglify().on('error', gutil.log ) )
		.pipe( rename({suffix: '.min'}) )
		.pipe( gulp.dest( config.themeRoot + 'js') )
		.pipe( notify({ message: "Js compiled and minified!" }) )
});

//this task is to update the max number of watches since browsersync is watching 6 folders for changes
gulp.task('update-max-watches', shell.task([
  'echo fs.inotify.max_user_watches=524288 | sudo tee -a /etc/sysctl.conf && sudo sysctl -p'
]));

gulp.task( 'test-port-scanner', function() {

  var ports = [ 3010, 3011, 3012, 3013, 3014, 3015, 3016, 3017, 3018, 3019 ];

  for( port in ports ) {
    portscanner.checkPortStatus(port, '127.0.0.1', function( error, status ) {
        console.log(status);
    });
  }

});

gulp.task('init', ['css', 'compile-js'], function() {

  //define all BS servers to initialize
  browserSync.create('hc');
  browserSync.create('mla');
  browserSync.create('aseees');
  browserSync.create('ajs');
  browserSync.create('caa');

  //initialize all warp drives...
  if( process.env.HC_SITE_URL ) {

  	browserSync.get('hc').init([], {
  		https: true,
  		port: 3010,
      ui: { port: 3011 },
  		proxy: 'https://' + process.env.HC_SITE_URL,
      logLevel: "info",
  		files: [
  			config.sassPathRoot + '*.scss',
  			config.sassPathPartials + '*.scss',
        'web/app/**/*.php',
        config.muPluginRoot + '*.php',
        config.pluginsRoot + '**/*.php',
        config.themeRoot + '**/*.php',
        {
          fn: function( event, file ) {
            this.reload();
          }
        }
      ]
  	});

  } else {

    throw new Error("Uh Oh! The env var for HC_SITE_URL is not set, browserSync not initialized!");

  }

  if( process.env.MLA_SITE_URL ) {

    browserSync.get('mla').init([], {
      https: true,
      port: 3012,
      ui: { port: 3013 },
      proxy: 'https://' + process.env.MLA_SITE_URL,
      logLevel: "info",
      files: [
        config.sassPathRoot + '*.scss',
        config.sassPathPartials + '*.scss',
        'web/app/**/*.php',
        config.muPluginRoot + '*.php',
        config.pluginsRoot + '**/*.php',
        config.themeRoot + '**/*.php',
        {
          fn: function( event, file ) {
            this.reload();
          }
        }
      ]
    });

  } else {

    throw new Error("Uh Oh! The env var for MLA_SITE_URL is not set, browserSync not initialized!");

  }

  if( process.env.ASEEES_SITE_URL ) {

    browserSync.get('aseees').init([], {
      https: true,
      port: 3014,
      ui: { port: 3015 },
      proxy: 'https://' + process.env.ASEEES_SITE_URL,
      logLevel: "info",
      files: [
        config.sassPathRoot + '*.scss',
        config.sassPathPartials + '*.scss',
        'web/app/**/*.php',
        config.muPluginRoot + '*.php',
        config.pluginsRoot + '**/*.php',
        config.themeRoot + '**/*.php',
        {
          fn: function( event, file ) {
            this.reload();
          }
        }
      ]
    });

  } else {

    throw new Error("Uh Oh! The env var for ASEEES_SITE_URL is not set, browserSync not initialized!");

  }

  if( process.env.AJS_SITE_URL ) {

    browserSync.get('ajs').init([], {
      https: true,
      port: 3016,
      ui: { port: 3017 },
      proxy: 'https://' + process.env.AJS_SITE_URL,
      logLevel: "info",
      files: [
        config.sassPathRoot + '*.scss',
        config.sassPathPartials + '*.scss',
        'web/app/**/*.php',
        config.muPluginRoot + '*.php',
        config.pluginsRoot + '**/*.php',
        config.themeRoot + '**/*.php',
        {
          fn: function( event, file ) {
            this.reload();
          }
        }
      ]
    });

  } else {

    throw new Error("Uh Oh! The env var for AJS_SITE_URL is not set, browserSync not initialized!");

  }

  if( process.env.CAA_SITE_URL ) {

    browserSync.get('caa').init([], {
      https: true,
      port: 3018,
      ui: { port: 3019 },
      proxy: 'https://' + process.env.CAA_SITE_URL,
      logLevel: "info",
      files: [
        config.sassPathRoot + '*.scss',
        config.sassPathPartials + '*.scss',
        'web/app/**/*.php',
        config.muPluginRoot + '*.php',
        config.pluginsRoot + '**/*.php',
        config.themeRoot + '**/*.php',
        {
          fn: function( event, file ) {
            this.reload();
          }
        }
      ]
    });

  } else {

    throw new Error("Uh Oh! The env var for CAA_SITE_URL is not set, browserSync not initialized!");

  }

	gulp.watch( config.jsDir + '*.js', ['compile-js'] )
		.on( 'change', function() {
			browserSync.reload();
			notify( 'Js compiled!' ).write('');
		});

	gulp.watch( config.sassPathRoot + '*.scss', ['css'] )
		.on('change', function() {
			browserSync.reload();
			notify('CSS compressed from root scss & browser reloaded!').write('');
		});

	gulp.watch( config.sassPathPartials + '*.scss', ['css'] )
		.on('change', function() {
			browserSync.reload();
			notify('CSS compressed from partials & browser reloaded!').write('');
		});

});
