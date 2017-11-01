<?php
/**
 * Created by PhpStorm.
 * User: lyx
 * Date: 16/3/30
 * Time: 下午3:32
 */
?>

@extends('layouts.main')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">微信用户</h3>
                    <div class="box-tools pull-right">
                        <div class="input-group input-group-sm" style="width: 150px;">
                            <input type="text" name="table_search" class="form-control pull-right" placeholder="快速查询">
                            <div class="input-group-btn">
                                <button type="button" class="btn btn-default disabled">
                                    <i class="fa fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="box-body table-responsive no-padding">
                    <table class="table table-hover">
                        <tr>
                            <th>ID</th>
                            <th>头像</th>
                            <th>昵称</th>
                            <th>姓名</th>
                        </tr>
                        @forelse($wxUsers as $wxUser)
                            <tr>
                                <td>{{$wxUser->id}}</td>
                                <td><img src="{{$wxUser->head_img_url}}" style="width: 45px;"></td>
                                <td>{{$wxUser->nickname}}</td>
                                <td>{{$wxUser->name}}</td>
                                <td>
                                    <a class="btn btn-info" href="{{URL::to('people/'.$wxUser->id.'/edit')}}">
                                        编辑
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">暂无数据</td>
                            </tr>
                        @endforelse
                    </table>
                </div>

                @if($wxUsers->render() !== "")
                    <div class="box-footer">
                        {!! $wxUsers->render() !!}
                    </div>
                @endif
            </div>
        </div>
    </div>
@stop
@section('script')
    <script type="text/javascript">
        $('#defalutModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var url = button.data('url');
            var modal = $(this);

            modal.find('form').attr('action', url);
        })
    </script>
@stop