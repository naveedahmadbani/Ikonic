<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $users = self::getSuggestions(0);
        return view('home',compact('users'));
    }
    public function getSuggestions($skip)
    {
        $users=User::where('id',auth()->user()->id)->first();
        $ids_f[]=null;
        $ids_s_r[]=null;
        $ids_r[]=null;
        if($users->send_requests){
            $ids_s_r = json_decode($users->send_requests,true);
        }if($users->friends){
            $ids_f = json_decode($users->friends,true);
        }if($users->requests){
            $ids_r = json_decode($users->requests,true);
        }
        $ids = array_merge($ids_f,$ids_s_r,$ids_r);
        $ids[] = auth()->user()->id;
        foreach($ids as $key=>$id){
            if($ids[$key] == null){
                unset($ids[$key]);
            }
        }
        // dd(User::whereNotIn('id',$ids)->skip($skip)->take(10)->get());
        return User::whereNotIn('id',$ids)->skip($skip)->take(10)->get();
    }
    public function getData(Request $request)
    {
        if($request->type == 'suggestions')
        {
            $users = self::getSuggestions($request->skip);
        }else if($request->type == 'sent_requests')
        {
            $users=User::where('id',auth()->user()->id)->first();
            if($users->send_requests){
                $ids = json_decode($users->send_requests,true);
                $users=User::whereIn('id',$ids)->get();
            }else{
                $users = null;
            }
        }else if($request->type == 'received')
        {
            $users=User::where('id',auth()->user()->id)->first();
            if($users->requests){
                $ids = json_decode($users->requests,true);
                $users=User::whereIn('id',$ids)->get();
            }else{
                $users = null;
            }
        }else if($request->type == 'connections')
        {
            $users=User::where('id',auth()->user()->id)->first();
            if($users->friends){
                $ids = json_decode($users->friends,true);
                $users=User::whereIn('id',$ids)->get();
            }else{
                $users = null;
            }
        }
        $html = '';
                if($users){
                    foreach($users as $user){
                        $html.='<div class="my-2 shadow text-white bg-dark p-1" id="">
                                    <div class="d-flex justify-content-between">
                                        <table class="ms-1">
                                            <td class="align-middle">'.$user->name.'</td>
                                            <td class="align-middle"> - </td>
                                            <td class="align-middle">'.$user->email.'</td>
                                            <td class="align-middle">
                                        </table>
                                        <div>';
                                            if($request->type == 'suggestions'){
                                                $html.='<button id="create_request_btn_" class="btn btn-primary me-1" onclick="sendFrndRequest('.$user->id.')">Connect</button>';
                                            }else if($request->type == 'sent_requests'){
                                                $html.='<button id="cancel_request_btn_" class="btn btn-danger me-1" onclick="withdrawRequest('.$user->id.')">Withdraw Request</button>';
                                            }else if($request->type == 'received'){
                                                $html.='<button id="accept_request_btn_" class="btn btn-primary me-1" onclick="aceptFrndRequest('.$user->id.')">Accept</button>';
                                            }else if($request->type == 'connections'){
                                                $html.='<button style="width: 220px" id="get_connections_in_common_" class="btn btn-primary" type="button"
                                                data-bs-toggle="collapse" data-bs-target="#collapse_" aria-expanded="false" aria-controls="collapseExample"
                                                onclick="commonFrnd('.$user->id.')">Connections in common ()</button>
                                                <button id="create_request_btn_" class="btn btn-danger me-1" onclick="removeFriend('.$user->id.')">Remove Connection</button>';                                        
                                            }
                                        $html.='</div>
                                    </div>
                                </div>';
                    }
                    if($request->type == 'suggestions'){
                        $html.='<div class="d-flex justify-content-center w-100 py-2" id="load_more_connections_in_common_1">
                                    <button class="btn btn-sm btn-primary" id="load_more_connections_in_common_" onclick="loadMoreF(\''.trim($request->type).'\')">Load
                                    more</button>
                                </div>';
                    }
                }else{
                    return $html;
                }
        return $html;
    }
    public function acceptFrndRequest(Request $request)
    {
        $id = auth()->user()->id;
        $user = User::find($id);
        $ids = json_decode($user->requests,true);

        if (($key = array_search($request->user_id, $ids)) !== false) {
            self::addNewFriend($ids[$key],$id);
            unset($ids[$key]);
        }
        $user->requests = json_encode($ids,true);
        $user->save();
        
        // reverse friend Addition

        $id = auth()->user()->id;
        $user = User::find($request->user_id);
        $ids = json_decode($user->send_requests,true);
        if (($key = array_search($id, $ids)) !== false) {
            self::addNewFriend($ids[$key],$request->user_id);
            unset($ids[$key]);
        }
        $user->send_requests = json_encode($ids,true);
        $user->save();

        return true;
    }
    public function addNewFriend($frnd_id,$id)
    {
        $user = User::find($frnd_id);
        if($user->friends){
            $user_ids = json_decode($user->friends,true);
            $user_ids[] = $id;
            $user->friends = json_encode($user_ids,true);
        }else{
            $user_ids[] = $id;
            $user->friends = json_encode($user_ids,true);
        }
        $user->save();
    }
    public function sendFrndRequest(Request $request)
    {
        $id = auth()->user()->id;
        $user = User::find($id);
        if($user->send_requests){
            $user_ids = json_decode($user->send_requests,true);
            $user_ids[] = $request->user_id;
            $user->send_requests = json_encode($user_ids,true);
            self::requests($request->user_id,$id);
        }else{
            $user_ids[] = $request->user_id;
            $user->send_requests = json_encode($user_ids,true);
            self::requests($request->user_id,$id);
        }
        $user->save();
        return true;
    }
    public function removeRequest($user_id,$id)
    {
        $user_frnd = User::find($user_id);
        $ids = json_decode($user_frnd->requests,true);
        if (($key = array_search($id, $ids)) !== false) {
            unset($ids[$key]);
        }
        if(count($ids)>0){
            $ids = json_encode($ids,true);
        }else{
            $ids=null;
        }
        $user_frnd->requests = $ids;
        $user_frnd->save();
        return true;
    }
    public function requests($user_id,$id)
    {
        $user_frnd = User::find($user_id);
        if($user_frnd->requests){
            $user_ids_n = json_decode($user_frnd->requests,true);
            $user_ids_n[] = $id;
            $user_frnd->requests = json_encode($user_ids_n,true);

        }else{
            $user_ids_n[] = $id;
            $user_frnd->requests = json_encode($user_ids_n,true);
        }
        $user_frnd->save();
        return true;
    }
    public function withdrawFrndRequest(Request $request)
    {
        $user = User::find(auth()->user()->id);
        $ids = json_decode($user->send_requests,true);
        if (($key = array_search($request->user_id, $ids)) !== false) {
            self::removeRequest($request->user_id,$user->id);
            unset($ids[$key]);
        }
        $user->send_requests = json_encode($ids,true);
        $user->save();

        return true;
    }
    public function removeConnection(Request $request)
    {
        self::removeFriendConnection($request->user_id,auth()->user()->id);
        self::removeFriendConnection(auth()->user()->id,$request->user_id);
        return true;
    }
    public function removeFriendConnection($friend_id,$id)
    {
        $user = User::find($id);
        $ids = json_decode($user->friends,true);
        if (($key = array_search($friend_id, $ids)) !== false) {
            unset($ids[$key]);
        }
        $user->friends = json_encode($ids,true);
        $user->save();
    }
    public function commonFrnd(Request $request)
    {
        $user = User::find(auth()->user()->id);
        $friend = User::find($request->user_id);
        $user_frnd = json_decode($user->friends,true);
        $frnd_frnd = json_decode($friend->friends,true);
        $ids = array_intersect($user_frnd,$frnd_frnd);
        $users=User::whereIn('id',$ids)->get();
        $html='';
        if(count($users)>0){
            foreach($users as $user){
                $html.='<div class="my-2 shadow text-white bg-dark p-1" id="">
                            <div class="d-flex justify-content-between">
                                <table class="ms-1">
                                    <td class="align-middle">'.$user->name.'</td>
                                    <td class="align-middle"> - </td>
                                    <td class="align-middle">'.$user->email.'</td>
                                    <td class="align-middle">
                                </table>
                            <div>
                        </div>';
            }

        }else{
            $html.='<div class="my-2 shadow text-white bg-dark p-1" id="">
                        <div class="d-flex justify-content-between">
                            <table class="ms-1">
                                <td class="align-middle">No Common</td>
                                <td class="align-middle"> - </td>
                                <td class="align-middle">Friends Found</td>
                                <td class="align-middle">
                            </table>
                        <div>
                    </div>';
        }
        return $html;
    }
}
