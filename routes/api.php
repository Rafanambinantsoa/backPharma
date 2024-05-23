<?php

use App\Http\Controllers\BilletController;
use App\Http\Controllers\CommandeController;
use App\Http\Controllers\EmailCOntroller;
use App\Http\Controllers\EssaieCOntroller;
use App\Http\Controllers\EvenementController;
use App\Http\Controllers\FlutterController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\MobileController;
use App\Http\Controllers\PointController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\UserController;
use App\Models\Commande;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('/registration', [UserController::class, 'registration']);
Route::post('/login', [UserController::class, 'login'])->name("login");

Route::get('/user/all', [UserController::class, 'allUsers']);
Route::get('/client/count', [UserController::class, 'countAllClient'])->middleware('auth:sanctum');

Route::get('/userNon', [UserController::class, 'userNonValide'])->middleware('auth:sanctum');
Route::patch('/validate/{user}', [UserController::class, 'validateAUser'])->middleware('auth:sanctum');
Route::get('/userValide', [UserController::class, 'userValide'])->middleware('auth:sanctum');;
Route::patch('/block/{user}', [UserController::class, 'blockUser'])->middleware('auth:sanctum');
Route::patch('/deblock/{user}', [UserController::class, 'debloque'])->middleware('auth:sanctum');
Route::delete('/user/delete/{user}', [UserController::class, 'delete'])->middleware('auth:sanctum');
Route::get('/client/all', [UserController::class, 'allClient'])->middleware('auth:sanctum');
Route::get('/organisateur/count', [UserController::class, 'countAllOrganisateur'])->middleware('auth:sanctum');
Route::post('/user/update/{user}', [UserController::class, 'update'])->middleware('auth:sanctum');
Route::get('/user/{user}', [UserController::class, 'oneUser'])->middleware('auth:sanctum');
Route::get('/infoOrgan/{user}', [UserController::class, 'infoOrgan'])->middleware('auth:sanctum');
Route::post("/forgot-password", [UserController::class, "forgotPassword"]);
Route::get("/password.reset/{token}", [UserController::class, "check"]);
Route::post("/reset-password", [UserController::class, "resetPassword"]);
Route::get('/account.activate/{id}', [UserController::class, 'activateAccount']);

//Related route to evenement
Route::get('/all', [EvenementController::class, 'index'])->middleware('auth:sanctum');
Route::get('/allEvents', [EvenementController::class, 'allEvents'])->middleware('auth:sanctum');
Route::get('/alllEvents', [EvenementController::class, 'allEvents']);
Route::post('/create_event', [EvenementController::class, 'add'])->middleware('auth:sanctum');
Route::get('/event/{event}', [EvenementController::class, 'oneEvent'])->middleware('auth:sanctum');
Route::get('/latest', [EvenementController::class, 'latest'])->middleware('auth:sanctum');
Route::put('/update/{event}', [EvenementController::class, 'updateEvent'])->middleware('auth:sanctum');
Route::delete('/delete/{event}', [EvenementController::class, 'deleteEvent'])->middleware('auth:sanctum');
Route::get('/count', [EvenementController::class, 'countAllEventbyUser'])->middleware('auth:sanctum');
Route::get('/eventCount', [EvenementController::class, 'countAllEvents'])->middleware('auth:sanctum');
Route::get('/countValide/{id}', [EvenementController::class, 'countAllEventValidebyUser'])->middleware('auth:sanctum');
Route::get('/countNonValide/{id}', [EvenementController::class, 'countAllEventNonValidebyUser'])->middleware('auth:sanctum');
Route::get('/eventEncours/{id}', [EvenementController::class, 'cours'])->middleware('auth:sanctum');
Route::get('/eventEncoursPaginated', [EvenementController::class, 'getEventsPaginated'])->middleware('auth:sanctum');
Route::get('/eventOver/{id}', [EvenementController::class, 'terminer'])->middleware('auth:sanctum');
Route::get('/eventOverPaginated/{id}', [EvenementController::class, 'terminerPaginated'])->middleware('auth:sanctum');

