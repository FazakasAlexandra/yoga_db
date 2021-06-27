<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

// Load the system's routing file first, so that the app and ENVIRONMENT
// can override as needed.
if (file_exists(SYSTEMPATH . 'Config/Routes.php')) {
	require SYSTEMPATH . 'Config/Routes.php';
}

/**
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(false);

// users routes
$routes->group('users', function ($routes) {
	$routes->get('/', 'Users::index');
	$routes->post('/', 'Users::post');
});
$routes->get('users/(:any)/(:any)', 'Users::getUser/$1/$2');
$routes->get('users/clients', 'Users::usersNonAdm');
$routes->get('user/(:any)', 'Users::userNonAdm/$1');

// subscriptions routes related to users
$routes->get('subscriptions/decrease/(:any)/(:any)', 'Subscriptions::decreaseSubscriptionCoverage/$1/$2');
$routes->get('subscriptions/user/(:any)/class/(:any)', 'Subscriptions::getUserSubscriptionByClass/$1/$2');
$routes->get('subscriptions/user/(:any)', 'Subscriptions::userSubscriptions/$1');
$routes->delete('subscriptions/user/(:any)', 'Subscriptions::removeUserSubscription/$1');

// subscriptions routes
$routes->get('subscriptions/', 'Subscriptions::index');
$routes->delete('subscriptions/(:any)', 'Subscriptions::deleteSubscription/$1');
$routes->post('subscriptions', 'Subscriptions::addSubscription');
$routes->get('subscriptionnames', 'Subscriptions::getSubscriptionsNames');
$routes->post('subscriptions/(:any)/(:any)', 'Subscriptions::addSubscriptionToUser/$1/$2');

// schedules routes
$routes->get('schedules/classes/date/(:any)', 'SchedulesWeeks::getDaySchedule/$1'); // gets all classes for one day by date
$routes->put('schedules/links/classes/(:any)/(:any)', 'SchedulesWeeks::updateScheduledClassLink/$1/$2');
$routes->get('schedules/(:any)/(:any)', 'SchedulesWeeks::index/$1/$2');
$routes->get('schedules/latest', 'SchedulesWeeks::mostRecent');
$routes->post('schedules/(:any)/(:any)', 'SchedulesWeeks::postWeekSchedule/$1/$2');

// bookings routes
$routes->post('bookings/(:any)/(:any)', 'Bookings::postBooking/$1/$2');
$routes->get('bookings/classes/(:any)', 'Bookings::getClassBookings/$1'); // gets all bookings for one class based on schedule_weeks_id
//$routes->match(['post'], 'chgstatus/(:any)/(:any)', 'Bookings::chgStatus/$1/$2');
$routes->get('chgstatus/(:any)/(:any)', 'Bookings::chgStatus/$1/$2');
//$routes->get('bookings/(:any)', 'Bookings::getBooking/$1');

// classes routes
$routes->get('classes', 'Classes::index');
$routes->post('classes/newclass', 'Classes::addClass');
$routes->get('classes/attendences', 'Classes::attendences');
$routes->get('classes/dailyattendences/(:any)', 'Classes::dailyAttendances/$1');
$routes->get('classes/dlt/(:any)', 'Classes::dltClass/$1');

// events routes
$routes->get('events', 'Events::index');
$routes->get('events/(:any)', 'Events::getUpcomingEvents/$1');
$routes->post('events/newevent', 'Events::addEvent');
$routes->delete('events/dlt/(:any)', 'Events::dltEvent/$1');

/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (file_exists(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
	require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
