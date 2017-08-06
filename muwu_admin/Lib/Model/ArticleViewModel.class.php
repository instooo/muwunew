<?php

/***********************************************************

	@function article 视图模型



    @Filename ArticleViewModel.class.php $





*************************************************************/
class ArticleViewModel extends ViewModel 

{
	public $viewFields = array (
			
			'article' => array (
					'aid',
					'typeid',
					'title',
					'addtime',
					'updatetime',
					'istop',
					'hits',
					'status',
					'isshouye',
					'webconfig_id',
					'_type' => 'LEFT' 
			),
			
			'category' => array (
					'typename',
					'_on' => 'article.typeid=category.typeid' 
			) 
	)
	;
}

?>