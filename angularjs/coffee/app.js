// Modules
angular.module('coffeeApp', [
	'ngRoute',
	'coffeeApp.coffees',
	'coffeeApp.reviews'
]).
config(['$routeProvider', function($routeProvider) {
	$routeProvider.otherwise({redirectTo: '/coffees'});
}]);