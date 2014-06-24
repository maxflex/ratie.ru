<?php
	class NewsType extends Model
	{
	
		/*====================================== ПЕРЕМЕННЫЕ И КОНСТАНТЫ ======================================*/
		
		const NEW_VOTE		= 1;
		const VOTE_FOR		= 2;
		const VOTE_AGAINST	= 3;
		const CHANGE_FOR	= 4;
		const CHANGE_AGAINST= 5;
		const SUBSCRIBED	= 6;
		const UNSUBSCRIBED	= 7; 
		const COMMENT 		= 8;
		const NEW_VK_FRIEND	= 9;	// Новый друг из ВК появился на Ratie
		const NEW_MESSAGE	= 10;	// Новое сообщение в открытой беседе [НЕ ИСПОЛЬЗУЕТСЯ]
		const NEW_COMMENT	= 11;	// Прокомментировано мнение, оставленное ТОБОЙ
		
		public static $mysql_table	= "news_types";
		
		// Какие новости не показывать подписчикам
		public static $not_show_to_subscribers = array(self::NEW_COMMENT, self::NEW_VK_FRIEND);
		
		/*====================================== СИСТЕМНЫЕ ФУНКЦИИ ======================================*/
		
		
		
		/*====================================== СТАТИЧЕСКИЕ ФУНКЦИИ ======================================*/
		

				
		/*====================================== ФУНКЦИИ КЛАССА ======================================*/


	}