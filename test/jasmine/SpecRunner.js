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

jasmine.executeSpecsInFolder(
    __dirname + '/spec', function(runner, log)
    {
        process.exit(runner.results().failedCount ? 1 : 0);
    },
    true,
    true
);
