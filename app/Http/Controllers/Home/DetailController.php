<?php

namespace App\Http\Controllers\Home;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Contents;
use App\Model\Article;
use App\Model\User;


class DetailController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //获取Article的数据
      
        $res = Article::orderBy("id",'desc')->paginate(15); //15条每页浏览
         
        //dump($res);
        //返回试图并遍历数据
        return view("home.detail.index",['res'=>$res]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view("home.detail.create");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //获取传过来的字段信息
        $info = $request->only('userid','title','author','content');
     
        //获取得到session里储存的登录信息
        $list =\DB::table('user')->where('account',session("User"))->first();
        //将session里获取的id赋给传过来的userid;
        $info['userid'] = $list->id;
       // echo "<pre>";
       // print_r($info);
        //向contents表里添加数据并获取表的id
        $id = \DB::table("contents")->insertGetId($info);
        //实例Article表
        $data = new Article;
        //获取传过来的标题，作者字段
        $info2 = $request->only('title','author');
        //让contents的id等于$info2里的 cid,字段，userid字段等于session里的id字段
        $info2['cid'] = $id;

        $info2['userid'] =$list->id;
        //$info2的数据添加到 Article
        $data->insert($info2);
        //返回视图
       // home/detail
          return redirect('home/detail');
        //return view("home.detail.store");
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

        //return $id;
        //获取要修改的单条信息
        $list = Contents::where('id',$id)->find($id); //获取信息
        //print_r($list);die();
        //return $id;
        //var_dump($list);
      
        return view("home.detail.edit",["list"=>$list]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$id)
    {
        //dump( $id) ;die; 
        //获取修改的信息
        $input = $request->only('title','author','content');
        //$data =new Contents;
        $data =Contents::where('id',$id)->update($input);

        $info = $request->only('title','author');
        $content = Article::where('cid',$id)->update($info);


       // echo "<pre>";
       // var_dump($input);die();
       // if($idd){
        //    return 1;
        //}else{
          //  return 2;
        //}
        return redirect('home/detail');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        //print_r($id);
        //获取传过来的$id等于Article里的cid然后执行删除
        Article::where('cid',$id)->delete();
        //获取传过来的$id等于Contents里的id执行删除
        Contents::where('id',$id)->delete();
        //重定向至视图
        return redirect('home/detail');
    }
    //作为已通过文章
    public function through()
    {

        //echo 'aaaaa';
        $list =  Article::where('status',1)->get();
        //加载已通过文章页面
        return view('home.detail.through',['list'=>$list]);
    }

    public function nothrough()
    {   

        $list = Article::where('status',2)->get();
        //加载未通过文章页面
        return view('home.detail.nothrough',['list'=>$list]);
    }

    public function daithrough()
    {   
        
        $list = Article::where('status',3)->get();
        return view('home.detail.daithrough',['list'=>$list]);
    }
}
