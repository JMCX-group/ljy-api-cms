<?php

namespace App\Http\Controllers\Business;

use App\Http\Helper\EasyWeChat;
use App\Models\Cms\WxUser;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class WxUserController extends Controller
{
    public $page_level = "人员管理";

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        /**
         * 同步微信服务器端的数据：
         */
        $this->syncUsers();

        $wxUsers = WxUser::paginate(50);
        $page_title = "人员列表";
        $page_level = $this->page_level;

        return view('wx_users.index', compact('wxUsers', 'page_title', 'page_level'));
    }

    /**
     * 同步用户
     */
    public function syncUsers()
    {
        $users = EasyWeChat::getAllFans();

        foreach ($users as $user) {
            WxUser::updateOrCreate(['open_id' => $user['openid']], ['nickname' => $user['nickname'], 'head_img_url' => $user['headimgurl']]);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $wxUser = WxUser::find($id);
        $page_title = "编辑人员";
        $page_level = $this->page_level;

        return view('wx_users.edit', compact('wxUser', 'page_title', 'page_level'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $wxUser = WxUser::find($id);
        $wxUser->name = $request['name'];
        $wxUser->status = $request['status'];
        $wxUser->profession = ($request['status'] == '学生') ? $request['profession'] : '无';

        try {
            $wxUser->save();

            return redirect()->route('wx_users.index')->withSuccess('编辑人员成功');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(array('error' => $e->getMessage()))->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
