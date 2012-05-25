//fake browser window
global.window = require("jsdom")
                .jsdom()
                .createWindow();
global.$ = require("jquery");
global._ = require("underscore");
global.Backbone = require("backbone");

//Test framework
var jasmine=require('jasmine-node');
for(var key in jasmine) {
  global[key] = jasmine[key];
}

//What we're testing
global.HUGnet = require("../../src/webapi/html/hugnet.js").HUGnet;

var isVerbose = false;
var showColors = true;
var teamcity = process.env.TEAMCITY_PROJECT_NAME || false;
var useRequireJs = false;
var extentions = "js";
var match = '.';
var matchall = false;
var autotest = false;
var specFolder = __dirname + '/spec';
var regExpSpec = null;

jasmine.loadHelpersInFolder(
    specFolder,
    new RegExp("helpers?\\.(" + extentions + ")$", 'i')
);

var junitreport = {
  report: true,
  savePath : __dirname + "/../../build/jasmine/",
  useDotNotation: true,
  consolidate: true
}

jasmine.executeSpecsInFolder(
    specFolder,
    function(runner, log)
    {
        process.exit(runner.results().failedCount ? 1 : 0);
    },
    isVerbose,
    showColors,
    teamcity,
    useRequireJs,
    regExpSpec,
    junitreport
);
