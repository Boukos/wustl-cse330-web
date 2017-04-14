// Modules
// Define the coffee module
// view1: coffees
// view2: reviews
angular.module('coffeeApp', [
	'ngRoute',
	'coffeeApp.coffees',
	'coffeeApp.reviews'
])

.config(['$routeProvider', function($routeProvider) {
	$routeProvider.when('/coffees', {
		templateUrl: 'coffees/coffees.html',
		controller: 'coffeesCtrler'
	}).when('/reviews/:coffeeId', {
		templateUrl: 'reviews/reviews.html',
		controller: 'reviewsCtrler'
	}).otherwise('/coffees');
}]);