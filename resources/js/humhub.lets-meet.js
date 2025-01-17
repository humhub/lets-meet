humhub.module('letsMeet', function (module, require, $) {
    var object = require('util').object;
    var client = require('client');
    var Content = require('content').Content;

    var LetsMeet = function (id) {
        Content.call(this, id);
    };



    module.export({
        LetsMeet: LetsMeet
    });
});