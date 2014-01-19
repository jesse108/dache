<?php
interface WeiXin_Handler{
	const MSG_TYPE_TEXT = 'text';
	const MSG_TYPE_IMAGE = 'image';
	const MSG_TYPE_VOICE = 'voice';
	const MSG_TYPE_VIDEO = 'video';
	const MSG_TYPE_LOCATION = 'location';
	const MSG_TYPE_LINK = 'link';
	const MSG_TYPE_EVENT = 'event';

	///////event
	const EVENT_TYPE_SUBSCRIBE = 'subscribe'; // 订阅
	const EVENT_TYPE_UNSUBSCRIBE = 'unsubscribe'; // 取消订阅
	const EVENT_TYPE_SCAN = 'scan'; //扫描二维码
	const EVENT_TYPE_LOCATION = 'LOCATION'; //微信自动上报地理位置
	const EVENT_TYPE_CLICK = 'CLICK'; //点击事件
	
	public function handleRquest($type,$content);
}