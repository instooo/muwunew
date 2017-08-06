<?php
   /**
    * @name 文章视图 
    * 
    * @todo 连接文章类别表和文章表
    */
   class ArticleViewModel extends ViewModel
   {
   	   public $viewFields = array(

   	   		'category' => array('typeid'=>'category_id','typename'),
   	   		'article'  =>array('aid','title','typeid','content','titlecorlor','addtime','status','articledel','isshouye','redirect','from'=>'afrom','author','_on'=>'category.typeid=article.typeid')
   	   );
   }
?>