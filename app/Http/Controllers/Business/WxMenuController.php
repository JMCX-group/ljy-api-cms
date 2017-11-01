<?php

namespace App\Http\Controllers\Business;

use App\Http\Helper\EasyWeChat;
use App\Models\cms\WxMenu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class WxMenuController extends Controller
{
    public $page_level = "系统管理";

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $wxMenus = WxMenu::find(1);
        if (isset($wxMenus->json)) {
            $data = json_decode($wxMenus->json, true);
        } else {
            /**
             * 没有就初始化参数：
             */
            $data = [
                'left_name' => '进入网站',
                'left_url' => 'http://m.lifejianji.com',
                'right_name' => '',
                'right_url' => '',
            ];
            $wxMenus->json = json_encode($data);
            $wxMenus->save();
        }

        $wxMenu = [
            'id' => $wxMenus->id,

            'left_name' => $data['left_name'],
            'left_url' => $data['left_url'],
            'right_name' => $data['right_name'],
            'right_url' => $data['right_url'],
        ];
        $wxMenu = (object)$wxMenu;
        $page_title = "菜单管理";
        $page_level = $this->page_level;

        return view('wx-menus.edit', compact('wxMenu', 'page_title', 'page_level'));
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
        //
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
        $leftName = $request['left-name'];
        $leftUrl = $request['left-url'];
        $rightName = $request['right-name'];
        $rightUrl = $request['right-url'];

        if ($rightName == '') {
            $buttons = [
                [
                    "type" => "view",
                    "name" => $leftName,
                    "url" => $leftUrl
                ]
            ];
        } else {
            $buttons = [
                [
                    "type" => "view",
                    "name" => $leftName,
                    "url" => $leftUrl
                ],
                [
                    "type" => "view",
                    "name" => $rightName,
                    "url" => $rightUrl
                ]
            ];
        }

        /**
         * Save data.
         */
        $data = [
            'left_name' => $leftName,
            'left_url' => $leftUrl,
            'right_name' => $rightName,
            'right_url' => $rightUrl
        ];

        $wxMenu = WxMenu::find($id);
        $wxMenu->json = json_encode($data);

        try {
            $wxMenu->save();
            EasyWeChat::setMenus($buttons);

            return redirect()->route('wx-menu.index')->withSuccess('更新成功');
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
