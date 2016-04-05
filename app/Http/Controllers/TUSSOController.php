<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Log;
use Auth;

class TUSSOController extends Controller {
	/*
	|--------------------------------------------------------------------------
	| The Mu Request Handler
	|--------------------------------------------------------------------------
	*/


	/*
	| timeStamp()
	|
	| a method called when client read ID card
	|
	| @CallMethod Route
	| @Input [String]Card Data
	| @Output View
	|
	*/
	public function timeStamp(Request $request) {
		// Record to database, as "entrance" if it's the first time of the day, if not, record as "exit".

		//Get user id from the card
		if ($request->has('card')) {
			if (!$userinfo = DB::table('personnel')->where('card', $request->input('card'))->first()) {
				return view('errors.custom', [
					'title' => 'User Not Found',
					'description' => 'ไม่พบข้อมูลบัตรนี้ในระบบ',
					'button' => '<a href="/client" class="waves-effect waves-light btn indigo darken-3 tooltipped center-align" data-tooltip="Back to index"
       style="width:80%;max-width:350px;margin-top:20px">หน้าหลัก</a><script>setTimeout(function () { window.location = "/client"}, 5000);</script>'
				]);
			}
		} elseif ($request->has('user')) {
			if (!$userinfo = DB::table('personnel')->where('id', $request->input('user'))->first()) {
				return view('errors.custom', [
					'title' => 'User Not Found',
					'description' => 'ไม่พบผู้ใช้นี้ในระบบ',
					'button' => '<a href="/client" class="waves-effect waves-light btn indigo darken-3 tooltipped center-align" data-tooltip="Back to index"
       style="width:80%;max-width:350px;margin-top:20px">หน้าหลัก</a>'
				]);
			} else {
				Log::notice('OVERRIDE: '.$request->input('user').' has been manually time stamped by '.$request->session()->get('userid', 'UNKNOWN').' (Not Confirmed)');
			}
		} else {
			return view('errors.custom', [
				'title' => 'Bad Request',
				'description' => 'ไม่พบข้อมูลบัตรในคำขอ กรุณาแจ้งผู้ดูแลระบบ',
				'button' => '<a href="/client" class="waves-effect waves-light btn indigo darken-3 tooltipped center-align" data-tooltip="Back to index"
       style="width:80%;max-width:350px;margin-top:20px">หน้าหลัก</a>'
			]);
		}

		$tm = date('o-m-d H:i:s');

		//Check if the user has stamped yet?
		if ($stamps = DB::table('timestamp')->where('person', $userinfo->id)->whereRaw('date = CURDATE()')->first()) {
			//The second+ stamp.

			if (DB::table('timestamp')->where('person', $userinfo->id)->where('date',
				date('o-m-d'))->update(['exit' => date('o-m-d H:i:s')])
			) {

				$mytime = new DateTime(date('H:i:s'));
				$extime = new DateTime(config('ats.' . $userinfo->type . '-exit-hour') . ':' . config('ats.' . $userinfo->type . '-exit-minute') . ':00');
				if ($mytime < $extime) {
					DB::table('timestamp')->where('person', $userinfo->id)->where('date',
						date('o-m-d'))->update(['preexit' => TRUE]);

					if (empty($stamps->preexitreason)) {
						return view('clientsuccess', [
							'message' => $userinfo->name . ' ออกโรงเรียนเมื่อเวลา ' . $tm,
							'special' => '<div class="sector red lighten-2"><br />นักเรียนไม่ได้รับอนุญาตให้ออกก่อนเวลา<br /></div>'
						]);
					} else {
						return view('clientsuccess', [
							'message' => $userinfo->name . ' ออกโรงเรียนเมื่อเวลา ' . $tm,
							'special' => '<div class="sector green lighten-1"><br />นักเรียนได้รับอนุญาตให้ออกก่อนเวลา (' . $stamps->preexitreason . ')<br /></div>'
						]);
					}
				} else {
					if ($stamps->preexit) {
						DB::table('timestamp')->where('person', $userinfo->id)->where('date',
							date('o-m-d'))->update(['preexit' => FALSE]);
					}

					return view('clientsuccess', ['message' => $userinfo->name . ' ออกโรงเรียนเมื่อเวลา ' . $tm]);
				}
			} else {
				Log::error('DB: Error occured while clocking out user: '.$userinfo->id);
				return view('errors.custom', [
					'title' => 'Database Error',
					'description' => 'เกิดข้อผิดพลาดในการบันทึกลงในฐานข้อมูล อาจเป็นเพราะคุณบันทึกเวลาถี่เกินไป',
					'button' => '<a href="/client" class="waves-effect waves-light btn indigo darken-3 tooltipped center-align" data-tooltip="Back to index"
       style="width:80%;max-width:350px;margin-top:20px">หน้าหลัก</a>'
				]);
			}
		} else {
			//The first stamp

			//Check if late?
			if (date('G') >= config('ats.' . $userinfo->type . '-entrance-hour') && (date('i') >= config('ats.' . $userinfo->type . '-entrance-minute') || date('G') > config('ats.' . $userinfo->type . '-entrance-hour'))) {
				//Late, ask for explanation
				return view('clientlate', ['tm' => $tm, 'uid' => $userinfo->id]);
			} else {
				//Normal entrance, record to database
				if (DB::table('timestamp')->insert([
					'person' => $userinfo->id,
					'date' => date('o-m-d'),
					'entrance' => $tm
				])
				) {
					return view('clientsuccess', ['message' => $userinfo->name . ' เข้าโรงเรียนเมื่อเวลา ' . $tm]);
				} else {
					Log::error('DB: Error occured while clocking in user: '.$userinfo->id);
					return view('errors.custom', [
						'title' => 'Database Error',
						'description' => 'เกิดข้อผิดพลาดในการบันทึกเวลาเข้า กรุณาแจ้งผู้ดูแลระบบ',
						'button' => '<a href="/client" class="waves-effect waves-light btn indigo darken-3 tooltipped center-align" data-tooltip="Back to index"
       style="width:80%;max-width:350px;margin-top:20px">หน้าหลัก</a>'
					]);
				}
			}
		}
	}

	/*
	| TryLogIn()
	|
	| a method called when user submitted the login form
	|
	| @CallMethod Route
	| @Input [String]username, [String]password
	| @Output Redirection
	|
	*/
	public function TryLogIn(Request $request) {
		//$this->validate($request, ['username' => 'required', 'password' => 'string']);

		if (Auth::attempt(['username' => $request->input('username'), 'password' => $request->input('password')], $request->has('remember'))) {
			//dump(Auth::user());
			//return 'OK';

			// For some unknown reasons, auth::user() don't persist during session, so we temporary store explicitly in session ourselves.
			//$request->session()->put('userid', Auth::user()->username);
			//$request->session()->put('name', Auth::user()->name);

			return redirect('/')->with('notify', trans('messages.loginsuccess'));
		} else {
			//return 'BAD';
			return redirect('/')->with('notify', trans('messages.loginfail'));
		}
		/*$search = Adldap::getProvider('default')->search()->where('userprincipalname', '=', $request->input('username'))->get();
		dump ($search);
		$login = Adldap::getProvider('default')->auth()->attempt($request->input('username'), $request->input('password'));
		echo '<h4>Login:</h4>';
		dump($login);*/
	}

}
