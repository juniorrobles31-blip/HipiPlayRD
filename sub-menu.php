<li class="sub-menu">
	<a class="<?php
		if($page=="showtransusr"||$page=="results"){
			echo 'active';
		}
	?>" href="#">
		<i class="fa fa-exchange "></i>
		<span>Transacciones</span>
	</a>
	<ul class="sub">
		<li class="<?php echo ($page == "showtransusr" ? "active" : "")?>"><a href="?page=showtransusr">Ver Transacciones</a></li>
		<li class="<?php echo ($page == "results" ? "active" : "")?>"><a href="?page=results">Resultados</a></li>
	</ul>
</li>
<li class="sub-menu">
  <a  class="<?php echo ($page == "users" ? "active" : "")?>" href="?page=users" >
	  <i class="fa fa-user"></i>
	  <span>Usuarios</span>
  </a>
</li>