Route::get('/eventOverCount/{id}', [EvenementController::class, 'countTerminer'])->middleware('auth:sanctum');
Route::get('/eventEncoursCount/{id}', [EvenementController::class, 'countCours'])->middleware('auth:sanctum');
Route::get('/allEventUser/{id}', [EvenementController::class, 'allEventbyUser'])->middleware('auth:sanctum');
Route::get('/allEventUserPaginated/{id}', [EvenementController::class, 'allEventbyUserPaginated'])->middleware('auth:sanctum');
Route::put('/cloreEvent', [EvenementController::class, 'cloreEvent']);
Route::get('/nombreTousEvent', [EvenementController::class, 'countAllEvent'])->middleware('auth:sanctum');
Route::get('/nombreEventEnCours', [EvenementController::class, 'countAllEventValide'])->middleware('auth:sanctum');
Route::get('/marie', [EvenementController::class, 'encours'])->middleware('auth:sanctum');




Route::get('/merde', [UserController::class, 'tousLesUser']);




//Related route to commande
Route::post('/commande', [CommandeController::class, 'index'])->middleware('auth:sanctum');
Route::get('/commande/montantTotal', [CommandeController::class, 'totalCommande'])->middleware('auth:sanctum');
Route::get('/historique/{user}', [CommandeController::class, 'historiqueParUser'])->middleware('auth:sanctum');
Route::get('/revenusParOrgan/{user}', [CommandeController::class, 'revenusParOrganisateur'])->middleware('auth:sanctum');
Route::get('/annulerCommandeParUser/{user}', [CommandeController::class, 'annulerCommandeParUser'])->middleware('auth:sanctum');


//Related route for ticket

//Related route for email real


Route::post('/loginApi', [FlutterController::class, 'loginApi']);
Route::get('/userGet', [FlutterController::class, 'getUser'])->middleware('auth:sanctum');

//Related route  stripe
Route::post('/stripe', [CommandeController::class, 'stripe']);
Route::get('/success', [CommandeController::class, 'redirigerVersPageLocale'])->name('success');
Route::get('/cancel', [CommandeController::class, 'cancel'])->name('cancel');


//Related route for test
Route::get('/test', [CommandeController::class, 'test']);

Route::post('/event/invitation/{event}', [InvitationController::class, 'EnvoieInvitation']);
Route::post('/event/{event}/{user}', [InvitationController::class, 'sendSingleInvitation']);
Route::get('/user/info/{token}', [InvitationController::class, 'showUserInformation']);
Route::post('/event/{event_id}/firstScan/{user_id}', [InvitationController::class, 'firstPresence']);
Route::put('/event/{event_id}/secondScan/{user_id}', [InvitationController::class, 'secondPresence']);
Route::get('/event/listPresence/{event_id}', [InvitationController::class, 'getListPresence']);
Route::get('/event/listPresence/{event_id}/first', [InvitationController::class, 'getListPresenceFirst']);
Route::get('/event/listAbsence/{event_id}', [InvitationController::class, 'getListAbsence']);
Route::get('/searchevent', [InvitationController::class, 'getAllEvent']);
Route::get('/sendQr', [InvitationController::class, 'sendQrAllUser']);
Route::get('/sendQrsingle/{user}', [InvitationController::class, 'sendQrToUser']);


//Point related route
Route::post('/addPoint/{user}', [PointController::class, 'addPoint']);
Route::get('/user/point/{user}', [PointController::class, 'showUserPoint']);
Route::post('/users/actualistionPoint', [PointController::class, 'actualisationPoint']);
Route::get('/user/sumpoint/{user}', [PointController::class, 'sommePoint']);


//Reservation related route 
Route::post('/reservation/add/{event_id}', [ReservationController::class, 'addReservation']);
Route::get('/reservation/list/{event_id}', [ReservationController::class, 'getListReservation']);
Route::get('/reservation/user/{user}', [ReservationController::class, 'reservationPerUser']);
// ->middleware('auth:sanctum');

//Related route for Mobile
Route::post('/mobile/login', [MobileController::class, 'login']);
Route::post('/mobile/user/update/{user}', [UserController::class, 'mettreAjour'])->middleware('auth:sanctum');
Route::get('/mobile/events', [MobileController::class, 'getEventsEncours']);
Route::delete('/mobile/events/{event_id}/annuler/{user_id}', [ReservationController::class, 'cancelReservation'])->middleware('auth:sanctum');
