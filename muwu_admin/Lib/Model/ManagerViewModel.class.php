<?php 
 
  class ManagerViewModel extends ViewModel{ 	
  		public $viewFields  = array(
  				'admin_role'=>array('rolename','disabled'),
  				'manager'=>array('uid','username','password','login_ip','os','login_time','login_count','status','email','fruit','_on'=>'admin_role.roleid=manager.roleid')			
  		);
  }
?>