<?php

namespace Larafortp\Tests;

use Larafortp\MenuGenerate;
use Illuminate\Support\Facades\DB;

class MenuGenerateTest extends TestCase
{
    /*
     * 统一捕获异常
     */
    public function captureException($arrData,$message)
    {
        $this->expectExceptionObject(new \Exception($message));
        MenuGenerate::insertAll($arrData);
        $this->assertEquals(\Exception::class,$this->getExpectedException(),$message);
    }
    //下面用的是insertAll方法
    //菜单空异常
    public function testAddMenuException()
    {
        $data = array();
        $data[''] = array(
            array(
                'name'=>'index',
                'title'=>'首页轮播图',
                'sort' => 0,
                'controller'=>'Index1',
                'status'=>1,
            ),
        );
        //未设置键值异常
        $this->captureException($data,'insertMenu Null');
    }

    /**
     * 添加控制器//异常
     */
    public function testAddControllException()
    {
        //空白控制器
        $data = array();
        $data['首页'] = array(
            array(
                'name'=>'index',
                'title'=>'首页轮播图',
                'sort' => 0,
                'controller'=>'',
                'status'=>1,
            ),
        );
        $this->captureException($data,'控制器名为空请检查');
    }

    /*
     * 添加空白节点（方法）//异常
     */
    public function testAddNodeException()
    {
        //空白
        $data['添加'] = array(
            array(
            ),
        );
        $this->captureException($data,'数据为空');
    }
    //节点名空
    public function testAddNodeActionNmae()
    {
        $data = array();
        $data['首页'] = array(
            array(
                'name'=>'',
                'title'=>'首页轮播图',
                'sort' => 0,
                'controller'=>'IsControll',
                'status'=>1,
            ),
        );
        $this->captureException($data,'方法名名为空请检查');
    }
    //节点标题空异常
    public function testAddNodeActionTitle()
    {
        //标题
        $data = array();
        $data['首页'] = array(
            array(
                'name'=>'index',
                'title'=>'',
                'sort' => 0,
                'controller'=>'IsControll',
                'status'=>1,
            ),
        );
        $this->captureException($data,'标题为空请检查');
    }
   /**
    * 插入成功
    */
    public function testInsertSuccess()
    {
        $menuData = array(
            '测试模块'=>array(
                array(
                    'name'=>'index',
                    'title'=>'首页轮播图',
                    'sort' => 0,
                    'controller'=>'Index',
                    'status'=>1,
                ),
                array(
                    'name'=>'index',
                    'title'=>'新闻中心',
                    'sort' => 1,
                    'controller'=>'Index',
                    'status'=>1,
                ),
                array(
                    'name'=>'index',
                    'title'=>'集团机构',
                    'controller'=>'Index'
                ),
            ),
        );
        MenuGenerate::insertAll($menuData);
        //菜单
        $this->assertDatabaseHas('qs_menu', ['title' => '测试模块', 'type' => 'backend_menu', 'level' => 2]);
        //控制器
        $this->assertDatabaseHas('qs_node', ['name' => 'Index','title' => 'Index','pid'=>1, 'level' => 2]);
        //模块
        //这种方式插入，模块默认是admin
        //
        $this->assertDatabaseHas('qs_node', ['name' => 'index','title' =>'集团机构',  'level' => 3,'pid'=>MenuGenerate::$node_pid]);
    }
    /**
     * 认证认证菜单重复插入-》》同时认证插入相同或不同，等与不等
     */
    public function testInsertMenuRepeat()
    {
        // $menuData 和 $menuData2的值相同
        $menuData = array(
            '测试模块'=>array(
                array(
                    'name'=>'index',
                    'title'=>'首页轮播图',
                    'sort' => 0,
                    'controller'=>'Index',
                    'status'=>1,
                ),
                array(
                    'name'=>'index',
                    'title'=>'新闻中心',
                    'sort' => 1,
                    'controller'=>'Index',
                    'status'=>1,
                ),
                array(
                    'name'=>'index',
                    'title'=>'集团机构',
                    'controller'=>'Index'
                ),
            ),
        );
        $menuData2 = array(
            '测试模块'=>array(
                array(
                    'name'=>'index',
                    'title'=>'首页轮播图',
                    'controller'=>'Index',
                ),
                array(
                    'name'=>'index',
                    'title'=>'新闻中心',
                    'controller'=>'Index',
                ),
                array(
                    'name'=>'index',
                    'title'=>'集团机构',
                    'controller'=>'Index'
                ),
            ),
        );
        MenuGenerate::insertAll($menuData);
        $befor_menu = DB::table('qs_menu')->count();
        MenuGenerate::insertAll($menuData2);
        $after_menu = DB::table('qs_menu')->count();
        $this->assertEquals($befor_menu,$after_menu);
        //认证不等
        $menuData3 = array(
            '测试模块2'=>array(
                array(
                    'name'=>'index',
                    'title'=>'首页轮播图',
                    'controller'=>'Index',
                ),
                array(
                    'name'=>'index',
                    'title'=>'新闻中心',
                    'controller'=>'Index',
                ),
                array(
                    'name'=>'index',
                    'title'=>'集团机构',
                    'controller'=>'Index'
                ),
            ),
        );
        MenuGenerate::insertAll($menuData3);
        $after_menu = DB::table('qs_menu')->count();
        $this->assertNotEquals($befor_menu,$after_menu);
    }
    /**
     * 认证认证控制器重复插入-》》同时认证插入相同或不同，等与不等
     */
    public function testInsertControllRepeat()
    {
        // $menuData 和 $menuData2的值相同
        $menuData = array(
            '测试模块'=>array(
                array(
                    'name'=>'index',
                    'title'=>'首页轮播图',
                    'sort' => 0,
                    'controller'=>'Index',
                    'status'=>1,
                ),
                array(
                    'name'=>'index',
                    'title'=>'新闻中心',
                    'sort' => 1,
                    'controller'=>'Index',
                    'status'=>1,
                ),
                array(
                    'name'=>'index',
                    'title'=>'集团机构',
                    'controller'=>'Index'
                ),
            ),
        );
        $menuData2 = array(
            '测试模块'=>array(
                array(
                    'name'=>'index',
                    'title'=>'首页轮播图',
                    'controller'=>'Index',
                ),
                array(
                    'name'=>'index',
                    'title'=>'首页轮播图',
                    'controller'=>'Index',
                ),
                array(
                    'name'=>'index',
                    'title'=>'新闻中心',
                    'controller'=>'Index',
                ),
                array(
                    'name'=>'index',
                    'title'=>'集团机构',
                    'controller'=>'Index'
                ),
            ),
        );
        MenuGenerate::insertAll($menuData);
        $befor = DB::table('qs_node')->count();
        MenuGenerate::insertAll($menuData2);
        $after = DB::table('qs_node')->count();
        $this->assertEquals($befor,$after);
        //认证不等
        $menuData3 = array(
            '测试模块'=>array(
                array(
                    'name'=>'index',
                    'title'=>'首页轮播图',
                    'controller'=>'AddContorll',
                ),
            ),
        );
        MenuGenerate::insertAll($menuData3);
        $after = DB::table('qs_node')->count();
        $this->assertNotEquals($befor,$after);
    }
    /**
     * 认证认证方法重复插入-》》同时认证插入相同或不同，等与不等
     */
    public function testInsertNodeActionRepeat()
    {
        // $menuData 和 $menuData2的值相同
        $menuData = array(
            '测试模块'=>array(
                array(
                    'name'=>'index',
                    'title'=>'首页轮播图',
                    'sort' => 0,
                    'controller'=>'Index',
                    'status'=>1,
                ),
                array(
                    'name'=>'index',
                    'title'=>'新闻中心',
                    'sort' => 1,
                    'controller'=>'Index',
                    'status'=>1,
                ),
                array(
                    'name'=>'index',
                    'title'=>'集团机构',
                    'controller'=>'Index'
                ),
            ),
        );
        $menuData2 = array(
            '测试模块'=>array(
                array(
                    'name'=>'index',
                    'title'=>'首页轮播图',
                    'controller'=>'Index',
                ),
                array(
                    'name'=>'index',
                    'title'=>'首页轮播图',
                    'controller'=>'Index',
                ),
                array(
                    'name'=>'index',
                    'title'=>'新闻中心',
                    'controller'=>'Index',
                ),
                array(
                    'name'=>'index',
                    'title'=>'集团机构',
                    'controller'=>'Index'
                ),
            ),
        );
        MenuGenerate::insertAll($menuData);
        $befor = DB::table('qs_node')->count();
        MenuGenerate::insertAll($menuData2);
        $after = DB::table('qs_node')->count();
        $this->assertEquals($befor,$after);
        //认证不等
        $menuData3 = array(
            '测试模块'=>array(
                array(
                    'name'=>'index2',
                    'title'=>'首页轮播图',
                    'controller'=>'AddContorll',
                ),
            ),
        );
        MenuGenerate::insertAll($menuData3);
        $after = DB::table('qs_node')->count();
        $this->assertNotEquals($befor,$after);
    }
}