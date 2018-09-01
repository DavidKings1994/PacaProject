//compilado y servidor
var gulp = require('gulp');
var gutil = require("gulp-util");
var webpack = require("webpack");
var inject = require('gulp-inject');
var webserver = require('gulp-webserver');
var connect = require('gulp-connect-php');
var gnf = require('gulp-npm-files');
var vueify = require('gulp-vueify');
var ExtractTextPlugin = require('extract-text-webpack-plugin');

//Creacion del ambiente public
var htmlclean = require('gulp-htmlclean');
var cleanCSS = require('gulp-clean-css');
var concat = require('gulp-concat');
var uglify = require('gulp-uglify');
var del = require('del');

//deploy
var argv  = require('minimist')(process.argv);
var rsync = require('gulp-rsync');
var prompt = require('gulp-prompt');
var gulpif = require('gulp-if');

// Directorios generales
var paths = {
    src: 'src/**/*',
    srcHTML: 'src/pages/**/*.php',
    srcCSS: 'src/**/*.css',
    srcJS: 'src/**/*.js',
    srcPHP: 'src/php/**/*.php',
    srcIMG: 'src/assets/**/*.*',

    public: 'public',
    publicIndex: 'public/index.php',
    publicCSS: 'public/build/**/*.css',
    publicJS: 'public/dist/**/*.js',

    dist: 'dist',
    distIndex: 'dist/index.php',
    distCSS: 'dist/**/*.css',
    distJS: 'dist/**/*.js'
};

gulp.task("webpack", function(callback) {
    return webpack({
        entry: [
            __dirname + "/src/js/main.js",
            __dirname + "/src/scss/main.scss"
        ],
        output: {
            path: __dirname + "/dist/",
            publicPath: __dirname + "/public/",
            filename: "[name].bundle.js"
        },
        plugins: [
            new ExtractTextPlugin({
                filename: '[name].bundle.css',
                allChunks: true
            }),
            new webpack.ProvidePlugin({
                $: 'jquery',
                jquery: 'jquery',
                'window.jQuery': 'jquery',
                jQuery: 'jquery'
            })
        ],
        resolve: {
            alias: {
                'jquery': __dirname + '/node_modules/jquery/dist/jquery.js',
                'bootstrap': __dirname + '/node_modules/bootstrap/dist/js/bootstrap.js',
                'dom-to-image':  __dirname + '/node_modules/dom-to-image/src/dom-to-image.js',
                'file-saver':  __dirname + '/node_modules/file-saver/FileSaver.js',
                'vue-bootstrap-toggle':  __dirname + '/node_modules/vue-bootstrap-toggle/dist/index.js',
                'vue': __dirname + '/node_modules/vue/dist/vue.js',
                'vuex':  __dirname + '/node_modules/vuex/dist/vuex.js',
                'vue-router':  __dirname + '/node_modules/vue-router/dist/vue-router.js',
                'vue-select':  __dirname + '/node_modules/vue-select/dist/vue-select.js',
                'vue-bootstrap-table':  __dirname + '/node_modules/vue2-bootstrap-table2/dist/vue-bootstrap-table.js',
                'swal':  __dirname + '/node_modules/sweetalert/dist/sweetalert.min.js'
            }
        },
        module: {
            loaders: [
                { test: /\.css$/, loader: "style!css" },
                { test: /\.vue$/, loader: "vue-loader" },
                { test: /\.(png|jpg)$/, loader: 'file-loader' }
            ],
            rules: [
                {
                    test: /\.css$/,
                    loader: ExtractTextPlugin.extract({
                        loader: 'css-loader?importLoaders=1',
                    })
                },
                {
                    test: /\.(sass|scss)$/,
                    loader: ExtractTextPlugin.extract(['css-loader', 'sass-loader'])
                },
                {
                    test: /\.vue$/,
                    loader: 'vue-loader'
                }
            ]
        }
    }, function(err, stats) {
        if(err) throw new gutil.PluginError("webpack", err);
        gutil.log("[webpack]", stats.toString({
            colors: true
        }));
        callback();
    });
});

gulp.task('vueify', function() {
    return gulp.src('src/js/views/**/*.vue')
        .pipe(vueify())
        .pipe(gulp.dest('./dist/components'));
});

gulp.task('html', function () {
    return gulp.src(paths.srcHTML).pipe(gulp.dest(paths.public));
});

gulp.task('css', function () {
    return gulp.src(paths.distCSS).pipe(gulp.dest('./public/dist'));
});

gulp.task('js', function () {
    return gulp.src(paths.distJS).pipe(gulp.dest('./public/dist'));
});

