'use strict';

/**
 * @ngdoc service
 * @name wwwApp.Place
 * @description
 * # Place
 * Service in the wwwApp.
 */
angular.module('wwwApp')
  .service('Place', function (config, $resource) {
    return $resource(
        config.api.baseURL + '/places',
        {},
        {
          'top': {cache : true, method: 'GET', url: config.api.baseURL + '/top/place'}
        }
    );
  });