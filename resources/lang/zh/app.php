<?php

return [

    'name'              => 'Deployer',
    'signout'           => '注销',
    'toggle_nav'        => '切换导航',
    'dashboard'         => '仪表盘',
    'admin'             => '管理',
    'projects'          => '项目',
    'templates'         => '部署模板',
    'groups'            => '项目分组',
    'users'             => '用户',
    'created'           => '创建',
    'edit'              => '编辑',
    'confirm'           => '确认',
    'not_applicable'    => 'N/A',
    'date'              => '日期',
    'status'            => '状态',
    'details'           => '详情',
    'delete'            => '删除',
    'save'              => '保存',
    'close'             => '关闭',
    'never'             => '从未',
    'none'              => '无',
    'yes'               => '是',
    'no'                => '否',
    'warning'           => '警告',
    'socket_error'      => '服务器错误',
    'socket_error_info' => '无法建立 socket 连接到 <strong>' . config('deployer.socket_url') . '</strong>. ' .
                           '它需要用于报告部署的运行状态。 请重新加载网页，如果问题依旧，请联系系统管理员',
//    'not_down'          => '在运行此命令前，你必须切换到维护模式以确保没有新的部署运行',
//    'switch_down'       => '现在就切换到维护模式? 应用程序会在清理工作完成后自动切换回在线模式',
    'update_available'  => '有可用更新!',
    'outdated'          => '你正在运行过期的 release :current, 有一个新的 release <a href=":link" rel="noreferrer">:latest</a> 可用!',

];