gulp.task('bundles', ['webpack'], function () {
    var js = gulp.src(paths.distJS).pipe(gulp.dest('./public/dist'));
    var css = gulp.src(paths.distCSS).pipe(gulp.dest('./public/dist'));
    return [js,css];
});

gulp.task('php', function () {
    return gulp.src(paths.srcPHP).pipe(gulp.dest('./public/php'));
});

gulp.task('img', function () {
    return gulp.src(paths.srcIMG).pipe(gulp.dest('./public/assets'));
});

// copia las dependecias listadas en el package.json a la direccion especificada
gulp.task('copyNpm', function() {
    gulp.src(gnf(), {base:'./'}).pipe(gulp.dest('./public/build'));
});

gulp.task('copy', ['html', 'bundles', 'php', 'img', 'copyNpm']);

// inyecta las nuevas direcciones publicas de los archivos de css y js en el index de la carpeta public
gulp.task('inject', ['copy'], function () {
    var mainCss = gulp.src([paths.publicCSS, 'public/dist/main.bundle.css'], { read: false });
    var js = gulp.src(paths.publicJS);
    return gulp.src(paths.publicIndex)
        .pipe(inject( mainCss, { relative:true } ))
        .pipe(inject( js, { relative:true } ))
        .pipe(gulp.dest(paths.public));
});

// inicializa un servidor web de gulp para la aplicacion
gulp.task('serve', ['inject'], function () {
    return gulp.src(paths.public)
        .pipe(webserver({
            port: 3000,
            livereload: true
        }));
});

// gulp.task('watch', ['serve'], function () {
//     gulp.watch(paths.src, ['inject']);
// });

// conecta el servidor de gulp con el servicio de php
gulp.task('connect', ['inject'], function() {
    connect.server({
        bin: 'C:/xampp/php/php.exe',
        ini: 'C:/xampp/php/php.ini',
        port: 8000,
        base: paths.public
    });
});

gulp.task('disconnect', function() {
    connect.closeServer();
});

gulp.task('watch', ['connect'], function () {
    gulp.watch(paths.src, ['inject']);
});

gulp.task('html:dist', function () {
    return gulp.src(paths.srcHTML)
        .pipe(htmlclean())
        .pipe(gulp.dest(paths.dist));
});

gulp.task('css:dist', function () {
    return gulp.src(paths.srcCSS)
        .pipe(concat('style.min.css'))
        .pipe(cleanCSS())
        .pipe(gulp.dest(paths.dist));
});

gulp.task('js:dist', function () {
    return gulp.src(paths.srcJS)
        .pipe(concat('script.min.js'))
        .pipe(uglify())
        .pipe(gulp.dest(paths.dist));
});

gulp.task('copy:dist', ['html:dist', 'css:dist', 'js:dist']);

gulp.task('inject:dist', ['copy:dist'], function () {
    var css = gulp.src(paths.distCSS);
    var js = gulp.src(paths.distJS);
    return gulp.src(paths.distIndex)
        .pipe(inject( css, { relative:true } ))
        .pipe(inject( js, { relative:true } ))
        .pipe(gulp.dest(paths.dist));
});

gulp.task('build', ['inject:dist']);

// elimina las carpetas de public y dist
gulp.task('clean', function () {
    del([paths.public, paths.dist]);
});

gulp.task('default', ['watch']);

gulp.task('deploy', function() {

    // Dirs and Files to sync
    rsyncPaths = [paths.public];

    // Default options for rsync
    rsyncConf = {
        progress: true,
        incremental: true,
        relative: true,
        emptyDirectories: true,
        recursive: true,
        clean: true,
        exclude: [],
    };

    // Staging
    if (argv.staging) {

        rsyncConf.hostname = ''; // hostname
        rsyncConf.username = ''; // ssh username
        rsyncConf.destination = ''; // path where uploaded files go

        // Production
    } else if (argv.production) {

        rsyncConf.hostname = 'sirnus.com'; // hostname
        rsyncConf.username = 'ph19757465091'; // ssh username
        rsyncConf.destination = 'sirnus.com'; // path where uploaded files go


        // Missing/Invalid Target
    } else {
        throwError('deploy', gutil.colors.red('Missing or invalid target'));
    }


    // Use gulp-rsync to sync the files
    return gulp.src(rsyncPaths)
    .pipe(gulpif(
        argv.production,
        prompt.confirm({
            message: 'Heads Up! Are you SURE you want to push to PRODUCTION?',
            default: false
        })
    ))
    .pipe(rsync(rsyncConf));

});


function throwError(taskName, msg) {
    throw new gutil.PluginError({
        plugin: taskName,
        message: msg
    });
}
