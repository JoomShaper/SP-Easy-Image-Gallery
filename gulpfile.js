const { series, parallel } = require('gulp');
const { src, dest } = require('gulp');
const del = require('del');
const zip = require('gulp-zip');


const config = {
    srcPath: './',
    buildPath: './dist/component/',
    rootPath: './dist/',
    componentName: 'speasyimagegallery',
    com_package_name: 'com_speasyimagegallery_fullpackage_v2.0.3.zip',
    moduleExts: ['xml','php','js','css','jpg','png','gif','ttf','otf','woff','woff2','svg','eot']
}

const tasks = {
    manifest: {
        src: [config.srcPath + 'administrator/components/com_' + config.componentName + '/' + config.componentName + '.xml', config.srcPath + 'administrator/components/com_' + config.componentName + '/installer.script.php'],
        dest: config.buildPath
    },
    admin: {
        src: [
            config.srcPath + 'administrator/components/com_' + config.componentName + '/**/*.{xml,php,js,css,jpg,png,gif,ttf,otf,woff,woff2,svg,eot,sql,json}'
        ],
        dest: config.buildPath + '/admin'
    },
    site: {
        src: [config.srcPath + 'components/com_' + config.componentName + '/**/*.{xml,php,js,css,jpg,png,gif,ttf,otf,woff,woff2,svg,eot,json,txt}'],
        dest: config.buildPath + '/site'
    },
    modules: [
        {
            name: 'speasyimagegallery',
            exts: config.moduleExts
        }
    ],
}

// clean rootpath
function clean() {
    return del([config.rootPath]);
}

// manifest task
function manifest() {
    return src(tasks.manifest.src, {allowEmpty: true})
    .pipe(dest(tasks.manifest.dest));
}

// admin tasks
function admin() {
    return src(tasks.admin.src)
    .pipe(dest(tasks.admin.dest));
}

// site tasks 
function site() {
    return src(tasks.site.src)
    .pipe(dest(tasks.site.dest));
}

// modules tasks
function modules(callback) {
    let modules = typeof (tasks.modules) == 'object' && tasks.modules instanceof Array && tasks.modules.length > 0 ? tasks.modules : false;
    let module_tasks = [];

    if (modules) {
        modules.map(module => {
            let mod_task =  (taskDone) => {
                src([ config.srcPath + 'modules/mod_' + module.name + '/**/*.{' + module.exts.join(',') + '}', config.srcPath + 'language/en-GB/en-GB.mod_' + module.name + '*.ini'], {allowEmpty: true})
                .pipe(dest(config.buildPath + '/modules/mod_' + module.name + '/'));
                taskDone();
            }
            module_tasks.push(mod_task);
        });
    }
    return series(...module_tasks, seriesDone => {
        seriesDone();
        callback();
    })();
}


// plugin tasks
function plugins(callback) {

    let plugins = typeof (tasks.plugins) == 'object' && tasks.plugins instanceof Array && tasks.plugins.length > 0 ? tasks.plugins : false;
    let plugin_tasks = [];

    if (plugins) {
        plugins.map(plugin => {
            let plg_task = taskDone => {
                src([plugin.path + '/' + plugin.name + '/**/*.{xml,php,js,css,jpg,png,gif,ttf,otf,woff,woff2,svg,eot,json}',
                config.srcPath + 'administrator/language/en-GB/en-GB.plg_' + plugin.type + '_' + plugin.name + '*.ini'], { allowEmpty: true })
                .pipe(dest(config.buildPath + '/plugins/' + plugin.name));
                taskDone();
            }
            plugin_tasks.push(plg_task);
        });
    }

    return series(...plugin_tasks, seriesDone => {
        seriesDone();
        callback();
    })();
}

// admin language task
function adminLanguage(callback) {
    return src([config.srcPath + 'administrator/language/en-GB/en-GB.com_' + config.componentName + '*.ini'], { allowEmpty: true })
    .pipe(dest(config.buildPath + '/language/admin/en-GB'));
}

// site language task
function siteLanguage(callback) {
    return src([config.srcPath + 'language/en-GB/en-GB.com_' + config.componentName + '.ini'], { allowEmpty: true })
    .pipe(dest(config.buildPath + '/language/site/en-GB'));
}

// modules language
function moduleLanguages(callback) {
    let modules = typeof (tasks.modules) == 'object' && tasks.modules instanceof Array && tasks.modules.length > 0 ? tasks.modules : false;
    let module_language_tasks = [];
    if (modules) {
        modules.map(module => {
            let mod_task =  (taskDone) => {
                src([ config.srcPath + 'language/en-GB/en-GB.mod_' + module.name + '.ini', config.srcPath + 'language/en-GB/en-GB.mod_' + module.name + '.sys.ini'], {allowEmpty: true})
                .pipe(dest(config.buildPath + '/modules/mod_' + module.name + '/language'));
                taskDone();
            }
            module_language_tasks.push(mod_task);
        });
    }
    return series(...module_language_tasks, seriesDone => {
        seriesDone();
        callback();
    })();
}

// make component package
function componentPackage(callback) {
    return src(config.buildPath + '/**')
    .pipe(zip(config.com_package_name))
    .pipe(dest(config.buildPath));
}

exports.default = series(
    clean,modules, plugins, manifest, admin, site, adminLanguage, siteLanguage, 
    componentPackage
)